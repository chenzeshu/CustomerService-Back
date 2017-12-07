<?php

namespace App\Console\Commands;

use App\Jobs\Cache\Utils;
use Illuminate\Console\Command;

class CacheUtils extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zcache:utils';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '中网客服平台:工具表一周缓存更新';

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
        Utils::dispatch("all");
    }
}
