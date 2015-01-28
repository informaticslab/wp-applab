<?php
/*
Plugin Name: App Release
Plugin URI: http://www.phiresearchlab.org/
Description: Declares a plugin that will create a custom post type for releasing mobile apps.
Version: 0.3
Author: Greg Ledbetter
Author URI: http://www.phiresearchlab.org/
License: Apache License 2.0
*/

require('release-mgr.php');


add_action( 'init', 'create_app_release' );
add_action( 'admin_init', 'app_release_admin' );
add_action( 'save_post_app_release', 'save_app_release_fields', 10, 2 );
add_action( 'init', 'create_release_taxonomies');
//add_filter( 'template_include', 'include_template_function', 1 );
add_action( 'restrict_manage_posts', 'release_filter_list' );
add_filter( 'parse_query','perform_filtering' );
//add_action( 'publish_app_release', 'set_app_release_title');

$release_mgr = new ReleaseManager();
$app_release_error = false;
$app_release_error_msg = '';

function perform_filtering( $query ) {
    $qv = &$query->query_vars;

    if (isset($qv['app_release_project']) ) {
        if (($qv['app_release_project']) && is_numeric($qv['app_release_project'])) {
            $term = get_term_by('id', $qv['app_release_project'], 'app_release_project');
            $qv['app_release_project'] = $term->slug;
        }
    }
}

function release_filter_list() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'app_release' ) {
        wp_dropdown_categories( array(
            'show_option_all' => 'Show All Releases',
            'taxonomy' => 'app_release_project',
            'name' => 'app_release_project',
            'orderby' => 'name',
            'selected' => ( isset( $wp_query->query['app_release_project'] ) ? $wp_query->query['app_release_project'] : '' ),
            'hierarchical' => false,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ) );
    }
}

function include_template_function( $template_path ) {
    if ( get_post_type() == 'app_release' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-app_release.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-app_release.php';
            }
        }
    } elseif ( is_archive() ) {
        if ( $theme_file = locate_template( array ( 'archive-app_release.php' ) ) ) {
            $template_path = $theme_file;
        } else {
            $template_path = plugin_dir_path( __FILE__ ) . '/archive-app_release.php';
        }
    }

    return $template_path;
}

function create_release_taxonomies() {
    register_taxonomy(
        'app_release_project',
        'app_release',
        array(
            'labels' => array(
                'name' => 'App Project',
                'add_new_item' => 'Add New App Project',
                'new_item_name' => "New App Release Project"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}

function create_app_release() {
    register_post_type( 'app_release',
        array(
            'labels' => array(
                'name' => 'App Releases',
                'singular_name' => 'App Release',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New App Release',
                'edit' => 'Edit',
                'edit_item' => 'Edit App Release',
                'new_item' => 'New App Release',
                'view' => 'View',
                'view_item' => 'View App Release',
                'search_items' => 'Search App Releases',
                'not_found' => 'No App Releases found',
                'not_found_in_trash' => 'No App Releases found in Trash',
                'parent' => 'Parent App Release'
            ),

            'public' => true,
            'capability_type' =>'post',
            'menu_position' => 2,
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array( 'category' ),
            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

function app_release_admin() {
    add_meta_box( 'app_release_meta_box',
        'App Release Details',
        'display_app_release_meta_box',
        'app_release', 'normal', 'high'
    );
}

function save_app_release_fields( $app_release_id, $app_release )
{

    global $release_mgr;

    if ('POST' !== $_SERVER['REQUEST_METHOD'])
        return;

    if (defined('DOING_AJAX') || defined('DOING_CRON') || defined('DOING_AUTOSAVE'))
        return;

    // if post type is an app release
    if ($app_release->post_type == 'app_release') {

        // store data in post meta table if present in post data
        if (!empty($_POST['project_name_input'])) {
            $project_name =  $_POST['project_name_input'];
            update_post_meta($app_release_id, 'project_name', $project_name);
        } else {
            // error_log('Project name is not set', 0);
        }

        if (!empty($_POST['version_number_input'])) {
            $version_number = $_POST['version_number_input'];
            update_post_meta($app_release_id, 'version_number', $version_number);
        } else {
            // error_log('Version number is not set', 0);
        }

        $release = $release_mgr->configure_release($project_name, $version_number);

        update_post_meta($app_release_id, 'release_date', $release->date);
        update_post_meta($app_release_id, 'platform_id', $release->platform);
        update_post_meta($app_release_id, 'manifest_link', $release->manifest_link);
        update_post_meta($app_release_id, 'app_store_link', $release->app_store_link);
        update_post_meta($app_release_id, 'github_link', $release->github_link);
        update_post_meta($app_release_id, 'download_link', $release->download_link);
        update_post_meta($app_release_id, 'icon', $release->icon);


        //  global $wpdb;
        //  $wpdb->update( $wpdb->posts, array( 'post_title' =>  $_POST['project_name_input']. ' ' . $_POST['version_number_input'] ), array( 'ID' => $app_release_id ) );

    }

}


add_action( 'admin_enqueue_scripts', 'wps_cpt_admin_enqueue_scripts' );
/**
 * Disable initial autosave/autodraft
 */
function wps_cpt_admin_enqueue_scripts() {
    if ( 'app_release' == get_post_type() )
        wp_dequeue_script( 'autosave' );
}

function display_app_release_meta_box( $app_release ) {

    global $release_mgr;

    // get current name of the app and release notes
    $project_name = get_post_meta( $app_release->ID, 'project_name', true );
    $version_number = get_post_meta( $app_release->ID, 'version_number', true );
//    $mobile_platform_id = get_post_meta( $app_release->ID, 'platform_id', true );
//    $release_date = get_post_meta( $app_release->ID, 'release_date', true );
//    $manifest_link = get_post_meta( $app_release->ID, 'manifest_link', true );
//    $app_store_link = get_post_meta( $app_release->ID, 'app_store_link', true );
//    $github_link = get_post_meta( $app_release->ID, 'github_link', true );


    ?>
    <table class="form-table">
        <tr>
            <td>Project Name</td>
            <td>
            <?php $release_mgr->write_plugin_project_buttons($project_name); ?>
            </td>
        </tr>
         <tr>
            <td>Version Number</td>
            <td><input type="text" size="20" name="version_number_input" value="<?php echo $version_number; ?>" /></td>
        </tr>
    </table>
<?php
}
?>

