<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $kernal = $app->make(Kernel::class);
        $kernal->bootstrap();

        $kernal->call('migrate --env=testing');
        if (\App\User::count() == 0 && \App\Group::count() == 1)
            $kernal->call('db:seed --env=testing');

        return $app;
    }
}