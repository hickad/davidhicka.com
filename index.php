<?php
/**
 * Template Name: About
 *
 * @package DigitalResume
 */

get_header();

$correct_password_entered = isset($_SESSION['correct_password_entered']) && $_SESSION['correct_password_entered'];

?>

<div class="main-wrapper">
	<section class="about-me-section px-3 p-lg-5 theme-bg-light">
		<div class="container">
			<div class="profile-teaser row">
				<div class="col-md-12">
					<h2 class="name font-weight-bold mb-1">David Hicka</h2>
					<div class="tagline mb-3">Software Engineer | Front-end Developer | UI/UX Designer</div>
					<div class="mb-1">
						<a class="btn btn-secondary mb-3" href="<?php echo esc_url( home_url( '/resume/' ) ); ?>">
							<i class="fas fa-file-alt me-2"></i>
							<span class="d-none d-md-inline">View</span> Resume
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="overview-section px-3 p-lg-5">
		<div class="container">
			<h2 class="section-title font-weight-bold mb-3">What I Do</h2>
			<div class="row">
				<div class="item col-12 col-md-4">
					<div class="item-inner">
						<div class="item-icon"><i class="fab fa-js-square"></i></div>
						<h3 class="item-title">JavaScript, jQuery</h3>
						<div class="item-desc">
							Engineered advanced functionalities with dynamic interaction using JavaScript and jQuery.
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
							Expert at using Bootstrap and Material-UI frameworks to design and implement responsive and aesthetically pleasing user interfaces.
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
							Developed robust web applications and services using ASP.NET and C#.
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
							Expert knowledge of Adobe Creative Cloud suite, including Photoshop, and Illustrator for professional-grade graphics and multimedia content.
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
				$args = array(
					'post_type'      => 'portfolio',
					'post_status'    => 'publish',
					'posts_per_page' => 4,
					'order'          => 'DESC',
					'orderby'        => 'date',
				);

				$portfolio_query = new WP_Query($args);

				if ($portfolio_query->have_posts()) :
					while ($portfolio_query->have_posts()) :
						$portfolio_query->the_post();
						?>
						<div class="col-md-6 mb-5">
							<div class="card project-card">
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
					wp_reset_postdata();
				else :
					echo '<p>No projects found.</p>';
				endif;
				?>
			</div><!--//row-->
			<div class="text-center py-3">
				<a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>" class="btn btn-primary">
					<i class="fas fa-arrow-alt-circle-right me-2"></i>View All Projects
				</a>
			</div>
		</div><!--//container-->
	</section><!--//featured-section-->

	<?php else: ?>
	<!-- Optionally include a section to prompt the user for a password -->
	<?php endif; ?>

	<div class="container"><hr></div>

	<section class="d-none latest-blog-section p-3 p-lg-5">
		<div class="container">
			<h2 class="section-title font-weight-bold mb-5">Latest Blog Posts</h2>
			<div class="row">
				<?php
				$args = array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'order'          => 'DESC',
					'orderby'        => 'date',
				);

					$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						?>
						<div class="col-md-4 mb-5">
							<div class="card blog-post-card">
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
					echo '<p>No posts found.</p>';
				}

				wp_reset_postdata();
				?>
			</div><!--//row-->
			<div class="text-center py-3">
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-primary">
					<i class="fas fa-arrow-alt-circle-right me-2"></i>View Blog
				</a>
			</div>
		</div><!--//container-->
	</section>

</div><!--//main-wrapper-->

<?php
get_footer();
