<?php namespace LdapAuth;


class Profile {

    /**
     * Adding new fields to user profile page.
     *
     * @param $user     The user for which we are creating the profile fields
     *
     * @return bool
     */
    public function create_profile_fields($user) { ?>
        <h3>Store & Region information</h3>
        <table class="form-table">
            <tr>
                <th><label for="store">Store</label></th>
                <td>
                    <input type="text" name="store" id="store" value="<?php echo esc_attr(get_the_author_meta('store', $user->ID)); ?>" class="regular-text" /><br />
                    <span class="description">Store TLC</span>
                </td>
            </tr>
            <tr>
                <th><label for="region">Region</label></th>
                <td>
                    <input type="text" name="region" id="region" value="<?php echo esc_attr(get_the_author_meta('region', $user->ID)); ?>" class="regular-text" /><br />
                    <span class="description">Region ID</span>
                </td>
            </tr>
        </table> <?php

        return true;
    }

    /**
     * Creating user profile fields and saving them when updated are 2 different things.
     * This contains the logic to actually update the database when someone clicks the 'save'
     * button on their profile edit page. We many want to remove this at some point, since
     * even if someone manually updates their store/region info it will be overwritten the
     * next time they login.
     *
     * @param $user_id
     *
     * @return bool
     */
    public function save_profile_fields($user_id) {
        if(!current_user_can('edit_user', $user_id)) {
            return false;
        }

        update_user_meta($user_id, 'store', $_POST['store']);
        update_user_meta($user_id, 'store', $_POST['region']);

        return true;
    }

}