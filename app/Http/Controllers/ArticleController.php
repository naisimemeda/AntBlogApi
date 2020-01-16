<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $page     = $request->input('page', 1);
        $pageSize = 16;

        $params = [
            'index' => 'articles',
            'type'  => '_doc',
            'body'  => [
                'from'  => ($page - 1) * $pageSize, // 通过当前页数与每页数量计算偏移值
                'size'  => $pageSize,
                'query' => [
                    'bool' => [
                    ],
                ],
            ],
        ];

        if ($search = $request->input('search', '')) {
            // 将搜索词根据空格拆分成数组，并过滤掉空项
            $keywords = array_filter(explode(' ', $search));

            $params['body']['query']['bool']['must'] = [];
            // 遍历搜索词数组，分别添加到 must 查询中
            foreach ($keywords as $keyword) {
                $params['body']['query']['bool']['must'][] = [
                    'multi_match' => [
                        'query'  => $keyword,
                        'fields' => [
                            'title^2',
                            'body^2',
                            'tags_value^1',
                        ],
                    ],
                ];
            }
        }

        if ($request->input('category_id') && $category = ArticleCategory::find($request->input('category_id'))) {
               $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
        }

        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $params['body']['sort'] = [[$m[1] => $m[2]]];
                }
            }
        }

        $result = app('es')->search($params);

        $articleIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $article = Article::query()->whereIn('id', $articleIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $articleIds)))->get();
    }
}
