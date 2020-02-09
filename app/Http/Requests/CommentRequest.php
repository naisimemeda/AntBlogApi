<?php

namespace App\Http\Requests;

class CommentRequest extends ApiBaseRequest
{
    public function rules()
    {
        switch($this->method()) {
            case 'POST':
                return [
                    'content' => 'required|string',
                    'article_id' => 'required|exists:articles,id',
                    'parent_id' => 'exists:comments,id',
                    'image' => 'string',
                ];
                break;
            case 'PATCH':
                return [
                    'content' => 'string',
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'body.required' => '请输入评论内容',
            'article_id.required' => '请选择要评论的文章',
            'article_id.exists' => '文章不存在'
        ];
    }
}
