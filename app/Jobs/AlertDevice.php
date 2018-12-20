<?php

namespace App\Jobs;

use App\Http\Repositories\MailRepository;
use App\Models\Utils\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AlertDevice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mailRepository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MailRepository $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = time();
        $report_data = Device::get(['id','device_id', 'company_id', 'checked_at', 'guarantee'])
            ->filter(function ($device) use ($now){
                //todo  注意去掉中网的
                if($device->checked_at && $device->company_id != 1){
                    //todo 计算出质保期
                    $g = strtotime($device->checked_at) + $device->guarantee * 86400 * 365;
                    if($g > $now){
                        return false;
                    } else {
                        return true;
                    }
                }
                return false;
            })->map(function ($device){
            //todo 获得对应的负责人
            $raw = $device->company()->with('employees')->first();
            if(count($raw['employees']) != 0){
                $data = [
                    'phone' => $raw['employees'][0]['phone'],
                    'send' => [
                        'name' => $raw['employees'][0]['name'],
                        'device_name' => $raw['device_id'],
                        'problem_desc'=> "质保过期",
                        'four00tel' => env('FOUR00TEL')
                    ]
                ];
                return $data;
            }
        });

        foreach ($report_data as $report) {
            $this->mailRepository->sendReportMsg($report['phone'], $report['send']);
        }
        //todo 由于验收时要测试这个功能，那么就会几百条短信出去，不太好
        //todo 所以到时候展示代码就行了。
    }
}
