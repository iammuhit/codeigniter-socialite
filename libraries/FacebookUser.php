<?php

namespace App\Modules\Social\Libraries;

use League\OAuth2\Client\Provider\FacebookUser as FacebookResourceOwner;

class FacebookUser extends FacebookResourceOwner {

    public function getUserDetails() {
        $user = new User;

        $user->uid = $this->getId();
        $user->nickname = $this->getName();
        $user->name = $this->getName();
        $user->first_name = $this->getFirstName();
        $user->last_name = $this->getLastName();
        $user->email = $this->getEmail();
        $user->location = $this->getHometown();
        $user->description = $this->getBio();
        $user->imageUrl = $this->getPictureUrl();
        $user->urls = ['Facebook' => $this->getLink()];

        $used = ['id', 'name', 'first_name', 'last_name', 'email', 'hometown', 'bio', 'picture_url', 'link', 'picture'];

        // Save all extra data
        $user->extra = array_diff_key($this->data, array_flip($used));

        return $user;
    }

}
