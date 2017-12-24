<?php

namespace App\Providers;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_apply;
use App\Models\Contract;
use App\Models\Contractc;
use App\Observers\ApplyObserver;
use App\Observers\ChannelObserver;
use App\Observers\ContractcObserver;
use App\Observers\ContractObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Contract::observe(ContractObserver::class);
        Contractc::observe(ContractcObserver::class);
        Channel::observe(ChannelObserver::class);
        Channel_apply::observe(ApplyObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
