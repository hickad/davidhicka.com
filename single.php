<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package DigitalResume
 */

get_header();
?>


<div class="main-wrapper">
    <article class="blog-post px-3 py-5 p-md-5">
        <?php
        while (have_posts()):
            the_post();

            get_template_part('template-parts/content', get_post_type());
        ?>
        <nav class="blog-nav nav nav-justified my-5">
            <?php
            the_post_navigation(
                array(
                    'prev_text' => '<span class="nav-link-prev nav-item nav-link rounded-left">' . esc_html__('Previous', 'digitalresume') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-link-next nav-item nav-link rounded-right">' . esc_html__('Next', 'digitalresume') . '</span> <span class="nav-title">%title</span>',
                )
            );
            ?>
        </nav>
        <?php
        endwhile; // End of the loop.
        ?>
    </article>
</div>

<?php
get_footer();
?>
