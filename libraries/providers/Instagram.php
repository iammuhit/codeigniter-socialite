<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\InstagramUser;
use League\OAuth2\Client\Provider\Instagram as InstagramProvider;
use League\OAuth2\Client\Token\AccessToken;

class Instagram extends InstagramProvider {

    public $name = 'instagram';

    protected function createResourceOwner(array $response, AccessToken $token) {
        return new InstagramUser($response);
    }

}

/* End of file Instagram.php */
/* Location: ./application/modules/social/providers/Instagram.php */