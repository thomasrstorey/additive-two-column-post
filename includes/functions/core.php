<?php
namespace ThomasRStorey\AdditiveTwo_Column_Post\Core;

// TODO: hook to save post action to also save second column editor content  [x]
// TODO: add hidden column post editor																			 [x]
// TODO: hook to new post action to create column Post											 [x]
// TODO: register column post type 																					 [x]
// TODO: hook to delete post to delete columns when their post is deleted		 [x]

/**
 * Default setup routine
 *
 * @uses add_action()
 * @uses do_action()
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );

	add_action( 'edit_form_advanced', $n( 'add_column_toggle' ) );
	add_action( 'edit_form_advanced', $n( 'add_column_editor' ) );
	add_action( 'edit_page_form', $n( 'add_column_toggle' ) );
	add_action( 'edit_page_form', $n( 'add_column_editor' ) );

	add_action( 'save_post', $n( 'add_column' ), 10, 3 );
	add_action( 'edit_post', $n( 'edit_column' ), 10, 2 );
	add_action( 'before_delete_post', $n( 'delete_column' ) );

	add_action( 'admin_enqueue_scripts', $n( 'additive_column_script_enqueue' ) );

	do_action( 'additive_tcp_loaded' );


}

/**
 * Registers the default textdomain.
 *
 * @uses apply_filters()
 * @uses get_locale()
 * @uses load_textdomain()
 * @uses load_plugin_textdomain()
 * @uses plugin_basename()
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'additive_tcp' );
	load_textdomain( 'additive_tcp', WP_LANG_DIR . '/additive_tcp/additive_tcp-' . $locale . '.mo' );
	load_plugin_textdomain( 'additive_tcp', false, plugin_basename( ADDITIVE_TCP_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @uses do_action()
 *
 * @return void
 */
function init() {
	do_action( 'additive_tcp_init' );
	$labels = array(
        'name'               => _x( 'Columns', 'post type general name', 'additive_tcp' ),
        'singular_name'      => _x( 'Column', 'post type singular name', 'additive_tcp' ),
        'menu_name'          => _x( 'Columns', 'admin menu', 'additive_tcp' ),
        'name_admin_bar'     => _x( 'Column', 'add new on admin bar', 'additive_tcp' ),
        'add_new'            => _x( 'Add New', 'column', 'additive_tcp' ),
        'add_new_item'       => __( 'Add New Column', 'additive_tcp' ),
        'new_item'           => __( 'New Column', 'additive_tcp' ),
        'edit_item'          => __( 'Edit Column', 'additive_tcp' ),
        'view_item'          => __( 'View Column', 'additive_tcp' ),
        'all_items'          => __( 'All Columns', 'additive_tcp' ),
        'search_items'       => __( 'Search Columns', 'additive_tcp' ),
        'parent_item_colon'  => __( 'Parent Column:', 'additive_tcp' ),
        'not_found'          => __( 'No columns found.', 'additive_tcp' ),
        'not_found_in_trash' => __( 'No columns found in Trash.', 'additive_tcp' )
    );
	register_post_type('additive_column', array(
		'labels' => $labels,
		'description' => 'Columns inserted as additional content for posts.',
		'public' => true, // XXX: change to false for production
		'hierarchical' => false,
		'capability_type' => 'post',
		'supports' => array('title', 'editor', 'custom-fields')
	));
}

/**
 * Activate the plugin
 *
 * @uses init()
 * @uses flush_rewrite_rules()
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}

function add_column_toggle ($post) {
	if(get_post_type($post) == 'additive_column'){
		return;
	}
	// is this post currently enabled for two column content? check the meta
  $enabled = (bool)get_post_meta($post->ID, 'additive_two_column_post', true);
	$output = '<h4 id="additive-two-column-toggle-label-wrapper">';
	$output.= '<label id="additive-two-column-toggle-label" ';
	$output.= 'for="additive-two-column-toggle">Enable Two Column Content?</label></h4>';
	$output.= '<p id="additive-two-column-toggle-wrapper">';
	if($enabled){
		$output.= '<input type="checkbox" name="additive-two-column-toggle" id="additive-two-column-toggle" value="enable" checked=""/></p>';
	} else {
		$output.= '<input type="checkbox" name="additive-two-column-toggle" id="additive-two-column-toggle" value="enable"/></p>';
	}
	echo $output;
}

function add_column_editor ($post) {
	if(get_post_type($post) == 'additive_column'){
		return;
	}
	$post_ID = $post->ID;
	$enabled = (bool)get_post_meta($post_ID, 'additive_two_column_post', true);
	$content = '';
	if($enabled){
		$column_post_ID = get_post_meta($post_ID, 'additive_two_column_post', true);
		if($column_post_ID){
			$column_post = get_post($column_post_ID);
			if(get_post_type($column_post) == 'additive_column'){
				$content = $column_post->post_content;
			}
		}
	}
	echo '<div id="additivecolumneditor-wrapper">';
	wp_editor($content, 'additivecolumneditor', array('textarea_name' => 'additive-two-column-content'));
	echo '</div>';
}

function add_column ($post_ID, $post, $update) {
	// if this is an existing post, do nothing.
	if($update){
		return;
	}
	// if this is a column that is being saved, do nothing.
	if(!isset($post) || $post->post_type == 'additive_column'){
		return;
	}
	// if saved post is two-column enabled, add a new column post.
	$enabled = false;
	if(isset($_POST['additive-two-column-toggle'])){
		$enabled = true;
	}
	// if enabled, get content from column editor, else do nothing
	if($enabled){
		// create new column post with content from saved post
		$column_content = '';
		if(isset($_POST['additive-two-column-content'])){
			$column_content = $_POST['additive-two-column-content'];
		}
		$column_post = array(
			'post_content' => $column_content,
			'post_title' => $post->post_title." Second Column",
			'post_status' => 'publish',
			'post_type' => 'additive_column',
			'comment_status' => 'closed',
			'meta_input' => array('linked-post' => $post_ID)
		);
		// save column
		$column_ID = wp_insert_post($column_post);
		update_post_meta($post_ID, 'additive_two_column_post', $column_ID);
	}
}

function edit_column ($post_ID, $post) {
	// if this is a column that is being saved, do nothing.
	if(!isset($post) || $post->post_type == 'additive_column'){
		return;
	}
	$enabled = false;
	if(isset($_POST['additive-two-column-toggle'])){
		$enabled = true;
	}
	if($enabled){
		// if enabled, get the the content from the form and update the column
		$column_content = '';
		if(isset($_POST['additive-two-column-content'])){
			$column_content = $_POST['additive-two-column-content'];
		}
		$column_post_ID = get_post_meta($post_ID, 'additive_two_column_post', true);
		if($column_post_ID){
			$column_post = get_post($column_post_ID);
			if(get_post_type($column_post) == 'additive_column'){
				wp_update_post(array(
					'ID' => $column_post->ID,
					'post_content' => $column_content
				));
			}
		} else {
			// enabled, but no column post - make new column
			$column_post = array(
				'post_content' => $column_content,
				'post_title' => $post->post_title." Second Column",
				'post_status' => 'publish',
				'post_type' => 'additive_column',
				'comment_status' => 'closed',
				'meta_input' => array('linked-post' => $post_ID)
			);
			// save column
			$column_ID = wp_insert_post($column_post);
			update_post_meta($post_ID, 'additive_two_column_post', $column_ID);
		}
	} else {
		// if disabled, delete the column, and delete the post meta
		$column_post_ID = get_post_meta($post_ID, 'additive_two_column_post', true);
		if($column_post_ID){
			$column_post = get_post($column_post_ID);
			if(get_post_type($column_post) == 'additive_column'){
				wp_delete_post($column_post_ID);
				delete_metadata('post', $post_ID, 'additive_two_column_post');
			}
		}
	}
}

function delete_column ($post_ID) {
	$_post = get_post($post_ID);
	if(get_post_type($_post) == 'additive_column'){
		return;
	}
	$column_post_ID = get_post_meta($post_ID, 'additive_two_column_post', true);
	if($column_post_ID){
		$column_post = get_post($column_post_ID);
		if(get_post_type($column_post) == 'additive_column'){
			wp_delete_post($column_post_ID);
		}
	}
}

function additive_column_script_enqueue( $hook ) {
		if( 'post.php' != $hook ) return;
		wp_enqueue_script( 'additive-editor-toggle-script',
				ADDITIVE_TCP_URL.'assets/js/additive-two-column-post.js',
				array( 'jquery' )
		);
}
