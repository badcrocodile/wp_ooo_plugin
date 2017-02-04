<?php

/**
 * @link              https://wfmdigiweb.atlassian.net/wiki/spaces/NON/overview
 * @since             1.0.0
 * @package           IngredientManager
 *
 * @wordpress-plugin
 * Plugin Name:       ROCK Ingredient Manager
 * Plugin URI:        https://wfmdigiweb.atlassian.net/wiki/spaces/NON/overview
 * Description:       Manages the list of acceptable and unacceptable ingredients
 * Version:           1.0.0
 * Author:            WT&F
 * Author URI:        https://wfmdigiweb.atlassian.net/wiki/spaces/NON/overview
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ingredient-manager
 */

// If this file is called directly, abort.
if(!defined( 'WPINC')) {
    die;
}

use IngredientManager\EnqueueScript;
use PostTypes\PostType;

/**
 * The code that runs during plugin activation.
 */
function activate_plugin_slug() {
    require_once plugin_dir_path( __FILE__ ) . 'app/Activator/Activator.php';
    Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_plugin_slug() {
    require_once plugin_dir_path( __FILE__ ) . 'app/Activator/Deactivator.php';
    Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_slug' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_slug' );

/**
 * Require the autoloader
 */
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

/**
 * Enqueue all of our scripts and styles
 */
$scripts = new EnqueueScript();

/**
 * Create CPT to hold the ingredient list & also create related taxonomies
 */
$cpt_rock_ingredients_opts = array(
    'name' => 'rock_ingredient',
    'singular' => 'ROCK Ingredient',
    'plural' => 'ROCK Ingredients',
    'slug' => 'rock-ingredients'
);
$tax_function_opts = array(
    'hierarchical' => false
);
$tax_type_opts = array(
    'hierarchical' => true
);
$rock_ingredients = new PostType($cpt_rock_ingredients_opts);
$rock_ingredients->taxonomy('function', $tax_function_opts);
$rock_ingredients->taxonomy('type', $tax_type_opts);


/**
 * Since everything from here is action or filter based,
 * kick things off by registering your actions or filters.
 */

/**
 * Add actions can be registered using the following format
 */


// // Add new fields to profile page
// add_action('show_user_profile', array(new Profile(), 'create_profile_fields'));
// add_action('edit_user_profile', array(new Profile(), 'create_profile_fields'));
// // Save data from new fields on profile page
// add_action('personal_options_update', array(new Profile(), 'save_profile_fields'));
// add_action('edit_user_profile_update', array(new Profile(), 'save_profile_fields'));
// // This is where we hijack the login sequence and use our own.
// add_filter('authenticate', array(new Login, 'intercept_login'), 1, 3);
// // Remove default WP authentication hook
// remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
