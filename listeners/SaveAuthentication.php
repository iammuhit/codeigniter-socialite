<?php

namespace App\Modules\Social\Listeners;

use App\Modules\Users\Events\UserWasCreated;

class SaveAuthentication {

    /**
     * The session library
     * 
     * @var CodeIgniter/Libraries/Session/Session
     */
    protected $session;

    /**
     * The authentication repository
     * 
     * @var Social/Models/Authentication_m
     */
    protected $authentication;

    /**
     * Create a new SaveAuthentication instance
     */
    public function __construct() {
        $this->session = ci('load')->library('session')->session;
        $this->authentication = ci('load')->model('social/authentication_m')->authentication_m;
    }

    /**
     * Handle the event.
     *
     * @param UserWasCreated $event
     */
    public function handle(UserWasCreated $event) {
        // Let's get ready to interact with users
        $user = $event->getUser();
        $user_hash = $this->session->userdata('user_hash');
        $token = $this->session->userdata('token');

        // Remove the user_hash now that it's been set
        $this->session->unset_userdata('user_hash');
        $this->session->unset_userdata('token');
        
        // Attach this account to the logged in user
        $this->authentication->save([
            'user_id' => $user->id,
            'provider' => $token['provider'],
            'uid' => $user_hash['uid'],
            'access_token' => $token['access_token'],
            'secret' => isset($token['secret']) ? $token['secret'] : null,
            'expires' => isset($token['expires']) ? $token['expires'] : null,
            'refresh_token' => isset($token['refresh_token']) ? $token['refresh_token'] : null,
        ]);
    }

}
