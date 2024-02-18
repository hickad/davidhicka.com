<?php
/*
Template Name: portfolio
*/

get_header();
?>

<div class="main-wrapper">
	    <section class="cta-section theme-bg-light py-5">
		    <div class="container text-center single-col-max-width">
			    <h2 class="heading">Portfolio</h2>
			    <div class="intro">
			    <p>Welcome to my online portfolio. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. I'm taking on freelance work at the moment. Want some help building your software?</p>
			    
			    </div>
				<a class="btn btn-primary" href="<?php echo home_url('/contact/'); ?>">
					<i class="fas fa-paper-plane me-2"></i>Contact Me
				</a>
		    </div><!--//container-->
	    </section>
	    <section class="projects-list px-3 py-5 p-md-5">
		    <div class="container">
			<div class="text-center">
				<ul id="filters" class="filters mb-5 mx-auto ps-0">
					<li class="type active mb-3 mb-lg-0" data-filter="*">All</li>
					<?php
					$categories = get_categories(array(
						'orderby' => 'name',
						'order'   => 'ASC',
						'hide_empty' => true, // Change to false if you want to show empty categories
					));

					foreach ($categories as $category) {
						// Convert category name to a class-friendly format for filtering
						$filter_class = strtolower(preg_replace('/\s+/', '', $category->name));
						echo '<li class="type mb-3 mb-lg-0" data-filter=".' . esc_attr($filter_class) . '">' . esc_html($category->name) . '</li>';
					}
					?>
				</ul><!--//filters-->
			</div>

			<div class="project-cards row isotope">
    <?php
    $args = array(
        'post_type' => 'portfolio', // Ensure 'portfolio' matches your CPT
        'posts_per_page' => -1, // Adjust as needed
    );
    $portfolio_query = new WP_Query($args);

    if($portfolio_query->have_posts()) : 
        while($portfolio_query->have_posts()) : $portfolio_query->the_post();

        // Get the categories for each post and create a class string
        $categories = get_the_terms(get_the_ID(), 'category'); // Change to your custom taxonomy if not using 'category'
        $class_names = [];
        if($categories){
            foreach($categories as $category) {
                $class_names[] = strtolower($category->slug);
            }
        }
        $class_string = join(" ", $class_names);
    ?>
        <div class="isotope-item col-md-6 mb-5 <?php echo esc_attr($class_string); ?>">
            <div class="card project-card">
                <div class="row">
                    <div class="col-12 col-xl-5 card-img-holder">
                        <?php if(has_post_thumbnail()): ?>
                            <img src="<?php the_post_thumbnail_url(); ?>" class="card-img" alt="image">
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-xl-7">
                        <div class="card-body">
                            <h5 class="card-title"><a href="<?php the_permalink(); ?>" class="theme-link"><?php the_title(); ?></a></h5>
                            <p class="card-text"><?php the_excerpt(); ?></p>
							<?php 
								$client = get_post_meta(get_the_ID(), 'client', true); 
								if (!empty($client)) {
									echo '<p class="card-text"><small class="text-muted">Client: ' . esc_html($client) . '</small></p>';
								}
								?>
                        </div>
                    </div>
                </div>
                <div class="link-mask">
                    <a class="link-mask-link" href="<?php the_permalink(); ?>"></a>
                    <div class="link-mask-text">
                        <a class="btn btn-secondary" href="<?php the_permalink(); ?>">
                            <i class="fas fa-eye me-2"></i>View Case Study
                        </a>
                    </div>
                </div><!--//link-mask-->
            </div><!--//card-->
        </div><!--//isotope-item-->
    <?php
        endwhile;
        wp_reset_postdata();
    endif;
    ?>
</div><!--//row-->

		    </div>
	    </section>
	        
    </div><!--//main-wrapper-->

<?php
get_footer();
