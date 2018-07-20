<?php

namespace App\Console\Commands;

use App\Jobs\ES\FillJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EsCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        FillJob::dispatch();
        $time = date('Y-m-d H:i:s');
        Log::useDailyLog(storage_path('logs/job.log'));
        Log::info($time . ': 缓存ES:company');
    }
}
