<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 14:35
 */

namespace App\Observers;


use Elasticsearch\ClientBuilder;

class CompanyOb
{
    /**
     * 监听创建/更新
     */
    public function saved($model)
    {
        $client = ClientBuilder::create()->build();
        $params = [
            'index' => 'cs',
            'type' => 'company',
            'body' => [
                'id' => $model->id,
                'name' => $model->name,
                'address' => $model->address,
                'profession' => $model->profession,
                'type' => $model->type,
            ]
        ];
        $client->index($params);
    }

    /**
     * 监听删除
     */
    public function deleted($model)
    {
        $client = ClientBuilder::create()->build();
        $params = [
            'index' => 'cs',
            'type' => 'company',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'id' => $model->id
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $client->deleteByQuery($params);
    }
}