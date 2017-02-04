<?php namespace LdapAuth;


class Authenticate {

    private $username;
    private $password;

    /**
     * Authenticate constructor.
     *
     * @param $username
     * @param $password
     */
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;

        $this->authenticate();
    }

    /**
     * Here we validate the user trying to login against AD.
     * This is just a simple yes/no validation.
     *
     * @return bool|string
     */
    public function authenticate() {
        $res = @ldap_connect('ldap://wfm.pvt', 636);
        if(!$res) {
            return "Can't connect to Active Directory.";
        }

        $result = @ldap_bind($res, "wfm\\".$this->username, $this->password);
        if(!$result) {
            return "Invalid username and/or password.";
        }

        ldap_close($res);

        return true;
    }
}