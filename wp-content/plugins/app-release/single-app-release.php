<?php
/*Template Name: App Release Template
*/

get_header(); ?>
    <div id="primary">
        <div id="content" role="main">
            <?php
            $apppost = array( 'post_type' => 'app_releases', );
            $loop = new WP_Query( $apppost );
            ?>
            <?php while ( $loop->have_posts() ) : $loop->the_post();?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">

                        <!-- display app name and release notes  -->
                        <strong>App Name: </strong>
                        <?php echo esc_html( get_post_meta( get_the_ID(), 'project_name', true ) ); ?>
                        <br />

                        <strong>Version Number: </strong>
                        <?php echo esc_html( get_post_meta( get_the_ID(), 'version_number', true ) ); ?>
                        <br />

                        <strong>Manifest Link: </strong>
                        <?php echo esc_html( get_post_meta( get_the_ID(), 'manifest_link', true ) ); ?>
                        <br />

                        <strong>GitHub Link: </strong>

                        <?php echo esc_html( get_post_meta( get_the_ID(), 'github_link', true ) ); ?>
                        <br />
                    </header>

                    <!-- display app release info contents -->
                    <div class="entry-content"><?php the_content(); ?></div>
                </article>

            <?php endwhile; ?>
        </div>
    </div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>