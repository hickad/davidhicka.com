<?php
get_header();
?>

<div class="main-wrapper">
	    <article class="blog-post px-3 py-5 p-md-5">
		    <div class="container single-col-max-width">

<!-- Start the Loop -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <!-- Post content here -->
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="entry-content">
            <?php the_content(); ?>
            <!-- Here you can add additional content like meta fields -->
        </div>
    </article>

<?php endwhile; else : ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>
<!-- End the Loop -->

</div>
		</article>
</div>

<?php get_footer(); ?>
