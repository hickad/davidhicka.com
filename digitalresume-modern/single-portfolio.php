<?php
/**
 * single-portfolio.php — styled project detail page (child theme override).
 *
 * Renders one Portfolio project using dhm_project_resolve(): back-to-projects
 * button, eyebrow, title, hero image, the editor content (with excerpt
 * fallback), tech tags, external link and gallery. Scoped styles match the
 * Selected Work design; served behind the site login gate via get_header().
 *
 * @package DigitalResumeModern
 */
get_header();

if ( post_password_required() ) {
	echo '<section class="dh-section dh-shell">' . get_the_password_form() . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	get_footer();
	return;
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Space+Grotesk:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap">

<style>
	.pj-detail { font-family:'Roboto',sans-serif; color:#16181d; background:#fff; }
	.pj-detail *, .pj-detail *::before, .pj-detail *::after { box-sizing:border-box; }
	.pj-detail .pj-wrap { max-width:880px; margin:0 auto; padding:2.5rem 1.5rem 4rem; }
	.pj-detail .pj-back { display:inline-flex; align-items:center; gap:.4rem; font-family:'Roboto Mono',monospace; font-size:.78rem; color:#54b689; text-decoration:none; }
	.pj-detail .pj-back:hover { color:#3d8a66; }
	.pj-detail .pj-eyebrow { font-family:'Roboto Mono',monospace; font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:#969ca4; margin-top:2rem; }
	.pj-detail .pj-title { font-family:'Space Grotesk',sans-serif; font-size:2.6rem; font-weight:600; letter-spacing:-.03em; line-height:1.05; margin:.5rem 0 0; text-wrap:balance; }
	.pj-detail .pj-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1.25rem; }
	.pj-detail .pj-tag { font-family:'Roboto Mono',monospace; font-size:.74rem; font-weight:700; color:#3d8a66; background:#e6f3ed; padding:.3rem .65rem; border-radius:.75rem; }
	.pj-detail .pj-hero { margin-top:2rem; border-radius:14px; overflow:hidden; border:1px solid #e8e8e4; background:#f4faf7; }
	.pj-detail .pj-hero img { width:100%; height:auto; display:block; }
	.pj-detail .pj-content { font-size:1.05rem; line-height:1.7; color:#2c3238; margin-top:2rem; }
	.pj-detail .pj-content > *:first-child { margin-top:0; }
	.pj-detail .pj-content p { margin:0 0 1.1rem; }
	.pj-detail .pj-content img { max-width:100%; height:auto; border-radius:12px; }
	.pj-detail .pj-content h2, .pj-detail .pj-content h3 { font-family:'Space Grotesk',sans-serif; letter-spacing:-.02em; margin:2rem 0 .6rem; }
	.pj-detail .pj-content a { color:#3d8a66; }
	.pj-detail .pj-visit { display:inline-flex; align-items:center; gap:.5rem; margin-top:2rem; padding:.7rem 1.2rem; background:#54b689; color:#fff; border-radius:10px; font-weight:600; font-size:.95rem; text-decoration:none; }
	.pj-detail .pj-visit:hover { background:#4aa87c; }
	.pj-detail .pj-gallery { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-top:2.5rem; }
	.pj-detail .pj-gallery figure { margin:0; }
	.pj-detail .pj-gallery .frame { aspect-ratio:1/1; border-radius:12px; overflow:hidden; background:#f3f5f9; border:1px solid #e8e8e4; }
	.pj-detail .pj-gallery img { width:100%; height:100%; object-fit:cover; display:block; }
	.pj-detail .pj-gallery figcaption { font-family:'Roboto Mono',monospace; font-size:.62rem; color:#969ca4; margin-top:.5rem; }
	.pj-detail .pj-foot { margin-top:3rem; padding-top:1.5rem; border-top:1px solid #e8e8e4; }
	@media (max-width:600px){
		.pj-detail .pj-title { font-size:2rem; }
		.pj-detail .pj-gallery { grid-template-columns:repeat(2,1fr); }
	}
</style>

<main class="pj-detail">
	<?php
	while ( have_posts() ) :
		the_post();
		$p = function_exists( 'dhm_project_resolve' ) ? dhm_project_resolve( get_post() ) : null;
		?>
		<article class="pj-wrap">
			<a class="pj-back" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">← Back to projects</a>

			<?php if ( $p && $p['eyebrow'] ) : ?>
				<div class="pj-eyebrow"><?php echo esc_html( $p['eyebrow'] ); ?></div>
			<?php endif; ?>

			<h1 class="pj-title"><?php the_title(); ?></h1>

			<?php if ( $p && $p['tags'] ) : ?>
				<div class="pj-tags">
					<?php foreach ( $p['tags'] as $t ) : ?>
						<span class="pj-tag"><?php echo esc_html( $t ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $p && $p['image'] ) : ?>
				<div class="pj-hero"><img src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>"></div>
			<?php endif; ?>

			<div class="pj-content">
				<?php
				$content = trim( get_the_content() );
				if ( '' !== $content ) {
					the_content();
				} elseif ( $p && $p['desc'] ) {
					echo '<p>' . esc_html( $p['desc'] ) . '</p>';
				}
				?>
			</div>

			<?php if ( $p && $p['url'] ) : ?>
				<a class="pj-visit" href="<?php echo esc_url( $p['url'] ); ?>" target="_blank" rel="noopener">Visit <?php echo esc_html( preg_replace( '#^https?://#', '', untrailingslashit( $p['url'] ) ) ); ?> ↗</a>
			<?php endif; ?>

			<?php if ( $p && $p['gallery'] ) : ?>
				<div class="pj-gallery">
					<?php foreach ( $p['gallery'] as $g ) : ?>
						<figure>
							<div class="frame"><img src="<?php echo esc_url( $g['url'] ); ?>" alt="<?php echo esc_attr( $g['caption'] ? $g['caption'] : $p['title'] ); ?>" loading="lazy"></div>
							<?php if ( $g['caption'] ) : ?><figcaption><?php echo esc_html( $g['caption'] ); ?></figcaption><?php endif; ?>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="pj-foot">
				<a class="pj-back" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">← Back to projects</a>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php get_footer(); ?>
