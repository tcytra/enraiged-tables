<?php

namespace Enraiged\Tables;

// use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\ServiceProvider;

class TablesServiceProvider extends ServiceProvider
{
    /**
     *  Bootstrap the event services.
     *
     *  @return void
     */
    public function boot()
    {
        $this->bootPublish();
    }

    /**
     *  Bootstrap the publish services.
     *
     *  @return void
     */
    protected function bootPublish(): void
    {
        $this->publishes(
            [__DIR__.'/../publish/config/enraiged' => config_path('enraiged')],
            ['enraiged', 'enraiged-core', 'enraiged-core-config'],
        );
    }
}
