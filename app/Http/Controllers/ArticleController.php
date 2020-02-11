<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Jobs\SyncOneArticleToES;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Comment;
use App\SearchBuilders\ArticleSearchBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * 文章列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $page    = $request->input('page', 1);
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
            ->select('id', 'title', 'body', 'user_id', 'reply_count', 'view_count', 'hot', 'created_at', DB::raw('1 as diff'))
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $articleIds)))->paginate(16);

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

        $this->dispatch(new SyncOneArticleToES($article));

        return $this->success($article);
    }

    /**
     * 文章详情
     * @param Article $article
     * @param Request $request
     * @return mixed
     */
    public function show(Article $article, Request $request)
    {
        $article->load(['user']);
        $user_id = 0;
        if (isset(auth('api')->user()->id)) {
            $user_id = auth('api')->user()->id;
        }
        $like = $article->like()->where('user_id', $user_id)->exists();

        $comment = Comment::with(['user', 'children' => function($query) {
            $query->with(['user', 'like'])->select('id', 'user_id', 'content', 'image', 'parent_id', 'updated_at');
        }, 'like'])->where('article_id', $article->id)->whereNull('parent_id')
            ->select('id', 'user_id', 'content', 'image', 'updated_at')->paginate(16);
        $article->increment('view_count');
        return $this->success(compact('article', 'like', 'comment'));
    }


    /**
     * 收藏文章
     * @param Article $article
     * @param Request $request
     * @return mixed
     */
    public function favor(Article $article, Request $request)
    {
        $user = Auth::user();

        if ($user->favoriteArticles()->find($article->id)) {
            return $this->success('重复收藏');
        }

        $user->favoriteArticles()->attach($article);
        return $this->success('成功');
    }

    /**
     * 取消收藏
     * @param Article $article
     * @param Request $request
     * @return mixed
     */
    public function disfavor(Article $article, Request $request)
    {
        $user = Auth::user();
        $user->favoriteArticles()->detach($article);
        return $this->success('成功');
    }

    /**
     * 收藏列表
     * @param Request $request
     * @return mixed
     */
    public function favorites(Request $request)
    {
        $user_id = Auth::id();

        $article_id = DB::table('user_favorite_article')->where('user_id', $user_id)->pluck('article_id');

        $article = Article::with(['user:id'])->whereIn('id', $article_id)->select('id', 'user_id')->paginate(16);

        return $this->success(compact('article'));
    }

    /**
     * 文章点赞
     * @param Article $article
     * @param Request $request
     * @return mixed
     */
    public function articleLike(Article $article, Request $request)
    {
        $user = Auth::user();
        if ($article->like()->where('user_id', 12)->exists()) {
            return $this->success('重复收藏');
        }

        $article->like()->create(['user_id' => $user->id]);
        return $this->success('成功');

    }

    /**
     * 取消点赞
     * @param Article $article
     * @param Request $request
     * @return mixed
     */
    public function dislike(Article $article, Request $request)
    {
        $article->like()->where('user_id', Auth::id())->delete();

        return $this->success('成功');
    }
}
