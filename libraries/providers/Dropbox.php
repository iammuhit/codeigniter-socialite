<?php

namespace App\Modules\Social\Libraries\Providers;

use App\Modules\Social\Libraries\DropboxUser;
use Stevenmaguire\OAuth2\Client\Provider\Dropbox as DropboxProvider;
use League\OAuth2\Client\Token\AccessToken;

class Dropbox extends DropboxProvider {

    public $name = 'dropbox';

    protected function createResourceOwner(array $response, AccessToken $token) {
        return new DropboxUser($response);
    }

}

/* End of file Dropbox.php */
/* Location: ./application/modules/social/providers/Dropbox.php */