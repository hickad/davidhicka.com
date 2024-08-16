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
	<section class="about-me-section px-3 px-lg-5 pt-lg-5 pb-lg-3 theme-bg-light">
		<div class="container">
			<div class="profile-teaser row" data-aos="fade-in">
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



	<?php
	function generateSkillItem($iconHtml, $title, $percentage, $description) {
		return <<<HTML
		<div class="col-12 col-md-6 col-lg-4">
			<div class="item-inner mb-4" data-aos="fade-up">
				<div class="item-icon mb-1">
					$iconHtml
				</div>
				<h3 class="item-title">$title</h3>
				<div class="input-group input-group-sm mb-2 mt-3">
					<span class="input-group-text small">Proficiency</span>
					<div class="progress flex-fill form-control">
						<div class="progress-bar" data-percentage="$percentage"><strong>$percentage%</strong></div>
					</div>
				</div>
				<p>$description</p>
			</div>
		</div>
	HTML;
	}

	$skills = [
		[
			'iconHtml' => '<i class="fab fa-js-square"></i>',
			'title' => 'JavaScript & jQuery',
			'percentage' => 100,
			'description' => 'Engineered advanced functionalities with dynamic interaction using JavaScript and jQuery.'
		],
		[
			'iconHtml' => '<i class="fab fa-react"></i> <i class="fab fa-angular"></i>',
			'title' => 'React.js & Angular',
			'percentage' => 75,
			'description' => 'Expertise in creating highly responsive single-page applications using React.js and Angular, focusing on modular and scalable code architecture.'
		],
		[
			'iconHtml' => '<i class="fab fa-html5"></i> <i class="fab fa-css3-alt"></i> <i class="fab fa-sass"></i>',
			'title' => 'HTML5, CSS3 & Sass',
			'percentage' => 100,
			'description' => 'Advanced knowledge in developing responsive and accessible websites using HTML5, CSS3, and Sass for enhanced styling and design flexibility.'
		],
		[
			'iconHtml' => '<i class="fab fa-bootstrap"></i> <i class="fab fa-uikit"></i>',
			'title' => 'Bootstrap & Material-UI',
			'percentage' => 100,
			'description' => 'Expert at using Bootstrap and Material-UI frameworks to design and implement responsive and aesthetically pleasing user interfaces.'
		],
		[
			'iconHtml' => '<i class="fas fa-database"></i>',
			'title' => 'T-SQL & SSRS',
			'percentage' => 70,
			'description' => 'Leverage expertise in T-SQL and SSRS to develop detailed, insightful reports, enhancing data accessibility and supporting strategic decision-making.'
		],
		[
			'iconHtml' => '<i class="fas fa-code"></i>',
			'title' => 'ASP.NET & C#',
			'percentage' => 75,
			'description' => 'Developed robust web applications and services using ASP.NET and C#.'
		],
		[
			'iconHtml' => '<i class="fab fa-php"></i>',
			'title' => 'PHP & WordPress',
			'percentage' => 85,
			'description' => 'Crafted WordPress themes using PHP, elevating the visual and functional aspects of company marketing websites.'
		],
		[
			'iconHtml' => '<i class="fas fa-vector-square"></i>',
			'title' => 'Adobe XD & Figma',
			'percentage' => 100,
			'description' => 'Utilized Adobe XD and Figma for high-fidelity prototyping and UI/UX design, ensuring intuitive and user-centered designs.'
		],
		[
			'iconHtml' => '<i class="fas fa-paint-brush"></i>',
			'title' => 'Adobe Creative Cloud',
			'percentage' => 100,
			'description' => 'Expert knowledge of Adobe Creative Cloud suite, including Photoshop, and Illustrator for professional-grade graphics and multimedia content.'
		]
	];
	?>

	<section class="overview-section px-3 p-lg-5 pt-lg-4">
		<div class="container">
			<h2 class="section-title font-weight-bold mb-3" data-aos="fade-up">What I Do</h2>
			<div class="row">
				<?php
				foreach ($skills as $skill) {
					echo generateSkillItem($skill['iconHtml'], $skill['title'], $skill['percentage'], $skill['description']);
				}
				?>
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
