<?php

namespace App\Modules\Social\Libraries;

use Stevenmaguire\OAuth2\Client\Provider\DropboxResourceOwner;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DropboxUser extends DropboxResourceOwner {

    use ArrayAccessorTrait;

    public function getUserDetails() {
        $user = new User;

        $user->uid = $this->getId();
        $user->nickname = $this->getName();
        $user->name = $this->getName();
        $user->first_name = $this->getFirstName();
        $user->last_name = $this->getLastName();
        $user->email = $this->getEmail();
        $user->location = NULL;
        $user->description = NULL;
        $user->imageUrl = NULL;
        $user->urls = ['Dropbox' => NULL];

        $used = ['account_id', 'name', 'email'];

        // Save all extra data
        $user->extra = array_diff_key($this->data, array_flip($used));

        return $user;
    }

    public function getFirstName() {
        return $this->getField('name.given_name');
    }

    public function getLastName() {
        return $this->getField('name.surname');
    }

    public function getEmail() {
        return $this->getField('email');
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key) {
        return $this->getValueByKey($this->response, $key);
    }

}
