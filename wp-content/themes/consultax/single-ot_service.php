<?php
/**
 * The template for displaying all services
 *
 * This is the template that displays all services by default.
 * Please note that this is the WordPress construct of services
 * and that other 'services' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Consultax
 */

get_header();
?>

    <?php if( consultax_get_layout() == 'full-content' ) :

        while ( have_posts() ) : the_post();

        the_content();

    endwhile; elseif( consultax_get_layout() == 'boxed-content' ) :

        while ( have_posts() ) : the_post(); ?>

        <div class="entry-content">
            <div class="container">
                <div class="boxed-content">    
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

    <?php endwhile; else : ?>

    <div class="entry-content">
        <div class="container">
            <div class="row">
            <div id="primary" class="content-area <?php consultax_content_columns(); ?>">
                <main id="main" class="site-main">

                <?php
                while ( have_posts() ) :
                    the_post();

                    get_template_part( 'template-parts/content', 'page' );

                endwhile; // End of the loop.
                ?>

                </main><!-- #main -->
            </div><!-- #primary -->

            <?php get_sidebar('service'); ?>
            
            </div>
        </div>
    </div>

    <?php endif; ?>

<?php
get_footer();