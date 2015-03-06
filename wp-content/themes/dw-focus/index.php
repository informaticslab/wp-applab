<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */
get_header(); ?>
    <div id="primary" class="site-content span9">
   
        <?php if ( have_posts() ) : ?>
        
                <?php dynamic_sidebar('dw_focus_home'); ?>
                <br />
                 
                <div class="content-bar row-fluid">
            <h1 class="page-title">
                <?php if ( is_day() ) : ?>
                    <?php printf( __( 'Daily Archives: %s', 'dw-focus' ), '<span>' . get_the_date() . '</span>' ); ?>
                <?php elseif ( is_month() ) : ?>
                    <?php printf( __( 'Monthly Archives: %s', 'dw-focus' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'dw-focus' ) ) . '</span>' ); ?>
                <?php elseif ( is_year() ) : ?>
                    <?php
                        printf( __( 'Category Archives: %s', 'dw-focus' ), '<span>' . single_cat_title( '', false ) . '</span>' );
                    ?>
                 <?php elseif ( is_tag() ) : ?>
                    <?php
                        printf( __( 'Tag Archives: %s', 'dw-focus' ), '<span>' . single_tag_title( '', false ) . '</span>' );
                    ?>
                <?php else : ?>
                    <h1 class="page-title"><?php
						printf( __( "NEW APPS AVAILABLE FOR DOWNLOAD:  IOS AND ANDROID", 'dw-focus' ), '<span>' . single_cat_title( '', false ) . '</span>' );
					?></h1>
                <?php endif; ?>
            </h1>

            <div class="post-layout">
                <a class="layout-list active" href="#"><i class="icon-th-list"></i></a>
                <a class="layout-grid" href="#"><i class="icon-th"></i></a>
            </div>
        </div>
                <div class="content-inner">
                
                    <?php
            		$apppost = array( 'post_type' => 'app_release', );
            		$loop = new WP_Query( $apppost );
           		 ?>
                   <?php while ( $loop->have_posts() ) : $loop->the_post();?>

                        <?php get_template_part( 'content', 'archive' ); ?>
                    <?php endwhile; ?>
                </div>
                <?php dw_focus_pagenavi(); ?>

        <?php else : ?>

            <?php get_template_part( 'no-results', 'archive' ); ?>

        <?php endif; ?>
        
    </div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>