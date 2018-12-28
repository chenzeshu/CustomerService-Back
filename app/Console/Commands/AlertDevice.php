<?php

namespace App\Console\Commands;

use App\Models\Utils\Allow;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设备质保报警';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(Allow::findOrFail(1)->allow_report == 1){
            \App\Jobs\AlertDevice::dispatch();
            $time = date('Y-m-d H:i:s');
            Log::info($time." : 检查设备质保过期");
        }
    }
}
