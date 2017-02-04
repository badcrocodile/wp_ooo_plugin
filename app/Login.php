<?php namespace LdapAuth;

use WP_Error;

class Login {

    private $user;
    private $username;
    private $password;
    public $error;

    public function __construct() {
        $this->error = new WP_Error();
    }

    public function intercept_login($user, $username, $password) {
        $this->user = $user;
        $this->username = $username;
        $this->password = $password;

        // If previous authentication succeeded, respect that
        if(is_a($this->user, 'WP_User')) {
            return $this->user;
        }

        // Make sure user & pass fields were filled out
        if(empty($this->username)) {
            $this->error->add('empty_username', '<strong>ERROR:</strong> The username field is required.');

            return $this->error;
        }

        if(empty($this->password)) {
            $this->error->add('empty_password', '<strong>ERROR:</strong> The password field is required.');

            return $this->error;
        }

        // Let's see if they authenticate
        $authentication = new Authenticate($this->username, $this->password);
        $authentication_status = $authentication->authenticate();

        // If authenticated against AD, update their profile and end the script.
        // Else return to login screen and display error.
        if($authentication_status === true) {
            $user_id = $this->get_user_id();

            $update_user = new Update($this->username, $user_id);
            $update_user_basic_profile = $update_user->update_user_profile();

            // If there was a problem, return to the login screen and display the error.
            // Else the user profile was successfully updated so end the script.
            if(is_wp_error($update_user_basic_profile)) {
                do_action('wp_login_failed', $this->username);
                $this->error->add('update_user_error', '<strong>ERROR:</strong> AD credentials are correct and there is a matching WordPress user but a failure was experienced updating their profile information.');

                return $this->error;
            } else {
                // Successful login
                $new_user = new \WP_User($user_id);
                do_action_ref_array('auth_success', array($new_user));

                return $new_user;
            }
        } else {
            do_action('wp_login_failed', $this->username);
            $this->error->add('update_user_error', '<strong>ERROR:</strong> ' . $authentication_status);

            return $this->error;
        }
    }

    /**
     * Checks to see if our user already exists.
     *
     * @return false|\WP_User
     */
    public function user_exists() {
        return get_user_by('login', $this->username);
    }

    /**
     * Gets user ID of person just authenticated.
     * If the person already exists we just return existing ID.
     * If person does not yet exist, we create an account for them
     * with just username and password meta filled out and return their new ID.
     * We'll populate the additional user meta fields later.
     *
     * If user creation fails we throw an error back to login screen.
     *
     * @return int|Create|WP_Error
     */
    private function get_user_id() {
        // Does the user already exist?
        $user_exists = $this->user_exists();

        if(!$user_exists) {
            $create_user = new Create($this->username, $this->password);

            // Was the user created successfully?
            if(is_wp_error($create_user)) {
                do_action('wp_login_failed', $this->username);
                $this->error->add('user_creation_failure', '<strong>ERROR:</strong> AD credentials are correct but there is no matching WordPress user and user creation failed.');

                return $this->error;
            }

            return $user_id = $create_user;
        }

        return $user_id = $user_exists->ID;
    }

}
