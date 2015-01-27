<?php
/*Template Name: App Release Template
*/

get_header(); ?>
    <div id="primary">
        <div id="content" role="main">
            <?php while ( have_posts() ) : the_post();?>
                <?php get_template_part( 'content', 'single' );

                the_terms( $post->ID, 'app_release_project' ,  ' ' );

                echo '<div class="btn-toolbar">';

                $platform = get_post_meta( $post->ID, 'platform_id', true );

                $anchor_start = '<a href="';

                // iOS part of template
                if ($platform === 'ios' ) {

                    $ipa_path = get_post_meta($post->ID, 'download_link', true);
                    $manifest_link = get_post_meta($post->ID, 'manifest_link', true);
                    $itunes_link = get_post_meta($post->ID, 'app_store_link', true);

                    if ($itunes_link) {
                        echo $anchor_start, $itunes_link, '" class="btn btn-sm btn-info">iOS Release Download</a>';
                    } else {
                        // detect iOS devices
                        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
                        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
                        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");

                        if ($iPhone | $iPad | $iPod)
                            $ios_device = true;
                        else
                            $ios_device = false;

                        // manifest links only work for iOS devices and IPA can only be open on desktop
                        if ($ios_device) {
                            if ($manifest_link) {
                                echo $anchor_start, $manifest_link, '" class="btn btn-sm btn-info">iOS Beta Download</a>';
                            }
                        } else if ($ipa_path) {
                            echo $anchor_start, $ipa_path, '" class="btn btn-sm btn-info">iOS Beta Download</a>';
                        }

                    }

                }

                // Android part of template
                elseif ($platform === 'android') {

                    $apk_path = get_post_meta($post->ID, 'download_link', true);
                    $manifest_link = get_post_meta($post->ID, 'manifest_link', true);
                    $google_play_link = get_post_meta($post->ID, 'app_store_link', true);

                    if($google_play_link) {
                        echo $anchor_start, $google_play_link, '" class="btn btn-sm btn-success">Android Release Download</a>';
                    } else if($apk_path) {
                        echo $anchor_start, $apk_path, '" class="btn btn-sm btn-success">Android Beta Download</a>';
                    }

                }

                // GitHub links are displayed for all projects, even archived ones
                $github_link = get_post_meta( $post->ID, 'github_link', true );

                if ($github_link != null) {
                    echo '<a href="', $github_link, '" class="btn btn-sm btn-warning">Code on GitHub</a>';
                }
                echo '</div>';

                ?>

            <?php endwhile; ?>
        </div>
    </div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>