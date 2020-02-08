<?php

namespace App\Http\Requests;

class ArticleRequest extends ApiBaseRequest
{
    public function rules()
    {
        switch($this->method()) {
            case 'POST':
                return [
                    'title' => 'required|string|max:15',
                    'body' => 'required|string',
                    'category_id' => 'required|integer|exists:article_categories,id',
                ];
                break;
            case 'PATCH':
                return [
                    'article_id' => 'required|exists:articles,id',
                    'title' => 'string',
                    'body' => 'string',
                    'status' => 'integer|in:0,1',
                    'category_id' => 'exists:article_categories,id',
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'body.required' => '请填写文章内容',
            'category_id.required' => '请选择文章分类',
            'article_id.required' => '请选择要修改的文章',
            'article_id.exists' => '文章不存在',
        ];
    }
}
