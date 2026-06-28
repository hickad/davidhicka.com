<?php
/**
 * LinkedIn copy-paste view — Dashboard → Résumé → LinkedIn.
 *
 * LinkedIn has no API to edit a profile, so this formats the résumé data into
 * LinkedIn's sections (Headline, About, each Position, Skills) with character
 * counts against LinkedIn's limits and a Copy button per field. Keeping LinkedIn
 * current becomes copy-paste from the same single source.
 *
 * Defaults to the "general" variant (LinkedIn is one public profile) but any
 * audience can be selected.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** LinkedIn field limits (chars). */
function dhm_linkedin_limits() {
	return array(
		'headline' => 220,
		'about'    => 2600,
		'position' => 2000,
		'skills'   => 100, // per individual skill name
	);
}

function dhm_resume_linkedin_menu() {
	add_submenu_page(
		'dhm-resume',
		'LinkedIn',
		'LinkedIn',
		'manage_options',
		'dhm-resume-linkedin',
		'dhm_resume_linkedin_page'
	);
}
add_action( 'admin_menu', 'dhm_resume_linkedin_menu' );

/** Headline text (uses the résumé title line). */
function dhm_linkedin_headline( $r ) {
	return $r['title_line'];
}

/** About text: summary, plus a Core skills line when it fits the limit. */
function dhm_linkedin_about( $r ) {
	$limit = dhm_linkedin_limits()['about'];
	$about = $r['summary'];
	if ( '' !== trim( (string) $r['keywords'] ) ) {
		$extra = "\n\nCore skills: " . $r['keywords'];
		if ( strlen( $about . $extra ) <= $limit ) {
			$about .= $extra;
		}
	}
	return $about;
}

/** One position's description block (bullets as • lines, plain text). */
function dhm_linkedin_position_desc( $job ) {
	$lines = array();
	foreach ( $job['bullets'] as $b ) {
		$lines[] = '• ' . $b;
	}
	return implode( "\n", $lines );
}

/** Skills as a comma list (deduped) from skills items + keywords. */
function dhm_linkedin_skills( $r ) {
	$out  = array();
	$seen = array();
	$push = function ( $csv ) use ( &$out, &$seen ) {
		foreach ( explode( ',', (string) $csv ) as $s ) {
			$s = trim( $s );
			$k = strtolower( $s );
			if ( '' !== $s && ! isset( $seen[ $k ] ) ) {
				$seen[ $k ] = true;
				$out[]      = $s;
			}
		}
	};
	foreach ( $r['skills'] as $grp ) {
		$push( $grp['items'] );
	}
	$push( $r['keywords'] );
	return implode( ', ', $out );
}

/** A copyable field card: label, char count vs limit, readonly box, Copy button. */
function dhm_linkedin_field( $id, $label, $value, $limit = 0, $rows = 3, $note = '' ) {
	$len    = strlen( $value );
	$over   = $limit && $len > $limit;
	$countc = $over ? '#b32d2e' : '#646970';
	?>
	<div class="dhm-li-card">
		<div class="dhm-li-head">
			<strong><?php echo esc_html( $label ); ?></strong>
			<?php if ( $limit ) : ?>
				<span class="dhm-li-count" style="color:<?php echo esc_attr( $countc ); ?>">
					<?php echo (int) $len; ?> / <?php echo (int) $limit; ?><?php echo $over ? ' — over limit' : ''; ?>
				</span>
			<?php endif; ?>
			<button type="button" class="button button-small dhm-li-copy" data-target="<?php echo esc_attr( $id ); ?>">Copy</button>
		</div>
		<?php if ( $note ) : ?><p class="description"><?php echo esc_html( $note ); ?></p><?php endif; ?>
		<textarea id="<?php echo esc_attr( $id ); ?>" rows="<?php echo (int) $rows; ?>" readonly class="large-text code"><?php echo esc_textarea( $value ); ?></textarea>
	</div>
	<?php
}

function dhm_resume_linkedin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$auds = dhm_resume_audiences();
	$aud  = isset( $_GET['aud'] ) ? sanitize_key( wp_unslash( $_GET['aud'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $aud, $auds, true ) ) {
		$aud = 'general';
	}
	$r        = dhm_resume_get( $aud );
	$base_url = admin_url( 'admin.php?page=dhm-resume-linkedin' );
	$labels   = array(
		'finance'    => 'Finance',
		'defense'    => 'Defense',
		'healthcare' => 'Healthcare',
		'general'    => 'General',
	);
	?>
	<div class="wrap">
		<h1>LinkedIn</h1>
		<p>LinkedIn can't be edited by an API, so this turns your résumé into copy-paste blocks that match LinkedIn's sections and character limits. Edit content under <a href="<?php echo esc_url( admin_url( 'admin.php?page=dhm-resume' ) ); ?>">Résumé</a>; copy from here into LinkedIn. LinkedIn is one public profile — <strong>General</strong> is the recommended source.</p>

		<h2 class="nav-tab-wrapper">
			<?php foreach ( $auds as $a ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'aud', $a, $base_url ) ); ?>"
					class="nav-tab <?php echo ( $a === $aud ) ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $labels[ $a ] ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<?php if ( ! dhm_resume_has_content( $r ) ) : ?>
			<div class="notice notice-warning inline"><p>This variant has no content yet. Add it under <a href="<?php echo esc_url( admin_url( 'admin.php?page=dhm-resume&aud=' . $aud ) ); ?>">Résumé → <?php echo esc_html( $labels[ $aud ] ); ?></a> first.</p></div>
			</div>
			<?php
			return;
		endif;
		$limits = dhm_linkedin_limits();
		?>

		<h2>Headline</h2>
		<?php dhm_linkedin_field( 'li-headline', 'Headline', dhm_linkedin_headline( $r ), $limits['headline'], 2 ); ?>

		<h2>About</h2>
		<?php dhm_linkedin_field( 'li-about', 'About', dhm_linkedin_about( $r ), $limits['about'], 10 ); ?>

		<h2>Experience</h2>
		<p class="description">For each position, type the title/company/dates into LinkedIn, then paste the description.</p>
		<?php foreach ( $r['experience'] as $i => $job ) : ?>
			<?php
			$meta = array_filter( array( $job['dates'], $job['location'] ) );
			$head = trim( $job['role'] . ( ( $job['role'] && $job['company'] ) ? ' — ' : '' ) . $job['company'] );
			if ( $meta ) {
				$head .= '  (' . implode( ' · ', $meta ) . ')';
			}
			dhm_linkedin_field( 'li-exp-' . $i, $head, dhm_linkedin_position_desc( $job ), $limits['position'], 5, 'Description' );
			?>
		<?php endforeach; ?>

		<?php if ( ! empty( $r['projects'] ) ) : ?>
			<h2>Projects</h2>
			<?php foreach ( $r['projects'] as $i => $job ) : ?>
				<?php
				$head = trim( $job['role'] . ( ( $job['role'] && $job['company'] ) ? ' — ' : '' ) . $job['company'] );
				dhm_linkedin_field( 'li-proj-' . $i, $head, dhm_linkedin_position_desc( $job ), $limits['position'], 4, 'Description' );
				?>
			<?php endforeach; ?>
		<?php endif; ?>

		<h2>Skills</h2>
		<?php dhm_linkedin_field( 'li-skills', 'Skills', dhm_linkedin_skills( $r ), 0, 4, 'Add these on LinkedIn one at a time (it allows up to 50; pin your top 3).' ); ?>

		<?php if ( $r['education'] ) : ?>
			<h2>Education</h2>
			<?php dhm_linkedin_field( 'li-edu', 'Education', $r['education'], 0, 2 ); ?>
		<?php endif; ?>
	</div>

	<style>
		.dhm-li-card { background:#fff; border:1px solid #dcdcde; border-radius:6px; padding:12px 14px; margin:0 0 14px; max-width:820px; }
		.dhm-li-head { display:flex; align-items:center; gap:12px; margin-bottom:6px; }
		.dhm-li-head strong { flex:1; }
		.dhm-li-count { font-size:12px; font-variant-numeric:tabular-nums; }
		.dhm-li-card textarea { margin-top:6px; }
	</style>

	<script>
	( function () {
		document.addEventListener( 'click', function ( e ) {
			if ( ! e.target.classList || ! e.target.classList.contains( 'dhm-li-copy' ) ) { return; }
			var ta = document.getElementById( e.target.getAttribute( 'data-target' ) );
			if ( ! ta ) { return; }
			var done = function () { var t = e.target; var o = t.textContent; t.textContent = 'Copied'; setTimeout( function () { t.textContent = o; }, 1200 ); };
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( ta.value ).then( done, function () { ta.select(); document.execCommand( 'copy' ); done(); } );
			} else {
				ta.select(); document.execCommand( 'copy' ); done();
			}
		} );
	} )();
	</script>
	<?php
}
