<?php
/**
 * page-projects.php — "Selected Work" projects showcase.
 *
 * Layout/design implemented from the Claude Design "Selected Work.dc.html".
 * Content is managed in wp-admin via the Portfolio post type and rendered by
 * dhm_projects_render() (see inc/projects.php). Self-contained, scoped styles,
 * responsive; served behind the site's existing login gate via get_header().
 *
 * @package DigitalResumeModern
 */
get_header();

// Respect WP password protection on this page (theme's custom form).
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
	.dh-projects { font-family:'Roboto',sans-serif; color:#16181d; background:#fff; }
	.dh-projects *, .dh-projects *::before, .dh-projects *::after { box-sizing:border-box; }
	.dh-projects .pj-shell { max-width:1080px; margin:0 auto; padding-left:6rem; padding-right:6rem; }
	.dh-projects .pj-header { padding-top:5rem; }
	.dh-projects .pj-kicker { display:flex; align-items:center; gap:.75rem; font-family:'Roboto Mono',monospace; font-size:.72rem; font-weight:500; letter-spacing:.16em; text-transform:uppercase; color:#54b689; }
	.dh-projects .pj-kicker .rule { width:26px; height:1px; background:#54b689; }
	.dh-projects .pj-titlewrap { display:flex; align-items:flex-start; gap:1.1rem; margin-top:1.4rem; }
	.dh-projects .pj-num { font-family:'Roboto Mono',monospace; font-size:.9rem; font-weight:500; color:#54b689; margin-top:.5rem; }
	.dh-projects .pj-h1 { font-family:'Space Grotesk',sans-serif; font-size:3rem; font-weight:600; letter-spacing:-.03em; color:#16181d; margin:0; line-height:1.05; text-wrap:balance; }
	.dh-projects .pj-lead { font-size:1.15rem; line-height:1.55; color:#4a5057; max-width:60ch; margin:1.75rem 0 0; text-wrap:pretty; }
	.dh-projects .pj-divider { height:1px; background:#e8e8e4; margin-top:3rem; }
	.dh-projects .pj-section { padding-top:3.5rem; }
	.dh-projects .pj-feature { display:grid; grid-template-columns:1.15fr 1fr; gap:3rem; align-items:center; }
	.dh-projects .pj-feature-media { position:relative; border-radius:12px; overflow:hidden; border:1px solid #e8e8e4; aspect-ratio:4/3; background:#f4faf7; }
	.dh-projects .pj-feature-media img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s ease; }
	.dh-projects .pj-feature:hover .pj-feature-media img { transform:scale(1.03); }
	.dh-projects .pj-flag { position:absolute; top:14px; left:14px; display:inline-flex; align-items:center; gap:.4rem; font-family:'Roboto Mono',monospace; font-size:.62rem; font-weight:500; letter-spacing:.1em; text-transform:uppercase; color:#fff; background:#54b689; padding:.3rem .6rem; border-radius:50rem; }
	.dh-projects .pj-eyebrow { font-family:'Roboto Mono',monospace; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#969ca4; }
	.dh-projects .pj-h2 { font-family:'Space Grotesk',sans-serif; font-size:1.85rem; font-weight:600; letter-spacing:-.02em; color:#16181d; margin:.6rem 0 0; line-height:1.1; }
	.dh-projects .pj-desc { font-size:1rem; line-height:1.6; color:#4a5057; margin:1rem 0 0; text-wrap:pretty; }
	.dh-projects .pj-desc strong { color:#16181d; font-weight:700; }
	.dh-projects .pj-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1.4rem; }
	.dh-projects .pj-tag { font-family:'Roboto Mono',monospace; font-size:.74rem; font-weight:700; color:#3d8a66; background:#e6f3ed; padding:.3rem .65rem; border-radius:.75rem; }
	.dh-projects .pj-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1.75rem; }
	.dh-projects .pj-card { border:1px solid #e8e8e4; border-radius:12px; overflow:hidden; background:#fff; transition:transform .25s ease, box-shadow .25s ease; }
	.dh-projects .pj-card:hover { transform:translateY(-4px); box-shadow:0 16px 34px rgba(0,0,0,.09); }
	.dh-projects .pj-card-link { display:block; text-decoration:none; color:inherit; }
	.dh-projects .pj-feature-media { display:block; text-decoration:none; }
	.dh-projects .pj-h2-link { color:inherit; text-decoration:none; }
	.dh-projects .pj-h2-link:hover { color:#3d8a66; }
	.dh-projects .pj-backbar { padding-top:1.75rem; }
	.dh-projects .pj-back { display:inline-flex; align-items:center; gap:.4rem; font-family:'Roboto Mono',monospace; font-size:.78rem; color:#54b689; text-decoration:none; }
	.dh-projects .pj-back:hover { color:#3d8a66; }
	.dh-projects .pj-card-media { height:200px; overflow:hidden; background:#f3f5f9; }
	.dh-projects .pj-card-media img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s ease; }
	.dh-projects .pj-card:hover .pj-card-media img { transform:scale(1.04); }
	.dh-projects .pj-card-body { padding:1.4rem 1.5rem 1.6rem; }
	.dh-projects .pj-card-head { display:flex; justify-content:space-between; align-items:baseline; gap:.75rem; }
	.dh-projects .pj-card-title { font-family:'Space Grotesk',sans-serif; font-size:1.2rem; font-weight:600; letter-spacing:-.01em; color:#16181d; margin:0; }
	.dh-projects .pj-card-date { font-family:'Roboto Mono',monospace; font-size:.68rem; color:#969ca4; white-space:nowrap; }
	.dh-projects .pj-card-role { font-size:.78rem; color:#3d8a66; font-weight:500; margin:.35rem 0 .8rem; }
	.dh-projects .pj-card-desc { font-size:.9rem; line-height:1.55; color:#4a5057; margin:0 0 1.1rem; }
	.dh-projects .pj-card-tags { display:flex; flex-wrap:wrap; gap:.35rem 1rem; }
	.dh-projects .pj-card-tag { font-family:'Roboto Mono',monospace; font-size:.72rem; color:#969ca4; }
	.dh-projects .pj-dark { margin-top:5rem; background:#16181d; color:#fff; }
	.dh-projects .pj-dark-inner { max-width:1080px; margin:0 auto; padding:5rem 6rem; }
	.dh-projects .pj-dark-kicker { display:flex; align-items:center; gap:.6rem; font-family:'Roboto Mono',monospace; font-size:.72rem; font-weight:500; letter-spacing:.16em; text-transform:uppercase; color:#7ed0a8; }
	.dh-projects .pj-dark-kicker .rule { width:20px; height:1px; background:currentColor; }
	.dh-projects .pj-dark-titlewrap { display:flex; gap:1.25rem; align-items:baseline; flex-wrap:wrap; margin-top:1.5rem; }
	.dh-projects .pj-dark-h2 { font-family:'Space Grotesk',sans-serif; font-size:2.75rem; font-weight:600; letter-spacing:-.03em; margin:0; white-space:nowrap; }
	.dh-projects .pj-dark-link { color:rgba(255,255,255,.7); font-family:'Roboto Mono',monospace; font-size:.85rem; text-decoration:none; }
	.dh-projects .pj-dark-link:hover { color:#fff; }
	.dh-projects .pj-dark-lead { font-size:1.3rem; line-height:1.55; color:rgba(255,255,255,.85); max-width:62ch; margin:1.25rem 0 0; text-wrap:pretty; }
	.dh-projects .pj-dark-lead strong { color:#fff; font-weight:700; }
	.dh-projects .pj-gallery { display:grid; grid-template-columns:repeat(6,1fr); gap:1rem; margin:2.75rem 0 2rem; }
	.dh-projects .pj-gallery figure { margin:0; }
	.dh-projects .pj-gallery .frame { aspect-ratio:1/1; border-radius:12px; overflow:hidden; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); }
	.dh-projects .pj-gallery img { width:100%; height:100%; object-fit:cover; display:block; }
	.dh-projects .pj-gallery figcaption { font-family:'Roboto Mono',monospace; font-size:.62rem; color:rgba(255,255,255,.5); margin-top:.5rem; }
	.dh-projects .pj-dark-tags { display:flex; flex-wrap:wrap; gap:.5rem 1.5rem; }
	.dh-projects .pj-dark-tag { font-family:'Roboto Mono',monospace; font-size:.78rem; color:rgba(255,255,255,.6); }

	@media (max-width:820px){
		.dh-projects .pj-shell { padding-left:1.5rem; padding-right:1.5rem; }
		.dh-projects .pj-header { padding-top:3rem; }
		.dh-projects .pj-h1 { font-size:2.2rem; }
		.dh-projects .pj-feature { grid-template-columns:1fr; gap:1.5rem; }
		.dh-projects .pj-grid { grid-template-columns:1fr; }
		.dh-projects .pj-dark-inner { padding:3rem 1.5rem; }
		.dh-projects .pj-dark-h2 { font-size:2rem; white-space:normal; }
		.dh-projects .pj-gallery { grid-template-columns:repeat(3,1fr); }
	}
	@media (max-width:480px){
		.dh-projects .pj-gallery { grid-template-columns:repeat(2,1fr); }
	}
</style>

<main class="dh-projects">

	<!-- HEADER -->
	<header class="pj-shell pj-header">
		<div class="pj-backbar"><a class="pj-back" href="<?php echo esc_url( home_url( '/' ) ); ?>">← Back to home</a></div>
		<div class="pj-kicker"><span class="rule"></span>Selected Work</div>
		<div class="pj-titlewrap">
			<span class="pj-num">02</span>
			<h1 class="pj-h1">Projects</h1>
		</div>
		<p class="pj-lead">Two decades of shipping production software — from SOC&nbsp;2 / PCI&nbsp;DSS financial platforms and mission-critical defense training systems to a solo-built AI SaaS loved by 2,000+ customers.</p>
		<div class="pj-divider"></div>
	</header>

	<?php
	// Content is managed in wp-admin → Portfolio (see inc/projects.php).
	if ( function_exists( 'dhm_projects_render' ) ) {
		dhm_projects_render();
	}
	?>

</main>

<?php get_footer(); ?>
