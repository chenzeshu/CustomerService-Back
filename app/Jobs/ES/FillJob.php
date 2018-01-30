<?php

namespace App\Jobs\ES;

use App\Models\Company;
use App\User;
use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FillJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //todo 公司
        $data = Company::all()->toArray();
        $client = ClientBuilder::create()->build();

        //填充数据
        for($i = 0; $i < count($data); $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'cs',
                    '_type' => 'company',
                    '_id' => $i+1
                ]
            ];

            $params['body'][] = [
                'id' => $data[$i]['id'],
                'name' =>$data[$i]['name'],
                'address' => $data[$i]['address'],
                'profession' => $data[$i]['profession'],
                'type' => $data[$i]['type'],
            ];
        }
        $client->bulk($params);

        //todo 解除job
        $this->delete();
    }
}
