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
                        'filter' => [
                            ['term' => ['hot' => 0]],
                        ],
                    ],
                ],
            ],
        ];



        if ($filterString = $request->input('filters')) {
            $params['body']['query']['bool']['filter'][] = [
                'nested' => [
                    'path'  => 'tags',
                    'query' => [
                        ['term' => ['tags.name' => $filterString]],
                    ],
                ],
            ];
        }

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
                            'title^3',
                            'body^2',
                        ],
                    ],
                ];
            }
        }

        if ($request->input('category_id') && $category = ArticleCategory::find($request->input('category_id'))) {
               $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
        }

        $result = app('es')->search($params);
        $properties = collect($result['aggregations']['properties']['properties']['buckets'])
            ->map(function ($bucket) {
                // 通过 map 方法取出我们需要的字段
                return [
                    'key'    => $bucket['key'],
                ];
            });
        $articleIds = collect($result['hits']['hits'])->pluck('_id')->all();
        $article = Article::query()->whereIn('id', $articleIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $articleIds)))->get();

        return $this->success(compact('article', 'properties'));
    }
}
