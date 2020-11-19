<?php

namespace App\Modules\Social\Libraries;

use League\OAuth2\Client\Provider\GoogleUser as GoogleResourceOwner;

class GoogleUser extends GoogleResourceOwner {

    public function getUserDetails() {
        $CI = & get_instance();
        $CI->load->helper('url');

        return [
            'uid' => $this->getId(),
            'nickname' => url_title($this->getFirstName() . '.' . $this->getLastName(), 'dash', true),
            'name' => $this->getName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'location' => NULL,
            'description' => NULL,
            'image' => $this->getAvatar(),
            'urls' => ['Google' => NULL]
        ];
    }

}
