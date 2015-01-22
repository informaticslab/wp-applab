<?php get_header(); ?>
    <section id="primary">
        <div id="content" role="main" style="width: 70%">
            <?php if ( have_posts() ) : ?>
                <header class="page-header">
                    <h1 class="page-title">App Releases</h1>
                </header>
                <p>Using archive-app_release.php template</p>

                <table>
                    <!-- Display table headers -->
                    <tr>
                        <th style="width: 200px"><strong>App Release</strong></th>
                        <th><strong>Version</strong></th>
                    </tr>

                    <!-- Start the Loop -->
                    <?php while ( have_posts() ) : the_post(); ?>
                        <!-- Display review title and author -->
                        <tr>
                            <td><a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?></a></td>
                            <td><?php echo esc_html( get_post_meta( get_the_ID(), 'version_number', true ) ); ?></td>
                        </tr>
                    <?php endwhile; ?>

                    <!-- Display page navigation -->

                </table>
                <?php global $wp_query;
                if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 1 ) { ?>
                    <nav id="<?php echo $nav_id; ?>">
                        <div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&larr;</span> Older releases'); ?></div>
                        <div class="nav-next"><?php previous_posts_link( 'Newer releases <span class= "meta-nav">&rarr;</span>' ); ?></div>
                    </nav>
                <?php };
            endif; ?>
        </div>
    </section>
    <br /><br />
<?php get_footer(); ?>