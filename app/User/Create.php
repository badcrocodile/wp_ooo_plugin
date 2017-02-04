<?php namespace LdapAuth;


class Create {
    private $username;
    private $password;

    /**
     * Create constructor.
     *
     * @param $username
     * @param $password
     */
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;

        $this->create_user();
    }

    /**
     * Creates a new basic WP user with just username & password.
     *
     * @return int|\WP_Error
     */
    public function create_user() {
        $userdata = array(
            'user_login' => $this->username,
            'user_pass'  => $this->password
        );

        return wp_insert_user($userdata);
    }
}