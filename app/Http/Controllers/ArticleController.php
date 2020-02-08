<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Jobs\SyncOneArticleToES;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\SearchBuilders\ArticleSearchBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * 文章列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 16;

        $builder = (new ArticleSearchBuilder())->onSale()->paginate($perPage, $page);

        if ($request->input('category_id') && $category = ArticleCategory::find($request->input('category_id'))) {
            // 调用查询构造器的类目筛选
            $builder->category($category);
        }

        if ($search = $request->input('search', '')) {
            $keywords = array_filter(explode(' ', $search));
            // 调用查询构造器的关键词筛选
            $builder->keywords($keywords);
        }

        if ($search || isset($category)) {
            // 调用查询构造器的分面搜索
            $builder->aggregateProperties();
        }

        if ($filterString = $request->input('filters')) {
            $builder->propertyFilter($filterString);
        }

        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['id',])) {
                    // 调用查询构造器的排序
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }


        $result = app('es')->search($builder->getParams());
        $properties = [];
        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function ($bucket) {
                    // 通过 map 方法取出我们需要的字段
                    return [
                        'key' => $bucket['key'],
                    ];
                });
        }
        $articleIds = collect($result['hits']['hits'])->pluck('_id')->all();
        $article = Article::with(['user'])->whereIn('id', $articleIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $articleIds)))->get();

        return $this->success(compact('article', 'properties'));
    }

    public function store(ArticleRequest $request)
    {
        $category = ArticleCategory::query()->find($request->get('category_id'));

        $data = array_merge($request->only(['title', 'body']), ['status' => true, 'user_id' => Auth::id()]);

        $article = $category->article()->create($data);

        $tags = $request->get('tags');

        collect($tags)->map(function ($value) use ($article) {
            ArticleTag::query()->create([
                    'name' => $value,
                    'article_id' => $article->id
                ]
            );
        });

        $this->dispatch(new SyncOneArticleToES($article));

        return $this->success('成功');
    }

    public function update(ArticleRequest $request, Article $article)
    {

        $this->authorize('own', $article);

        $article->update($request->only(['title', 'body', 'status', 'category_id']));

        $tags = $request->get('tags');


        collect($tags)->map(function ($value) use ($article) {
            ArticleTag::query()->create([
                    'name' => $value,
                    'article_id' => $article->id
                ]
            );
        });

        $this->dispatch(new SyncOneArticleToES($article));

        return $this->success($article);
    }
}
