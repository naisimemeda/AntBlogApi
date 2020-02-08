<?php

namespace App\SearchBuilders;


use App\Models\ArticleCategory;

class ArticleSearchBuilder
{
    protected $params = [
        'index' => 'articles',
        'type'  => '_doc',
        'body'  => [
            'query' => [
                'bool' => [
                    'filter' => [],
                    'must'   => [],
                ]
            ]
        ]
    ];

    public function onSale()
    {
        $this->params['body']['query']['bool']['filter'][] = ['term' => ['on_sale' => true]];

        return $this;
    }

    public function paginate($size, $page)
    {
        $this->params['body']['from'] = ($page - 1) * $size;
        $this->params['body']['size'] = $size;

        return $this;
    }

    public function category(ArticleCategory $category)
    {
       $this->params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
        return $this;
    }

    public function aggregateProperties()
    {
        $this->params['body']['aggs'] = [
            'properties' => [
                'nested' => [
                    'path' => 'tags',
                ],
                'aggs'   => [
                    'properties' => [
                        'terms' => [
                            'field' => 'tags.name',
                        ],
                    ],
                ],
            ],
        ];

        return $this;
    }

    // 添加搜索词
    public function keywords($keywords)
    {
        // 如果参数不是数组则转为数组
        $keywords = is_array($keywords) ? $keywords : [$keywords];
        foreach ($keywords as $keyword) {
            $this->params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query'  => $keyword,
                    'fields' => [
                        'title^3',
                        'body^2',
                        'tags_value^2',
                    ],
                ],
            ];
        }
        return $this;
    }

    public function propertyFilter($name)
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'nested' => [
                'path'  => 'tags',
                'query' => [
                    ['term' => ['tags.name' => $name]],
                ],
            ],
        ];

        return $this;
    }

    public function orderBy($field, $direction)
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];

        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }
}
