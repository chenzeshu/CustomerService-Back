<?php

namespace App\Jobs;

use App\Http\Repositories\MailRepository;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class reportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $emps;
    private $data;
    private $mailRepository;
    /**
     * Create a new job instance.
     * $emps: Employee::findOrFail(ids);
     * $data: Array
     * @return void
     */
    public function __construct($emps, $data)
    {
        $this->emps = $emps;
        $this->data = $data;
        $this->mailRepository = new MailRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->emps as $emp){
            $this->mailRepository->sendReportMsg(
                    (int)$emp->phone,
                    array_merge([
                        "name" => $emp->name,
                    ],
                        $this->data)
                );
        }
        //todo 解除job
        $this->delete();
    }
}
