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

use IngredientManager\AddAction;
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
 * Create CPT to hold the ingredient list & also create related taxonomies
 * @link https://github.com/jjgrainger/PostTypes
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
// Create our CPT
$rock_ingredients = new PostType($cpt_rock_ingredients_opts);
// Add our taxonomies
$rock_ingredients->taxonomy('function', $tax_function_opts);
$rock_ingredients->taxonomy('type', $tax_type_opts);

/**
 * Enqueue all of our scripts and styles
 */
add_action('wp_enqueue_scripts', array(new AddAction(), 'enqueue_scripts'));

// Adding filters:
// add_filter('authenticate', array(new AddFilter(), 'method_name') 1, 3);

