<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\GoogleUser;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use League\OAuth2\Client\Token\AccessToken;

class Google extends GoogleProvider {

    public $name = 'google';

    protected function createResourceOwner(array $response, AccessToken $token) {
        $user = new GoogleUser($response);

        // Validate hosted domain incase the user edited the initial authorization code grant request
        if ($this->hostedDomain === '*') {
            if (empty($user->getHostedDomain())) {
                throw HostedDomainException::notMatchingDomain($this->hostedDomain);
            }
        } elseif (!empty($this->hostedDomain) && $this->hostedDomain !== $user->getHostedDomain()) {
            throw HostedDomainException::notMatchingDomain($this->hostedDomain);
        }

        return $user;
    }

}

/* End of file Google.php */
/* Location: ./application/modules/social/providers/Google.php */