<?php

defined('BASEPATH') or exit('No direct script access allowed');

/* Link multiple providers */
$config['multiple_providers'] = true;
$config['providers'] = [
    'facebook' =>   ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\Facebook::class],
    'github' =>     ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\Github::class],
    'google' =>     ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\Google::class],
    'instagram' =>  ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\Instagram::class],
    'linkedin' =>   ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\LinkedIn::class],
    'dropbox' =>    ['strategy' => 'oauth2', 'class' => App\Modules\Social\Libraries\Providers\Dropbox::class],
    'twitter' =>    ['strategy' => 'oauth1', 'class' => App\Modules\Social\Libraries\Providers\Twitter::class]
];

/* End of file social.php */
/* Location: ./application/modules/social/config/social.php */