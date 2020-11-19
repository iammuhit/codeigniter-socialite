<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\FacebookUser;
use League\OAuth2\Client\Provider\Facebook as FacebookProvider;
use League\OAuth2\Client\Token\AccessToken;

class Facebook extends FacebookProvider {

    public $name = 'facebook';

    const METHOD_DELETE = 'DELETE';

    public function __construct($options = [], array $collaborators = []) {
        if (empty($options['graphApiVersion'])) {
            $options['graphApiVersion'] = 'v3.1';
        }

        parent::__construct($options, $collaborators);
    }

    protected function createResourceOwner(array $response, AccessToken $token) {
        return new FacebookUser($response);
    }

    public function revoke($uid) {
        $request = $this->getAuthenticatedRequest(self::METHOD_DELETE, '/' . $uid . '/permissions', $this->getAccessToken());
    }

}

/* End of file Facebook.php */
/* Location: ./application/modules/social/providers/Facebook.php */