<?php
/**
 * The Template for displaying all single posts.
 */



get_header(); ?>

    <div id="primary" class="site-content span9">
<?php the_breadcrumb(); ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content', 'single' ); ?>

		<!-- display app name and release notes  -->
		<p>Using single-app_release.php template</p>
		<p><strong>App Name: </strong>
			<?php echo esc_html( get_post_meta( get_the_ID(), 'project_name', true ) ); ?>
			<br /></p>

		<strong>App Release Project: </strong>
		<?php
		the_terms( $post->ID, 'app_release_project' ,  ' ' );
		?>
		<br />

		<strong>Version Number: </strong>
		<?php echo esc_html( get_post_meta( get_the_ID(), 'version_number', true ) ); ?>
		<br />

		<strong>Release Date: </strong>
		<?php echo esc_html( get_post_meta( get_the_ID(), 'release_date', true ) ); ?>
		<br />

		<strong>Download Link: </strong>
		<?php echo esc_html( get_post_meta( get_the_ID(), 'download_link', true ) ); ?>
		<br />

		<strong>Manifest Link: </strong>
		<?php echo esc_html( get_post_meta( get_the_ID(), 'manifest_link', true ) ); ?>
		<br />

		<strong>App Store Link: </strong>
		<?php echo esc_html( get_post_meta( get_the_ID(), 'app_store_link', true ) ); ?>
		<br />

		<strong>GitHub Link: </strong>

		<?php
			$github_link = esc_html( get_post_meta( get_the_ID(), 'github_link', true ) );
			echo $github_link;
		?>
		<br />

		<?php
		// GitHub links are displayed for all projects, even archived ones
		if ($github_link != null) {
			echo '<a href="';
            echo $github_link;
            echo '" class="btn btn-sm btn-warning">Code on GitHub</a>';
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