<?php

namespace App\Providers;

use App\modules\AVURP\Repositories\VDPInfo\VDPInfoInterface;
use App\modules\AVURP\Repositories\VDPInfo\VDPInfoRepository;
use App\modules\HRM\Repositories\data\DataInterface;
use App\modules\HRM\Repositories\data\DataRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(
            VDPInfoInterface::class,
            VDPInfoRepository::class
        );
        $this->app->bind(
            DataInterface::class,
            DataRepository::class
        );
    }
}
