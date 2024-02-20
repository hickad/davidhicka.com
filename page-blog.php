<?php
/*
Template Name: blog
*/

get_header();
?>

<div class="main-wrapper">
	    
	    <article class="blog-post px-3 py-5 p-md-5">
		    <div class="container single-col-max-width">

    <?php
    // Define our WP Query Parameters
    $the_query = new WP_Query( 'posts_per_page=5' ); ?>

	<header class="blog-post-header">
		<?php
		// Start our WP Query
		while ($the_query -> have_posts()) : $the_query -> the_post(); 
		// Display the Post Title with Hyperlink
		?>
	</header>
    
    <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

    <div class="entry-content">
        <?php the_excerpt(); // Display the post excerpt ?>
    </div>

    <?php 
    // Repeat the process and reset once it hits the limit
    endwhile;
    wp_reset_postdata();
    ?>

			</div>
		</article>
</div><!-- #primary -->



    <?php
get_footer();