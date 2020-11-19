<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\Core\Http\Controller\PublicController;
use App\Modules\Users\Events\UserWasCreated;
use Illuminate\Support\Facades\Event;

class Social extends PublicController {

    protected $providers = [];

    public function __construct() {
        parent::__construct();

        $this->load->language('social');
        $this->load->config('social', true);
        $this->load->model(['authentication_m', 'credential_m']);

        $this->providers = $this->config->item('providers', 'social');
    }

    public function _remap($method, $args) {
        if ($method == 'linked') {
            $this->linked();
            return;
        }

        // Invalid method or no provider = BOOM
        if (!in_array($method, ['session', 'callback', 'revoke']) or empty($args)) {
            show_404();
        }

        // Get the provider (facebook, twitter, etc)
        list($provider) = $args;

        // This provider is not supported by the module
        array_key_exists($provider, $this->providers) or show_404();

        // Look to see if we have this provider in the db?
        if (!($credentials = $this->credential_m->get_active_provider($provider))) {
            $this->ion_auth->is_admin() ? show_error('Social Integration: ' . $provider . ' is not supported, or not enabled.') : show_404();
        }

        // Create the URL to return the user to
        $callback = site_url('social/callback/' . $provider);
        $strategy = element('strategy', $this->providers[$provider]);

        switch ($strategy) {
            case 'oauth1':
                $consumer = NULL;
                $options = [
                    'identifier' => $credentials->client_key,
                    'secret' => $credentials->client_secret,
                    'callback_uri' => $callback
                ];

                break;

            case 'oauth2':
                $consumer = ['scope' => explode(',', $credentials->scope)];
                $options = [
                    'clientId' => $credentials->client_key,
                    'clientSecret' => $credentials->client_secret,
                    'redirectUri' => $callback
                ];

                break;

            default:
                show_error('Something went properly wrong!');
        }

        $reflection = new ReflectionClass($this->providers[$provider]['class']);
        $provider = $reflection->newInstance($options);

        // Call session or callback, with lots of handy details
        call_user_func([$this, '_' . $method], $strategy, $provider, $consumer);
    }

    // Build the session and redirect to provider
    private function _session($strategy, $provider, $consumer) {

        switch ($strategy) {
            case 'oauth1':
                $temporaryCredentials = $provider->getTemporaryCredentials();
                $authorization_url = $provider->getAuthorizationUrl($temporaryCredentials);

                // Store the credentials in the session.
                $this->session->set_userdata('temporary_credentials', serialize($temporaryCredentials));

                break;

            case 'oauth2':
                $authorization_url = $provider->getAuthorizationUrl(['scope' => $consumer['scope']]);

                // Get the state generated for you and store it to the session.
                $this->session->set_userdata('oauth_state', $provider->getState());

                break;
        }

        // Redirect the user to the authorization URL.
        redirect($authorization_url);
    }

    // We've got back from the provider, so get smart and save stuff
    private function _callback($strategy, $provider, $consumer) {
        $tokenCredentials = new stdClass;

        switch ($strategy) {
            case 'oauth1':
                // Retrieve the temporary credentials from _session method
                $temporaryCredentials = unserialize($this->session->userdata('temporary_credentials'));
                $oauth_token = $this->input->get('oauth_token');
                $oauth_verifier = $this->input->get('oauth_verifier');

                $this->session->unset_userdata('temporary_credentials');

                if (!($oauth_token or $oauth_verifier) or empty($temporaryCredentials)) {
                    show_404();
                }

                $this->session->unset_userdata('temporary_credentials');

                $token = $provider->getTokenCredentials($temporaryCredentials, $oauth_token, $oauth_verifier);
                $user = $provider->getUserDetails($token);

                $tokenCredentials->access_token = $token->getIdentifier();
                $tokenCredentials->secret = $token->getSecret();

                break;

            case 'oauth2':
                $oauth_state = $this->session->userdata('oauth_state');

                $this->session->unset_userdata('oauth_state');

                if (empty($this->input->get('code')) || ($this->input->get('state') !== $oauth_state)) {
                    show_404();
                }

                $token = $provider->getAccessToken('authorization_code', ['code' => $this->input->get('code')]);
                $user = $provider->getResourceOwner($token)->getUserDetails();

                $tokenCredentials->access_token = $token->getToken();
                $tokenCredentials->expires = $token->getExpires();
                $tokenCredentials->refresh_token = $token->getRefreshToken();

                break;
        }

        // Let's get ready to interact with users
        $this->load->model('authentication_m');

        // Are we taking this back to the admin?
        if ($this->session->userdata('social_admin_redirect')) {
            // Send the token to the admin controller after redirect
            $this->session->set_userdata('token', [
                'provider' => $provider->name,
                'access_token' => $tokenCredentials->access_token,
                'secret' => isset($tokenCredentials->secret) ? $tokenCredentials->secret : null,
                'expires' => isset($tokenCredentials->expires) ? $tokenCredentials->expires : null,
                'refresh_token' => isset($tokenCredentials->refresh_token) ? $tokenCredentials->refresh_token : null,
            ]);

            $this->session->unset_userdata('social_admin_redirect');

            redirect('admin/social/token_save');
        }

        // Are they logged in?
        if ($this->current_user) {
            // Do they have attached? It might matter
            $auth = $this->authentication_m->get_by([
                'user_id' => $this->current_user->id,
                'provider' => $provider->name
            ]);

            // If there are no attachments, or they can have multiple
            if (!$auth or $this->config->item('multiple_providers') === true) {
                // If there is no uid we can't remember who this is
                if (empty($user->uid)) {
                    show_error('No uid in response from ' . $provider->name . '.');
                }

                // Attach this account to the logged in user
                $this->authentication_m->save([
                    'user_id' => $this->current_user->id,
                    'uid' => $user->uid,
                    'provider' => $provider->name,
                    'access_token' => $tokenCredentials->access_token,
                    'secret' => isset($tokenCredentials->secret) ? $tokenCredentials->secret : null,
                    'expires' => isset($tokenCredentials->expires) ? $tokenCredentials->expires : null,
                    'refresh_token' => isset($tokenCredentials->refresh_token) ? $tokenCredentials->refresh_token : null,
                ]);

                // Attachment went ok so we'll redirect
                redirect($this->input->get('success_url') ? $this->input->get('success_url') : 'social/linked');
            } else {
                show_error(sprintf('This user is already linked to "%s".', $auth->provider));
            }
        }

        // The user exists, so send him on his merry way as a user
        else if ($auth = $this->authentication_m->get_by(['uid' => $user->uid, 'provider' => $provider->name])) {
            // Force a login with this username
            if (!$this->ion_auth->force_login($auth->user_id)) {
                show_error('Failed to log you in.');
            }

            $this->session->set_flashdata('success', lang('user_logged_in'));
            redirect('/');
        }

        // They aren't a user, so redirect to registration page
        else {
            $this->session->set_userdata([
                'user_hash' => (array) $user,
                'token' => [
                    'provider' => $provider->name,
                    'access_token' => $tokenCredentials->access_token,
                    'secret' => isset($tokenCredentials->secret) ? $tokenCredentials->secret : null,
                    'expires' => isset($tokenCredentials->expires) ? $tokenCredentials->expires : null,
                    'refresh_token' => isset($tokenCredentials->refresh_token) ? $tokenCredentials->refresh_token : null,
                ]
            ]);

            if (!empty($user->email)) {
                // Got all required info, so register user automatically
                call_user_func([$this, '_create_user'], $user);
            } else {
                // Email not found, so redirect to user registration page
                redirect('users/register');
            }
        }
    }

    private function _revoke($strategy, $provider, $consumer) {
        $this->current_user or redirect('users/login/social/revoke/' . $provider->name);

        $this->load->model('authentication_m');
        $authentication = $this->authentication_m->get_by(['user_id' => $this->current_user->id, 'provider' => $provider->name]);

        //$request = $provider->getAuthenticatedRequest('DELETE', '/' . $authentication->uid . '/permissions', $authentication->access_token);

        $this->authentication_m->delete_by(['user_id' => $this->current_user->id, 'provider' => $provider->name]);

        redirect('my-profile');
    }

    // List of Linked accounts
    public function linked() {
        $this->current_user or redirect('users/login/social/linked');

        $authentications = $this->authentication_m->get_many_by(['user_id' => $this->current_user->id]);

        redirect('my-profile');
    }

    private function _create_user(object $user) {
        $this->load->model('users/profile_m');
        $this->load->helper('url');

        $username = url_title($user->first_name . '.' . $user->last_name, 'dash', true);

        // do they have a long first name + last name combo?
        if (strlen($username) > 19) {
            // try only the last name
            $username = url_title($user->last_name, 'dash', true);

            if (strlen($username) > 19) {
                // even their last name is over 20 characters, snip it!
                $username = substr($username, 0, 20);
            }
        }

        // Usernames absolutely need to be unique, so let's keep
        // trying until we get a unique one
        $i = 1;

        $username_base = $username;

        while ($this->db->where('username', $username)->count_all_results('users') > 0) {
            // make sure that we don't go over our 20 char username even with a 2 digit integer added
            $username = substr($username_base, 0, 18) . $i;

            ++$i;
        }

        $profile_data = [
            'display_name' => $user->nickname,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];

        // We are registering with a null group_id so we just
        // use the default user ID in the settings.
        $id = $this->ion_auth->register($username, $this->session->session_id, $user->email, null, $profile_data);

        if ($id > 0) {
            $user->id = $id;
            $user->username = $user->nickname;
            $user->display_name = $user->nickname;

            Event::fire(new UserWasCreated($user));

            /* send the internal registered email if applicable */
            if (Settings::get('registered_email')) {
                $this->load->library('user_agent');

                Event::fire('email', [
                    'name' => $user->name,
                    'sender_ip' => $this->input->ip_address(),
                    'sender_agent' => $this->agent->browser() . ' ' . $this->agent->version(),
                    'sender_os' => $this->agent->platform(),
                    'slug' => 'registered',
                    'email' => Settings::get('contact_email')
                ]);
            }

            $this->ion_auth->activate($id, false);

            // Force a login with this username
            if (!$this->ion_auth->force_login($id)) {
                show_error('Failed to log you in.');
            }

            $this->session->set_flashdata('success', lang('user_logged_in'));

            redirect($this->config->item('register_redirect', 'ion_auth'));
        }

        // The user exists, so redirect to login page
        $this->session->set_flashdata('notice', $this->ion_auth->messages());

        redirect('users/login');
    }

}
