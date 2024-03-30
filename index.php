<?php
/**
 * Template Name: about
 *
 * @package DigitalResume
 */

get_header();


$correct_password_entered = isset($_SESSION['correct_password_entered']) && $_SESSION['correct_password_entered'];

echo "Correct Password Entered: " . ($correct_password_entered ? 'true' : 'false');

?>

<div class="main-wrapper">		
		<section class="about-me-section px-3 p-lg-5 theme-bg-light">
			<div class="container">
				<div class="profile-teaser row">
					<div class="col-md-12">
						<h2 class="name font-weight-bold mb-1">David Hicka</h2>
						<div class="tagline mb-3">Software Engineer | Front-end Developer | UI/UX Designer</div>
						<div class="bio mb-4">Seasoned Software Engineer and accomplished UI/UX Designer with a rich background in advancing and innovating 
							technology across various sectors. With extensive experience in software development and design, I am committed to driving 
							significant enhancements in tech systems. Skilled in optimizing processes and leveraging my expertise to deliver highly efficient 
							and impactful services, I aim to contribute meaningful advancements in any technological environment.
						</div><!--//bio-->
						<div class="mb-1">
							<a class="btn btn-primary me-2 mb-3" href="<?php echo home_url('/projects/'); ?>">
								<i class="fas fa-arrow-alt-circle-right me-2"></i>
								<span class="d-none d-md-inline">View</span> projects
							</a>
							<a class="btn btn-secondary mb-3" href="<?php echo home_url('/resume/'); ?>">
								<i class="fas fa-file-alt me-2"></i>
								<span class="d-none d-md-inline">View</span> Resume
							</a>
						</div>
					</div><!--//col-->
				</div>
			</div>
		</section><!--//about-me-section-->
		
		<section class="overview-section px-3 p-lg-5">
    <div class="container">
        <h2 class="section-title font-weight-bold mb-3">What I Do</h2>
        <div class="section-intro mb-5">
            I am David Hicka, a Software Engineer and UI/UX Designer with extensive experience in software development and design. My career is marked by my role in driving significant enhancements in tech systems across various sectors. My work focuses on optimizing processes and leveraging cutting-edge technologies to deliver highly efficient and impactful services. Explore my <a class="text-link" href="resume.html">online resume</a> and <a class="text-link" href="projects.html">project projects</a> to learn more about my contributions to technological advancements.
        </div>
        <div class="row">
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fab fa-js-square"></i></div>
                    <h3 class="item-title">JavaScript, jQuery</h3>
                    <div class="item-desc">
                        Engineered advanced functionalities with dynamic interaction using JavaScript and jQuery, emphasizing robust front-end development.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fab fa-react pr-2"></i><i class="fab fa-angular"></i></div>
                    <h3 class="item-title">React.js & Angular</h3>
                    <div class="item-desc">
                        Expertise in creating highly responsive single-page applications using React.js and Angular, focusing on modular and scalable code architecture.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fab fa-html5 me-2"></i><i class="fab fa-css3-alt me-2"></i><i class="fab fa-sass"></i></div>
                    <h3 class="item-title">HTML5, CSS3 & Sass</h3>
                    <div class="item-desc">
                        Advanced knowledge in developing responsive and accessible websites using HTML5, CSS3, and Sass for enhanced styling and design flexibility.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
					<div class="item-icon">
						<i class="fab fa-bootstrap mr-2"></i>
						<i class="fab fa-uikit"></i>
					</div>
                    <h3 class="item-title">Bootstrap & Material-UI</h3>
                    <div class="item-desc">
                        Proficient in using Bootstrap and Material-UI frameworks to design and implement responsive and aesthetically pleasing user interfaces.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fas fa-database"></i></div>
                    <h3 class="item-title">T-SQL & SSRS</h3>
                    <div class="item-desc">
                        Spearheaded the creation of insightful reports using T-SQL and SSRS, significantly improving data accessibility and decision-making processes.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fas fa-code"></i></div>
                    <h3 class="item-title">ASP.NET & C#</h3>
                    <div class="item-desc">
                        Developed robust web applications and services using ASP.NET and C#, focusing on backend functionalities and business logic implementation.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fab fa-php"></i></div>
                    <h3 class="item-title">PHP & WordPress</h3>
                    <div class="item-desc">
                        Crafted WordPress themes using PHP, elevating the visual and functional aspects of company marketing websites.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
					<div class="item-icon"><i class="fas fa-vector-square"></i></div>
                    <h3 class="item-title">Adobe XD & Figma</h3>
                    <div class="item-desc">
                        Utilized Adobe XD and Figma for high-fidelity prototyping and UI/UX design, ensuring intuitive and user-centered designs.
                    </div>
                </div>
            </div>
            <div class="item col-12 col-md-4">
                <div class="item-inner">
                    <div class="item-icon"><i class="fas fa-paint-brush"></i></div>
                    <h3 class="item-title">Adobe Creative Cloud</h3>
                    <div class="item-desc">
                        Proficient with the Adobe Creative Cloud suite, including Photoshop, Illustrator, and Premiere, for professional-grade graphics and multimedia content.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
				
		<div class="container"><hr></div>
		
		<?php if ($correct_password_entered): ?>
		<section class="featured-section p-3 p-lg-5">
			<div class="container">
				<h2 class="section-title font-weight-bold mb-5">Featured Projects</h2>
				<div class="row">
					<?php
					// WP_Query arguments to retrieve posts from the "portfolio" section
					$args = array(
						'post_type'      => 'portfolio',
						'post_status'    => 'publish',
						'posts_per_page' => 4, // Adjust as needed
						'order'          => 'DESC',
						'orderby'        => 'date',
					);

					// The Query
					$portfolio_query = new WP_Query($args);

					// The Loop
					if ($portfolio_query->have_posts()) :
						while ($portfolio_query->have_posts()) :
							$portfolio_query->the_post();
							?>
							<div class="col-md-6 mb-5">
								<div class="card project-card">
									<!-- Dynamically get the post thumbnail if you have one -->
									<?php if (has_post_thumbnail()) : ?>
										<img class="card-img-top" src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title_attribute(); ?>">
									<?php endif; ?>
									<div class="card-body">
										<h5 class="card-title"><a class="theme-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
										<p class="card-text"><?php the_excerpt(); ?></p>
										<?php
										$client = get_post_meta(get_the_ID(), 'client', true);
										if (!empty($client)) {
											echo '<p class="card-text"><small class="text-muted">Client: ' . esc_html($client) . '</small></p>';
										}
										?>
									</div>
									<div class="card-footer">
										<a class="btn btn-secondary" href="<?php the_permalink(); ?>">
											<i class="fas fa-eye me-2"></i>View Case Study
										</a>
									</div>
								</div><!--//card-->
							</div><!--//col-->
							<?php
						endwhile;
						// Restore original Post Data
						wp_reset_postdata();
					else :
						// No posts found
						echo '<p>No projects found.</p>';
					endif;
					?>
				</div><!--//row-->
				<div class="text-center py-3">
					<a href="<?php echo home_url('/projects/'); ?>" class="btn btn-primary">
						<i class="fas fa-arrow-alt-circle-right me-2"></i>View All Projects
					</a>
				</div>
			</div><!--//container-->
		</section><!--//featured-section-->
		
		<?php else: ?>
		<!-- Message prompting the user to visit the projects page and enter the password -->
		<!-- <section class="password-required-message p-3 p-lg-5">
			<div class="container text-center">
				<p>Please visit the <a href="<?php echo home_url('/projects/'); ?>">projects page</a> and enter the password to view featured projects.</p>
			</div>
		</section> -->
	<?php endif; ?>
		
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
