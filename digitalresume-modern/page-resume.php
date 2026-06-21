<?php
/**
 * page-resume.php — résumé page in the editorial "sheet" treatment.
 * The experience timeline + skills below are hard-coded here for fidelity;
 * swap to ACF / the page editor if you prefer to manage them in wp-admin.
 *
 * @package DigitalResume
 */
get_header();
?>

<main>
	<section class="dh-hero dh-shell" style="padding-block:4rem 2.5rem;">
		<span class="dh-kicker">Curriculum Vitae</span>
		<h1 class="dh-hero-name" style="font-size:clamp(2.5rem,5vw,4rem);">Résumé</h1>
		<div class="dh-actions">
			<a class="btn btn-primary" href="<?php echo esc_url( digitalresume_audience_content( 'resume' ) ); ?>">
				<i class="fas fa-arrow-down me-2"></i>Download PDF (<?php echo esc_html( digitalresume_audience_label() ); ?>)
			</a>
		</div>
	</section>

	<section class="dh-section dh-shell" style="border-top:none;padding-top:0;">
		<article class="dh-resume">

			<div class="dh-section-head">
				<span class="dh-section-num">01</span>
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

			<div class="dh-section-head" style="margin-top:3rem;">
				<span class="dh-section-num">02</span>
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
		</article>
	</section>
</main>

<?php get_footer(); ?>
