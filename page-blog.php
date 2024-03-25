<?php
/*
Template Name: blog
*/

get_header();
?>

<div class="main-wrapper">
    <section class="cta-section theme-bg-light py-5">
		    <div class="container text-center">
			    <h2 class="heading">A Blog About Software Development</h2>
			    <div class="intro">Welcome to my blog. Subscribe and get my latest blog post in your inbox.</div>
			    <div class="single-form-max-width pt-3 mx-auto">
                <?php echo do_shortcode('[contact-form-7 id="3a21089" title="Subscribe to Blog"]'); ?>
			    </div><!--//single-form-max-width-->
		    </div><!--//container-->
	    </section>	    


        <section class="blog-list px-3 py-5 p-md-5">
        <div class="container">
            <div class="row">
                <?php
                // Define our WP Query Parameters
                $the_query = new WP_Query('posts_per_page=6');

                // Start our WP Query
                while ($the_query->have_posts()) : $the_query->the_post();
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="card blog-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <img class="card-img-top" src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="Post Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><a class="theme-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                <p class="card-text"><?php the_excerpt(); ?></p>
                                <p class="mb-0"><a class="text-link" href="<?php the_permalink(); ?>">Read more &rarr;</a></p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">Published <?php echo get_the_date('F j, Y'); ?></small>
                            </div>
                        </div><!--//card-->
                    </div><!--//col-->
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div><!--//row-->
        </div>
    </section>
</div><!--//main-wrapper-->

<?php
get_footer();
?>