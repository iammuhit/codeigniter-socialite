<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\LinkedInUser;
use League\OAuth2\Client\Provider\LinkedIn as LinkedInProvider;
use League\OAuth2\Client\Token\AccessToken;

class LinkedIn extends LinkedInProvider {

    public $name = 'linkedin';

    protected function createResourceOwner(array $response, AccessToken $token) {
        return new LinkedInUser($response);
    }

}

/* End of file LinkedIn.php */
/* Location: ./application/modules/social/providers/LinkedIn.php */