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
    protected $signature = 'zcache:utils {module}';

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
        switch ($this->argument('module')){
            case "service_types":
                Utils::dispatch("service_types");
                break;
            case "service_sources":
                Utils::dispatch("service_sources");
                break;
            case "coors":
                Utils::dispatch("coors");
                break;
            case "contract_types":
                Utils::dispatch("contract_types");
                break;
            case "info1":
                Utils::dispatch("info1");
                break;
            case "info2":
                Utils::dispatch("info2");
                break;
            case "info3":
                Utils::dispatch("info3");
                break;
            case "info5":
                Utils::dispatch("info5");
                break;
            case "plans":
                Utils::dispatch("plans");
                break;
            case "pros":
                Utils::dispatch("pros");
                break;
            default:
                Utils::dispatch();
                break;
        }

    }
}
