<?php

namespace App\Modules\Social\Libraries;

use League\OAuth2\Client\Provider\InstagramResourceOwner;

class InstagramUser extends InstagramResourceOwner {

    const BASE_INSTAGRAM_URL = 'https://www.instagram.com/';

    public function getUserDetails() {
        $CI = & get_instance();
        $CI->load->helper('url');

        return [
            'uid' => $this->getId(),
            'nickname' => url_title($this->getNickname(), 'dash', true),
            'name' => $this->getName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => null,
            'location' => null,
            'description' => $this->getDescription(),
            'image' => $this->getImageurl(),
            'urls' => ['Instagram' => $this->getUrl()]
        ];
    }

    /**
     * Returns the first name for the user as a string if present.
     *
     * @return string|null
     */
    public function getFirstName() {
        $name = $this->getName();
        $name_parts = explode(' ', $name);
        $last_name = (count($name_parts) > 1) ? array_pop($name_parts) : null;
        $first_name = implode(' ', $name_parts);

        return $first_name;
    }

    /**
     * Returns the last name for the user as a string if present.
     *
     * @return string|null
     */
    public function getLastName() {
        $name = $this->getName();
        $name_parts = explode(' ', $name);
        $last_name = (count($name_parts) > 1) ? array_pop($name_parts) : null;

        return $last_name;
    }

    public function getUrl() {
        return self::BASE_INSTAGRAM_URL . $this->getNickname();
    }

}
