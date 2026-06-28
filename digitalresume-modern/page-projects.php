<?php
/**
 * page-projects.php — "Selected Work" projects showcase.
 *
 * Implemented from the Claude Design "Selected Work.dc.html". Self-contained,
 * scoped styles (won't affect the rest of the site), responsive, and served
 * behind the site's existing login gate via get_header().
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

$pj_img = get_stylesheet_directory_uri() . '/assets/img/projects';
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
		<div class="pj-kicker"><span class="rule"></span>Selected Work</div>
		<div class="pj-titlewrap">
			<span class="pj-num">02</span>
			<h1 class="pj-h1">Projects</h1>
		</div>
		<p class="pj-lead">Two decades of shipping production software — from SOC&nbsp;2 / PCI&nbsp;DSS financial platforms and mission-critical defense training systems to a solo-built AI SaaS loved by 2,000+ customers.</p>
		<div class="pj-divider"></div>
	</header>

	<!-- FEATURED — DEAL PACK WEB -->
	<section class="pj-shell pj-section">
		<div class="pj-feature">
			<div class="pj-feature-media">
				<img src="<?php echo esc_url( $pj_img ); ?>/project-1.jpg" alt="Deal Pack Web">
				<span class="pj-flag">★ Flagship</span>
			</div>
			<div>
				<div class="pj-eyebrow">Lead Engineer · ABCoA · 2017 – Present</div>
				<h2 class="pj-h2">Deal Pack Web</h2>
				<p class="pj-desc">A financial-management &amp; loan-servicing platform for automotive dealerships and subprime finance companies — origination, payments, collections, accounting and CRM. Shipped inside a <strong>SOC&nbsp;2 Type&nbsp;II</strong> and <strong>PCI&nbsp;DSS</strong> compliant environment serving a national customer base.</p>
				<div class="pj-tags">
					<span class="pj-tag">C#</span>
					<span class="pj-tag">ASP.NET</span>
					<span class="pj-tag">T-SQL</span>
					<span class="pj-tag">Highcharts</span>
					<span class="pj-tag">SSRS</span>
				</div>
			</div>
		</div>
	</section>

	<!-- PROJECT GRID -->
	<section class="pj-shell pj-section">
		<div class="pj-grid">

			<article class="pj-card">
				<div class="pj-card-media"><img src="<?php echo esc_url( $pj_img ); ?>/project-2.jpg" alt="Defense training systems"></div>
				<div class="pj-card-body">
					<div class="pj-card-head">
						<h3 class="pj-card-title">Aircrew Training Systems</h3>
						<span class="pj-card-date">2004 – 2014</span>
					</div>
					<div class="pj-card-role">Multimedia Engineer · L3 Communications</div>
					<p class="pj-card-desc">Interactive Level-3 courseware and XML-driven MFD cockpit simulators for CV-22 Osprey, MV-22 and MH-60R programs — built against official military documentation with pilot SMEs.</p>
					<div class="pj-card-tags">
						<span class="pj-card-tag">XML</span>
						<span class="pj-card-tag">JavaScript</span>
						<span class="pj-card-tag">Simulation</span>
					</div>
				</div>
			</article>

			<article class="pj-card">
				<div class="pj-card-media"><img src="<?php echo esc_url( $pj_img ); ?>/project-3.jpg" alt="Florida Blue member tools"></div>
				<div class="pj-card-body">
					<div class="pj-card-head">
						<h3 class="pj-card-title">Florida Blue · Member Tools</h3>
						<span class="pj-card-date">2017</span>
					</div>
					<div class="pj-card-role">UI Developer · Health Insurance</div>
					<p class="pj-card-desc">WCAG 2.0 AA accessibility upgrades across benefit-management tools, plus a proxy integration bridging FloridaBlue.com with HealthCare.gov for individual-market exchange enrollment.</p>
					<div class="pj-card-tags">
						<span class="pj-card-tag">JavaScript</span>
						<span class="pj-card-tag">WCAG 2.0 AA</span>
						<span class="pj-card-tag">Integration</span>
					</div>
				</div>
			</article>

			<article class="pj-card">
				<div class="pj-card-media"><img src="<?php echo esc_url( $pj_img ); ?>/project-4.jpg" alt="Enfusion real-time analytics"></div>
				<div class="pj-card-body">
					<div class="pj-card-head">
						<h3 class="pj-card-title">Enfusion · Real-time Analytics</h3>
						<span class="pj-card-date">2015 – 2017</span>
					</div>
					<div class="pj-card-role">UI Developer · OSI</div>
					<p class="pj-card-desc">A real-time video-analytics application interface built with React.js and Material-UI — fast, data-dense dashboards designed for at-a-glance decision making.</p>
					<div class="pj-card-tags">
						<span class="pj-card-tag">React.js</span>
						<span class="pj-card-tag">Material-UI</span>
					</div>
				</div>
			</article>

			<article class="pj-card">
				<div class="pj-card-media"><img src="<?php echo esc_url( $pj_img ); ?>/cyclcrm.jpg" alt="cyclCRM"></div>
				<div class="pj-card-body">
					<div class="pj-card-head">
						<h3 class="pj-card-title">cyclCRM</h3>
						<span class="pj-card-date">2017 – 2019</span>
					</div>
					<div class="pj-card-role">Frontend &amp; UI/UX · ABCoA</div>
					<p class="pj-card-desc">End-to-end interface design and frontend build for a customer-relationship platform — engineered for clarity and speed in the hands of daily operators.</p>
					<div class="pj-card-tags">
						<span class="pj-card-tag">JavaScript</span>
						<span class="pj-card-tag">UI/UX</span>
					</div>
				</div>
			</article>

		</div>
	</section>

	<!-- FEATURED — TOON & TAILS (dark band) -->
	<section class="pj-dark">
		<div class="pj-dark-inner">
			<div class="pj-dark-kicker"><span class="rule"></span>Featured · Founder &amp; Engineer · Public Product</div>
			<div class="pj-dark-titlewrap">
				<h2 class="pj-dark-h2">Toon &amp; Tails</h2>
				<a href="https://toonandtails.com" target="_blank" rel="noopener" class="pj-dark-link">toonandtails.com ↗</a>
			</div>
			<p class="pj-dark-lead">A production AI SaaS I built and launched solo — it turns a pet photo into a custom cartoon portrait in about a minute, then prints it on mugs, canvas, ornaments and more. Loved by <strong>2,000+ pet owners</strong>.</p>

			<div class="pj-gallery">
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/dog-golden-retreiver-preview.webp" alt="Golden Retriever" loading="lazy"></div>
					<figcaption>Golden Retriever · 3D</figcaption>
				</figure>
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/french-bulldog-preview.webp" alt="French Bulldog" loading="lazy"></div>
					<figcaption>French Bulldog</figcaption>
				</figure>
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/cat-short-haired-preview.webp" alt="Black Cat" loading="lazy"></div>
					<figcaption>Black Cat · Cute</figcaption>
				</figure>
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/scarlet-macaw-preview.webp" alt="Scarlet Macaw" loading="lazy"></div>
					<figcaption>Scarlet Macaw · 3D</figcaption>
				</figure>
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/axolotl-preview.webp" alt="Axolotl" loading="lazy"></div>
					<figcaption>Axolotl · Watercolor</figcaption>
				</figure>
				<figure>
					<div class="frame"><img src="https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/lop-rabbit-preview.webp" alt="Lop Rabbit" loading="lazy"></div>
					<figcaption>Lop Rabbit · 2D</figcaption>
				</figure>
			</div>

			<div class="pj-dark-tags">
				<span class="pj-dark-tag">Next.js 15</span>
				<span class="pj-dark-tag">React 19</span>
				<span class="pj-dark-tag">OpenAI gpt-image-2</span>
				<span class="pj-dark-tag">Firebase</span>
				<span class="pj-dark-tag">Stripe</span>
				<span class="pj-dark-tag">Printful</span>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
