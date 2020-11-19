<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\GithubUser;
use League\OAuth2\Client\Provider\Github as GithubProvider;
use League\OAuth2\Client\Token\AccessToken;

class Github extends GithubProvider {

    public $name = 'github';

    protected function createResourceOwner(array $response, AccessToken $token) {
        if (empty($response['email'])) {
            $request = $this->getAuthenticatedRequest('GET', $this->apiDomain . '/user/emails', $token);
            $user_emails = $this->getParsedResponse($request);
            $user_email = array_shift($user_emails);

            $response['email'] = $user_email['email'];
        }

        $user = new GithubUser($response);

        return $user->setDomain($this->domain);
    }

}

/* End of file Github.php */
/* Location: ./application/modules/social/providers/Github.php */