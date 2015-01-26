<?php
/**
 * The Template for displaying all single posts.
 */



get_header(); ?>

    <div id="primary" class="site-content span9">
<?php the_breadcrumb(); ?>
	<?php while ( have_posts() ) : the_post(); ?>

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


<?php endwhile; // end of the loop. ?>

	<?php
		$tags = wp_get_post_tags( get_the_ID() );

		if ($tags) {
			$tag_ids = array();
			foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
			$args=array(
			'tag__in' => $tag_ids,
			'post__not_in' => array(get_the_ID()),
			'posts_per_page'=>3, // Number of related posts to display.
			'ignore_sticky_posts'=>1
			);
			$my_query = new WP_Query( $args );

			if( $my_query->have_posts() ) { ?>
				<div class="related-post">
					<h3><?php _e('Related posts','dw_focus') ?></h3>
					<div class="row-fluid">
						<div class="content-inner">
						<?php
							while( $my_query->have_posts() ) {
								$my_query->the_post();
								get_template_part('content', 'related-post'); 
							 }
						?>			
						</div>
					</div>
				</div>
	<?php 
			} 
		}
	?>	
		
	<?php  while ( have_posts() ) { the_post(); ?>
		<?php comments_template( '', true ); ?>
	<?php } ?>
	</div>
<?php get_sidebar( 'single' ); ?>
<?php get_footer(); ?>