<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\User;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Twitter as TwitterProvider;

class Twitter extends TwitterProvider {

    public $name = 'twitter';

    public function userDetails($data, TokenCredentials $tokenCredentials) {
        $user = new User;
        $user_details = parent::userDetails($data, $tokenCredentials);

        foreach ($user_details as $property => $value) {
            if (property_exists($user, $property)) {
                $user->{$property} = $value;
            }
        }

        $name_parts = explode(' ', $user->name);

        $user->last_name = (count($name_parts) > 1) ? array_pop($name_parts) : null;
        $user->first_name = implode(' ', $name_parts);

        return $user;
    }

}

/* End of file Twitter.php */
/* Location: ./application/modules/social/providers/Twitter.php */