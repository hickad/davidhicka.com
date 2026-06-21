<?php
/**
 * front-page.php — single-page home.
 * Hero + Work + Experience + Skills + Contact, linked from the header nav.
 *
 * @package DigitalResume
 */
get_header();
?>

<main>
	<!-- HERO -->
	<section class="dh-hero dh-shell">
		<div class="dh-hero-grid">
			<div class="dh-hero-text">
				<?php $dh_viewer = function_exists( 'dhm_viewer_label' ) ? dhm_viewer_label() : ''; ?>
				<?php if ( $dh_viewer ) : ?>
					<p class="dh-welcome">Prepared for <strong><?php echo esc_html( $dh_viewer ); ?></strong></p>
				<?php endif; ?>
				<span class="dh-kicker">Senior Software Engineer</span>
				<h1 class="dh-hero-name"><?php bloginfo( 'name' ); ?></h1>
				<p class="dh-hero-lead"><?php echo esc_html( digitalresume_audience_content( 'lead' ) ); ?></p>

				<div class="dh-actions">
					<a class="btn btn-primary" href="<?php echo esc_url( digitalresume_audience_content( 'resume' ) ); ?>">
						<i class="fas fa-arrow-down me-2"></i>Download Résumé
					</a>
					<a class="btn btn-ghost" href="#contact">
						Get in touch <i class="fas fa-arrow-right ms-2"></i>
					</a>
				</div>

				<div class="dh-stats">
					<div>
						<div class="dh-stat-num">20<span>+</span></div>
						<div class="dh-stat-label">Years shipping software</div>
					</div>
					<div>
						<div class="dh-stat-num">2,000<span>+</span></div>
						<div class="dh-stat-label">Customers on a product I built solo</div>
					</div>
					<div>
						<div class="dh-stat-num">SOC&nbsp;2<span>·</span>PCI</div>
						<div class="dh-stat-label">Audited environments</div>
					</div>
				</div>
			</div><!-- /.dh-hero-text -->

			<div class="dh-hero-media">
				<?php
				$hero_photo = wp_get_attachment_image(
					119,
					'full',
					false,
					array(
						'class' => 'dh-hero-photo',
						'alt'   => get_bloginfo( 'name' ),
					)
				);
				echo $hero_photo ? $hero_photo : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div><!-- /.dh-hero-grid -->
	</section>

	<!-- WORK -->
	<section class="dh-section dh-shell" id="work">
		<div class="dh-section-head">
			<span class="dh-section-num">01</span>
			<h2 class="dh-section-title">Work</h2>
		</div>
		<div class="dh-cards">
			<?php
			$portfolio = new WP_Query(
				array(
					'post_type'      => 'portfolio',
					'posts_per_page' => 12,
				)
			);

			if ( $portfolio->have_posts() ) :
				while ( $portfolio->have_posts() ) :
					$portfolio->the_post();
					?>
					<article class="dh-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="dh-card-img"><?php the_post_thumbnail( 'large' ); ?></div>
						<?php endif; ?>
						<div class="dh-card-body">
							<h3 class="dh-card-title"><?php the_title(); ?></h3>
							<div class="dh-card-kind"><?php echo esc_html( get_the_date() ); ?></div>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				$fallback = array(
					array( 'Deal Pack Web', 'Lead Engineer · 2019–Present', 'SOC 2 / PCI financial management & loan-servicing platform for dealerships and finance companies.' ),
					array( 'Defense Training Systems', 'L3 · 2004–2014', 'Interactive Level-3 courseware and XML-driven cockpit simulators for AFSOC, USMC & USN programs.' ),
					array( 'Florida Blue · Member Tools', 'UI Developer · 2015–2017', 'WCAG 2.0 AA upgrades and a HealthCare.gov enrollment integration.' ),
					array( 'Toon & Tails', 'Founder & Engineer · 2025', 'Production AI SaaS turning pet photos into cartoon portraits and printed merch. Built solo.' ),
				);
				foreach ( $fallback as $p ) :
					?>
					<article class="dh-card">
						<div class="dh-card-body">
							<h3 class="dh-card-title"><?php echo esc_html( $p[0] ); ?></h3>
							<div class="dh-card-kind"><?php echo esc_html( $p[1] ); ?></div>
							<p><?php echo esc_html( $p[2] ); ?></p>
						</div>
					</article>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</section>

	<!-- EXPERIENCE -->
	<section class="dh-section dh-shell" id="experience">
		<div class="dh-section-head">
			<span class="dh-section-num">02</span>
			<h2 class="dh-section-title">Experience</h2>
		</div>
		<div class="dh-timeline">
			<div class="dh-tl-item">
				<span class="dh-tl-badge">Most relevant</span>
				<h3 class="dh-tl-role">Lead Software Engineer — Advanced Business Computers of America</h3>
				<p class="dh-tl-meta">Oct 2019 – Present · Jacksonville, FL</p>
				<ul>
					<li>Lead engineer on "Deal Pack Web," a SOC 2 Type II &amp; PCI DSS financial management and loan-servicing platform for automotive dealerships and subprime finance companies.</li>
					<li>Built dashboard analytics (JavaScript + Highcharts) on a robust C# Web API; authored 100+ custom T-SQL / SSRS reports.</li>
				</ul>
			</div>
			<div class="dh-tl-item">
				<h3 class="dh-tl-role">Frontend Developer &amp; Designer — ABCoA</h3>
				<p class="dh-tl-meta">Dec 2017 – Nov 2019 · Jacksonville, FL</p>
				<ul>
					<li>Designed and built interfaces for Deal Pack Web, Dealer Sales Tools and cyclCRM; crafted WordPress themes in PHP.</li>
				</ul>
			</div>
			<div class="dh-tl-item muted">
				<h3 class="dh-tl-role">UI Developer — Florida Blue (contract) · OSI</h3>
				<p class="dh-tl-meta">2015 – 2017 · Jacksonville, FL</p>
				<ul>
					<li>WCAG 2.0 AA accessibility upgrades and a HealthCare.gov enrollment integration; real-time analytics UIs in React + Material-UI.</li>
				</ul>
			</div>
			<div class="dh-tl-item muted">
				<h3 class="dh-tl-role">Multimedia / Software Engineer — L3 Technologies</h3>
				<p class="dh-tl-meta">2004 – 2014</p>
				<ul>
					<li>Level-3 interactive training courseware and XML-driven cockpit-instrument simulators for AFSOC, USMC and USN aircrew programs.</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- SKILLS -->
	<section class="dh-section dh-shell" id="skills">
		<div class="dh-section-head">
			<span class="dh-section-num">03</span>
			<h2 class="dh-section-title">Skills</h2>
		</div>
		<div class="dh-skills">
			<?php
			$skills = array( 'C#', 'ASP.NET', 'T-SQL', 'JavaScript', 'TypeScript', 'React 19', 'Next.js 15', 'Angular', 'Node.js', 'Python', 'HTML5 / CSS', 'WCAG', 'Stripe', 'Firebase', 'OpenAI', 'Figma' );
			foreach ( $skills as $s ) {
				echo '<span class="dh-skill">' . esc_html( $s ) . '</span>';
			}
			?>
		</div>
		<p style="font-family:var(--font-mono);font-size:0.78rem;color:var(--ink-3);margin-top:2.5rem;padding-top:1.25rem;border-top:1px solid var(--line);letter-spacing:0.02em;">
			EDUCATION — B.A.Sc., Information Technology Management · Florida State College at Jacksonville
		</p>
	</section>

	<!-- CONTACT -->
	<section class="dh-section dh-shell" id="contact">
		<div class="dh-section-head">
			<span class="dh-section-num">04</span>
			<h2 class="dh-section-title">Contact</h2>
		</div>
		<p class="dh-hero-lead" style="max-width:48ch;font-size:1.15rem;">
			Available for senior engineering roles. Send a message below, or reach me on
			<a href="https://www.linkedin.com/in/davidhicka/">LinkedIn</a>.
		</p>
		<div class="dh-contact-form">
			<?php echo do_shortcode( '[contact-form-7 id="734188d" title="Contact Page Form"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
