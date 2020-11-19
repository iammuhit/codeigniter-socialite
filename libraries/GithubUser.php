<?php

namespace App\Modules\Social\Libraries;

use League\OAuth2\Client\Provider\GithubResourceOwner;

class GithubUser extends GithubResourceOwner {

    public function getUserDetails() {
        $CI = & get_instance();
        $CI->load->helper('url');

        return [
            'uid' => $this->getId(),
            'nickname' => url_title($this->getNickname(), 'dash', true),
            'name' => $this->getName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'location' => $this->getLocation(),
            'description' => $this->getBio(),
            'image' => $this->getAvatarUrl(),
            'urls' => [
                'Github' => $this->getUrl(),
                'Blog' => $this->getBlog()
            ]
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

    /**
     * Get resource owner blog
     *
     * @return string|null
     */
    public function getBlog() {
        return $this->getField('blog');
    }

    /**
     * Get resource owner Location
     *
     * @return string|null
     */
    public function getLocation() {
        return $this->getField('location');
    }

    /**
     * Get resource owner avatar url
     *
     * @return string|null
     */
    public function getAvatarUrl() {
        return $this->getField('avatar_url');
    }

    /**
     * Get resource owner bio
     *
     * @return string|null
     */
    public function getBio() {
        return $this->getField('bio');
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key) {
        return isset($this->response[$key]) ? $this->response[$key] : null;
    }

}
