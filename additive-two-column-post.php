<?php
/**
 * Plugin Name: Additive Two Column Post
 * Plugin URI:  http://github.com/thomasrstorey
 * Description: Allows posts to have two columns of content.
 * Version:     0.1.0
 * Author:      Thomas R Storey
 * Author URI:  http://thomasrstorey.net
 * Text Domain: additive_tcp
 * Domain Path: /languages
 * License:     MIT
 */


/**
 * Built using yo wp-make:plugin
 * Copyright (c) 2016 10up, LLC
 * https://github.com/10up/generator-wp-make
 */

// Useful global constants
define( 'ADDITIVE_TCP_VERSION', '0.1.0' );
define( 'ADDITIVE_TCP_URL',     plugin_dir_url( __FILE__ ) );
define( 'ADDITIVE_TCP_PATH',    dirname( __FILE__ ) . '/' );
define( 'ADDITIVE_TCP_INC',     ADDITIVE_TCP_PATH . 'includes/' );

// Include files
require_once ADDITIVE_TCP_INC . 'functions/core.php';


// Activation/Deactivation
register_activation_hook( __FILE__, '\ThomasRStorey\AdditiveTwo_Column_Post\Core\activate' );
register_deactivation_hook( __FILE__, '\ThomasRStorey\AdditiveTwo_Column_Post\Core\deactivate' );

// Bootstrap
ThomasRStorey\AdditiveTwo_Column_Post\Core\setup();