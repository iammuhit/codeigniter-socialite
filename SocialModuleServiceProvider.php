<?php

namespace App\Modules\Social;

use App\Providers\ModuleServiceProvider;

class SocialModuleServiceProvider extends ModuleServiceProvider {

    /**
     * The addon event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'App\Modules\Users\Events\UserWasCreated' => [
            'App\Modules\Social\Listeners\SaveAuthentication'
        ],
    ];

    /**
     * The class bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The singleton bindings.
     *
     * @var array
     */
    protected $singletons = [];

}
