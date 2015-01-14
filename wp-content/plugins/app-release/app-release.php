<?php
/*
Plugin Name: App Release
Plugin URI: http://www.phiresearchlab.org/
Description: Declares a plugin that will create a custom post type for releasing mobile apps.
Version: 0.1
Author: Greg Ledbetter
Author URI: http://www.phiresearchlab.org/
License: Apache License 2.0
*/

require('release-mgr.php');


add_action( 'init', 'create_app_release' );
add_action( 'admin_init', 'app_release_admin' );
add_action( 'save_post', 'add_app_release_fields', 10, 2 );
add_filter( 'template_include', 'include_template_function', 1 );

$release_mgr = new ReleaseManager();


function include_template_function( $template_path ) {
    if ( get_post_type() == 'app_releases' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-app-release.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-app-release.php';
            }
        }
    }
    return $template_path;
}


function create_app_release() {
    register_post_type( 'app_releases',
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
            'menu_position' => 15,
            'supports' => array( 'title','editor', 'thumbnail' ), #'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

function app_release_admin() {
    add_meta_box( 'app_release_meta_box',
        'App Release Details',
        'display_app_release_meta_box',
        'app_releases', 'normal', 'high'
    );
}

function add_app_release_fields( $app_release_id, $app_release ) {

    global $release_mgr;

    // if post type is an app release
    if ( $app_release->post_type == 'app_releases' ) {

        // store data in post meta table if present in post data
        if ( isset( $_POST['project_name_input'] ) && $_POST['project_name_input'] != '' )
            update_post_meta( $app_release_id, 'project_name', $_POST['project_name_input'] );

        if ( isset( $_POST['version_number_input'] ) && $_POST['version_number_input'] != '' )
            update_post_meta( $app_release_id, 'version_number', $_POST['version_number_input'] );

        if ( isset( $_POST['release_date_input'] ) && $_POST['release_date_input'] != '' )
            update_post_meta( $app_release_id, 'release_date', $_POST['release_date_input'] );

        if ( isset( $_POST['platform_id_input'] ) && $_POST['platform_id_input'] != '' )
            update_post_meta( $app_release_id, 'platform_id', $_POST['platform_id_input'] );

        if ( isset( $_POST['manifest_link_input'] ) && $_POST['manifest_link_input'] != '' )
            update_post_meta( $app_release_id, 'manifest_link', $_POST['manifest_link_input'] );

        if ( isset( $_POST['app_store_link_input'] ) && $_POST['app_store_link_input'] != '' )
            update_post_meta( $app_release_id, 'app_store_link', $_POST['app_store_link_input'] );

        if ( isset( $_POST['github_link_input'] ) && $_POST['github_link_input'] != '' )
            update_post_meta( $app_release_id, 'github_link', $_POST['github_link_input'] );



    }


}



function display_app_release_meta_box( $app_release ) {

    global $release_mgr;


    // get current name of the app and release notes
    $project_name = get_post_meta( $app_release->ID, 'project_name', true );
    $version_number = get_post_meta( $app_release->ID, 'version_number', true );
    $mobile_platform_id = get_post_meta( $app_release->ID, 'platform_id', true );
    $release_date = get_post_meta( $app_release->ID, 'release_date', true );
    $manifest_link = get_post_meta( $app_release->ID, 'manifest_link', true );
    $app_store_link = get_post_meta( $app_release->ID, 'app_store_link', true );
    $github_link = get_post_meta( $app_release->ID, 'github_link', true );

    ?>
    <table class="form-table">
        <tr>
            <td>Project Name</td>
            <td><input  type="text" size="20" name="project_name_input" value="<?php echo $project_name; ?>" /></td>
            </td>
        </tr>
        <tr>
            <td>Version Number</td>
            <td><input type="text" size="20" name="version_number_input" value="<?php echo $version_number; ?>" /></td>
        </tr>
        <tr>
            <td>Release Date</td>
            <td><input type="text" size="20" name="release_date_input" value="<?php echo $release_date; ?>" /></td>
        </tr>
        <tr>
            <td>Mobile Platform</br>

            <input type="radio" name="platform_id_input" value="ios" <?php if($mobile_platform_id == 'ios') echo 'checked="checked"'; ?> />iOS </br>
            <input type="radio" name="platform_id_input" value="android" <?php if($mobile_platform_id == 'android') echo 'checked="checked"'; ?>  />Android</br>
            </td>
        </tr>
        <tr>
            <td>Manifest Link</td>
            <td><input type="text" size="60" name="manifest_link_input" value="<?php echo $manifest_link; ?>" /></td>
        </tr>
        <tr>
            <td>App Store Link</td>
            <td><input type="text" size="60" name="app_store_link_input" value="<?php echo $app_store_link; ?>" /></td>
        </tr>
        <tr>
            <td>GitHub Link</td>
            <td><input type="text" size="60" name="github_link_input" value="<?php echo $github_link; ?>" /></td>
        </tr>

    </table>
<?php
}
?>

