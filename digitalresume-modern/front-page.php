<?php
/**
 * front-page.php — the modern editorial home / landing page.
 * Hero + stat strip + "What I bring" + featured product (Toon & Tails).
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
				<span class="dh-kicker">Senior Software Engineer</span>
				<h1 class="dh-hero-name"><?php bloginfo( 'name' ); ?></h1>
				<p class="dh-hero-lead"><?php echo esc_html( digitalresume_audience_content( 'lead' ) ); ?></p>

				<div class="dh-actions">
					<a class="btn btn-primary" href="<?php echo esc_url( digitalresume_audience_content( 'resume' ) ); ?>">
						<i class="fas fa-arrow-down me-2"></i>Download Résumé
					</a>
					<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
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

	<!-- WHAT I BRING -->
	<section class="dh-section dh-shell" id="work">
		<div class="dh-section-head">
			<span class="dh-section-num">01</span>
			<h2 class="dh-section-title">What I bring</h2>
		</div>
		<div class="dh-grid">
			<div class="dh-value">
				<i class="fas fa-shield-halved"></i>
				<h3>Compliance-driven</h3>
				<p>Years shipping inside SOC 2 Type II and PCI DSS audited environments.</p>
			</div>
			<div class="dh-value">
				<i class="fas fa-robot"></i>
				<h3>AI-assisted delivery</h3>
				<p>Active practitioner with Claude, Copilot, Cursor and local LLMs.</p>
			</div>
			<div class="dh-value">
				<i class="fas fa-layer-group"></i>
				<h3>Full-stack range</h3>
				<p>From T-SQL and C# to React 19 and Next.js 15 — shipped solo and in teams.</p>
			</div>
		</div>
	</section>

	<!-- FEATURED: TOON & TAILS -->
	<section class="dh-featured" id="featured">
		<div class="dh-shell">
			<span class="dh-kicker">Featured · Founder &amp; Engineer · public product</span>
			<h2 class="dh-featured-title">Toon &amp; Tails</h2>
			<p class="dh-featured-lead"><?php echo esc_html( digitalresume_audience_content( 'featured' ) ); ?></p>
			<div class="dh-featured-stack">
				<span>Next.js 15</span><span>React 19</span><span>OpenAI gpt-image</span>
				<span>Firebase</span><span>Stripe</span><span>Printful</span>
			</div>
		</div>
	</section>

	<?php
	// Optional: let the page's WP editor content flow in below the fixed sections.
	while ( have_posts() ) :
		the_post();
		if ( trim( get_the_content() ) ) :
			?>
			<section class="dh-section dh-shell"><div class="dh-timeline"><?php the_content(); ?></div></section>
			<?php
		endif;
	endwhile;
	?>
</main>

<?php get_footer(); ?>
