<?php namespace LdapAuth;


use WP_Query;

class Update {
    private $username;
    private $user_id;

    /**
     * Update constructor.
     *
     * @param     $username
     * @param int $user_id
     */
    public function __construct($username, $user_id) {
        $this->username = $username;
        $this->user_id = $user_id;

        $basedn = "DC=wfm,DC=pvt";
        $filter = "samaccountname=" . $this->username;
        $filter = "(&(objectCategory=person)({$filter}))";
        $fields = array("wfmlocationcode", "wfmpdivisioncode", "displayname", "givenname", "sn", "mail");
        $admin_user = "CASLDAPSvcPRD";
        $admin_pass = "9uilY3R*(E@#Y8";
        $ldapconn = ldap_connect('ldap://wfm.pvt', 636) or die("Could not connect");
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = ldap_bind($ldapconn, "wfm\\" . $admin_user, $admin_pass) or die("Could not bind");
        $result = ldap_search($ldapconn, $basedn, $filter, $fields) or die("Search error");
        $entries = ldap_get_entries($ldapconn, $result);
        $this->email = $entries[0]["mail"][0];
        $this->first_name = $entries[0]["givenname"][0];
        $this->last_name = $entries[0]["sn"][0];
        $this->display_name = $entries[0]["displayname"][0];
        $this->region = $entries[0]["wfmpdivisioncode"][0];
        $this->store = $entries[0]["wfmlocationcode"][0];
        var_dump($entries);
        die();
        ldap_close($ldapconn);
    }

    /**
     * wp_update_user will handle basic profile fields, but is not capable of handling our custom region/store fields.
     * We'll pass that responsibility off to update_user_store_region below.
     *
     * @return int|\WP_Error
     */
    public function update_user_profile() {
        $args = array(
            'ID' => $this->user_id,
            'user_nicename' => $this->display_name ? $this->display_name : "",
            'user_email' => $this->email ? $this->email : "",
            'display_name' => $this->display_name ? $this->display_name : "",
            'first_name' => $this->first_name ? $this->first_name : "",
            'last_name' => $this->last_name ? $this->last_name : ""
        );

        $this->update_user_store_region();

        return $update_user_basic_profile = wp_update_user($args);
    }

    /**
     * Handler for our custom region/store profile fields.
     *
     * @return bool
     */
    public function update_user_store_region() {
        if($this->store && $this->store != "CEN") {
            update_user_meta($this->user_id, 'store', $this->get_store_title($this->store));
        } elseif($this->store == "CEN") {
            update_user_meta($this->user_id, 'store', "CEN");
        } else {
            update_user_meta($this->user_id, 'store', 'undefined');
        }
        if($this->region) {
            update_user_meta($this->user_id, 'region', $this->region);
        } elseif(!$this->region && $this->store="CEN") {
            update_user_meta($this->user_id, 'region', 'Global');
        } else {
            update_user_meta($this->user_id, 'region', 'undefined');
        }

        return true;
    }

    private function get_store_title($store) {
        // The store comes through AD as 3 letter store code
        // Our program requires that it be stored as the full store name
        $args = array(
            'meta_key' => 'tlc',
            'meta_value' => 'ALN',
            'post_type' => 'store'
        );
        $store_query = new WP_Query($args);

        if($store_query->have_posts()) {
            while($store_query->have_posts()) {
                $store_query->the_post();

                var_dump(get_the_title());
                return get_the_title();
            }
        }

        return "Nada";
    }

}