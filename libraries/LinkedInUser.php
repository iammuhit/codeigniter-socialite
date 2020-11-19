<?php

namespace App\Modules\Social\Libraries;

use League\OAuth2\Client\Provider\LinkedInResourceOwner;

class LinkedInUser extends LinkedInResourceOwner {

    public function getUserDetails() {
        $CI = & get_instance();
        $CI->load->helper('url');

        return [
            'uid' => $this->getId(),
            'nickname' => url_title($this->getFirstName() . '.' . $this->getLastName(), 'dash', true),
            'name' => $this->getFirstName() . ' ' . $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'location' => $this->getLocation(),
            'description' => $this->getSummary(),
            'image' => $this->getImageurl(),
            'urls' => ['LinkedIn' => $this->getUrl()]
        ];
    }

}
