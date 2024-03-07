<?php
/**
 * Template Name: about
 *
 * @package DigitalResume
 */

get_header();
?>

<div class="main-wrapper">		
		<section class="about-me-section p-3 p-lg-5 theme-bg-light">
			<div class="container">
				<div class="profile-teaser row">
					
					<div class="col">
						<h2 class="name font-weight-bold mb-1">David Hicka</h2>
						<div class="tagline mb-3">Software Engineer</div>
						<div class="bio mb-4">I'm a software engineer specialised in frontend and backend development for complex scalable web apps. I write about software development on <a class="text-link" href="blog-home.html">my blog</a>. Want to know how I may help your project? Check out my project <a class="text-link" href="projects.html">portfolio</a> and <a class="text-link" href="resume.html">online resume</a>.
						</div><!--//bio-->
						<div class="mb-4">
							<a class="btn btn-primary me-2 mb-3" href="<?php echo home_url('/portfolio/'); ?>">
								<i class="fas fa-arrow-alt-circle-right me-2"></i>
								<span class="d-none d-md-inline">View</span> Portfolio
							</a>
							<a class="btn btn-secondary mb-3" href="<?php echo home_url('/resume/'); ?>">
								<i class="fas fa-file-alt me-2"></i>
								<span class="d-none d-md-inline">View</span> Resume
							</a>
						</div>
					</div><!--//col-->
					
					<div class="col-md-5 col-lg-5">
					    <!-- <img class="profile-image img-fluid mb-3 mb-lg-0 me-md-0" src="assets/images/profile-lg.jpg" alt=""> -->
					</div>
				</div>
			</div>
		</section><!--//about-me-section-->
		
		<section class="overview-section p-3 p-lg-5">
			<div class="container">
				<h2 class="section-title font-weight-bold mb-3">What I do</h2>
				<div class="section-intro mb-5">I have more than 10 years' experience building software for clients all over the world. Below is a quick overview of my main technical skill sets and technologies I use. Want to find out more about my experience? Check out my <a class="text-link" href="resume.html">online resume</a> and <a class="text-link" href="portfolio.html">project portfolio</a>.</div>
				<div class="row">
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-js-square"></i></div>
							<h3 class="item-title">Vanilla JavaScript</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.</div>
						</div><!--//item-inner-->
					</div><!--//item-->
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-angular me-2"></i><i class="fab fa-react me-2"></i><i class="fab fa-vuejs"></i></div>
							<h3 class="item-title">Angular, React &amp;  Vue</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-node-js"></i></div>
							<h3 class="item-title">Node.js</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-python"></i></div>
							<h3 class="item-title">Python &amp; Django</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-php"></i></div>
							<h3 class="item-title">PHP</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-npm me-2"></i><i class="fab fa-gulp me-2"></i><i class="fab fa-grunt"></i></div>
							<h3 class="item-title">npm, Gulp &amp; Grunt</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-html5 me-2"></i><i class="fab fa-css3-alt"></i></div>
							<h3 class="item-title">HTML &amp; CSS</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
					<div class="item col-6 col-lg-3">
						<div class="item-inner">
							<div class="item-icon"><i class="fab fa-sass me-2"></i><i class="fab fa-less"></i></div>
							<h3 class="item-title">Sass &amp; LESS</h3>
							<div class="item-desc">List skills/technologies here. You can change the icon above to any of the 1500+ <a class="theme-link" href="https://fontawesome.com/" target="_blank">FontAwesome 5 free icons</a> available. Aenean commodo ligula eget dolor.  </div>
						</div><!--//item-inner-->
					</div><!--//item-->
				</div><!--//row-->				
			</div><!--//container-->
		</section>
		
		<div class="container"><hr></div>
				
		<div class="container"><hr></div>
		
		<section class="featured-section p-3 p-lg-5">
			<div class="container">
				<h2 class="section-title font-weight-bold mb-5">Featured Projects</h2>
				<div class="row">
					<div class="col-md-6 mb-5">
						<div class="card project-card">
							<div class="row no-gutters">
								<div class="col-12 col-xl-5 card-img-holder">
									<!-- <img src="assets/images/project/project-1.jpg" class="card-img" alt="image"> -->
								</div>
								<div class="col-12 col-xl-7">
									<div class="card-body">
										<h5 class="card-title"><a href="project.html" class="theme-link">Project Heading</a></h5>
										<p class="card-text">Project intro lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes.</p>
										<p class="card-text"><small class="text-muted">Client: Google</small></p>
									</div>
								</div>
							</div>
							<div class="link-mask">
								<a class="link-mask-link" href="project.html"></a>
								<div class="link-mask-text">
									<a class="btn btn-secondary" href="project.html">
										<i class="fas fa-eye me-2"></i>View Case Study
									</a>
								</div>
							</div><!--//link-mask-->
						</div><!--//card-->
					</div><!--//col-->
					<div class="col-md-6 mb-5">	
						<div class="card project-card">
							<div class="row no-gutters">
								<div class="col-12 col-xl-5 card-img-holder">
									<!-- <img src="assets/images/project/project-2.jpg" class="card-img" alt="image"> -->
								</div>
								<div class="col-12 col-xl-7">
									<div class="card-body">
										<h5 class="card-title"><a href="project.html" class="theme-link">Project Heading</a></h5>
										<p class="card-text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>
										<p class="card-text"><small class="text-muted">Client: Dropbox</small></p>
									</div>
								</div>
							</div>
							<div class="link-mask">
								<a class="link-mask-link" href="project.html"></a>
								<div class="link-mask-text">
									<a class="btn btn-secondary" href="project.html">
										<i class="fas fa-eye me-2"></i>View Case Study
									</a>
								</div>
							</div><!--//link-mask-->
						</div><!--//card-->
					</div><!--//col-->
					<div class="col-md-6 mb-5">
						<div class="card project-card">
							<div class="row no-gutters">
								<div class="col-12 col-xl-5 card-img-holder">
									<!-- <img src="assets/images/project/project-3.jpg" class="card-img" alt="image"> -->
								</div>
								<div class="col-12 col-xl-7">
									<div class="card-body">
										<h5 class="card-title"><a href="project.html" class="theme-link">Project Heading</a></h5>
										<p class="card-text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>
										<p class="card-text"><small class="text-muted">Client: Google</small></p>
									</div>
								</div>
							</div>
							<div class="link-mask">
								<a class="link-mask-link" href="project.html"></a>
								<div class="link-mask-text">
									<a class="btn btn-secondary" href="project.html">
										<i class="fas fa-eye me-2"></i>View Case Study
									</a>
								</div>
							</div><!--//link-mask-->
						</div><!--//card-->
					</div><!--//col-->
					<div class="col-md-6 mb-5">
						<div class="card project-card">
							<div class="row no-gutters">
								<div class="col-12 col-xl-5 card-img-holder">
									<!-- <img src="assets/images/project/project-4.jpg" class="card-img" alt="image"> -->
								</div>
								<div class="col-12 col-xl-7">
									<div class="card-body">
										<h5 class="card-title"><a href="project.html" class="theme-link">Project Heading</a></h5>
										<p class="card-text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>
										<p class="card-text"><small class="text-muted">Client: Uber</small></p>
									</div>
								</div>
							</div>
							<div class="link-mask">
								<a class="link-mask-link" href="project.html"></a>
								<div class="link-mask-text">
									<a class="btn btn-secondary" href="project.html">
										<i class="fas fa-eye me-2"></i>View Case Study
									</a>
								</div>
							</div><!--//link-mask-->
						</div><!--//card-->
					</div><!--//col-->
				</div><!--//row-->
				<div class="text-center py-3">
					<a href="<?php echo home_url('/portfolio/'); ?>" class="btn btn-primary">
						<i class="fas fa-arrow-alt-circle-right me-2"></i>View Portfolio
					</a>
				</div>
								
			</div><!--//container-->
		</section><!--//featured-section-->
		
		<div class="container"><hr></div>
		
		<section class="latest-blog-section p-3 p-lg-5">
    <div class="container">
        <h2 class="section-title font-weight-bold mb-5">Latest Blog Posts</h2>
        <div class="row">
            <?php
            // WP_Query arguments
            $args = array(
                'post_type'              => 'post',
                'post_status'            => 'publish',
                'posts_per_page'         => '3', // Adjust as needed
                'order'                  => 'DESC',
                'orderby'                => 'date',
            );

            // The Query
            $query = new WP_Query( $args );

            // The Loop
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    ?>
                    <div class="col-md-4 mb-5">
                        <div class="card blog-post-card">
                            <!-- Dynamically get the post thumbnail if you have one -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <img class="card-img-top" src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><a class="theme-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                <p class="card-text"><?php the_excerpt(); ?></p>
                                <p class="mb-0"><a class="text-link" href="<?php the_permalink(); ?>">Read more &rarr;</a></p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">Published <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></small>
                            </div>
                        </div><!--//card-->
                    </div><!--//col-->
                    <?php
                }
            } else {
                // No posts found
                echo '<p>No posts found.</p>';
            }

            // Restore original Post Data
            wp_reset_postdata();
            ?>
        </div><!--//row-->
        <div class="text-center py-3">
            <a href="<?php echo home_url('/blog/'); ?>" class="btn btn-primary">
                <i class="fas fa-arrow-alt-circle-right me-2"></i>View Blog
            </a>
        </div>
    </div><!--//container-->
</section>

		
	</div><!--//main-wrapper-->




<?php
// get_sidebar();
get_footer();
