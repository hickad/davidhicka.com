<?php
/**
 * Résumé admin — the single editing surface (Dashboard → Résumé).
 *
 * One tab per audience; a structured form with add/remove rows for experience,
 * projects, and skills. Saving writes dhm_resume_data, which drives the website
 * sections and the generated PDF/Word — so one edit updates all three.
 *
 * Save flow mirrors the existing dhm_portfolio_admin_page() pattern (nonce +
 * update_option) for consistency.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function dhm_resume_admin_menu() {
	add_menu_page(
		'Résumé',
		'Résumé',
		'manage_options',
		'dhm-resume',
		'dhm_resume_admin_page',
		'dashicons-media-document',
		59
	);
}
add_action( 'admin_menu', 'dhm_resume_admin_menu' );

/** Turn a textarea's lines into a clean array (one entry per non-empty line). */
function dhm_resume_lines_to_array( $raw ) {
	$out   = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
	foreach ( $lines as $line ) {
		$line = sanitize_text_field( $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return $out;
}

/** Build a sanitized job list from posted experience/projects rows. */
function dhm_resume_parse_jobs( $rows ) {
	$jobs = array();
	if ( ! is_array( $rows ) ) {
		return $jobs;
	}
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$role     = isset( $row['role'] ) ? sanitize_text_field( wp_unslash( $row['role'] ) ) : '';
		$company  = isset( $row['company'] ) ? sanitize_text_field( wp_unslash( $row['company'] ) ) : '';
		$dates    = isset( $row['dates'] ) ? sanitize_text_field( wp_unslash( $row['dates'] ) ) : '';
		$location = isset( $row['location'] ) ? sanitize_text_field( wp_unslash( $row['location'] ) ) : '';
		$bullets  = isset( $row['bullets'] ) ? dhm_resume_lines_to_array( wp_unslash( $row['bullets'] ) ) : array();
		if ( '' === $role && '' === $company && '' === $dates && empty( $bullets ) ) {
			continue; // Skip blank rows.
		}
		$jobs[] = array(
			'role'     => $role,
			'company'  => $company,
			'dates'    => $dates,
			'location' => $location,
			'bullets'  => $bullets,
		);
	}
	return $jobs;
}

/** Build a sanitized record from $_POST. */
function dhm_resume_parse_post() {
	$record = array(
		'name'       => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
		'title_line' => isset( $_POST['title_line'] ) ? sanitize_text_field( wp_unslash( $_POST['title_line'] ) ) : '',
		'location'   => isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '',
		'contact'    => isset( $_POST['contact'] ) ? dhm_resume_lines_to_array( wp_unslash( $_POST['contact'] ) ) : array(),
		'clearance'  => isset( $_POST['clearance'] ) ? sanitize_text_field( wp_unslash( $_POST['clearance'] ) ) : '',
		'summary'    => isset( $_POST['summary'] ) ? sanitize_textarea_field( wp_unslash( $_POST['summary'] ) ) : '',
		'keywords'   => isset( $_POST['keywords'] ) ? implode( ', ', dhm_resume_lines_to_array( str_replace( ',', "\n", wp_unslash( $_POST['keywords'] ) ) ) ) : '',
		'experience' => isset( $_POST['experience'] ) ? dhm_resume_parse_jobs( wp_unslash( $_POST['experience'] ) ) : array(),
		'projects'   => isset( $_POST['projects'] ) ? dhm_resume_parse_jobs( wp_unslash( $_POST['projects'] ) ) : array(),
		'skills'     => array(),
		'education'  => isset( $_POST['education'] ) ? sanitize_text_field( wp_unslash( $_POST['education'] ) ) : '',
	);
	if ( isset( $_POST['skills'] ) && is_array( $_POST['skills'] ) ) {
		foreach ( wp_unslash( $_POST['skills'] ) as $grp ) {
			if ( ! is_array( $grp ) ) {
				continue;
			}
			$cat   = isset( $grp['category'] ) ? sanitize_text_field( $grp['category'] ) : '';
			$items = isset( $grp['items'] ) ? sanitize_text_field( $grp['items'] ) : '';
			if ( '' === $cat && '' === $items ) {
				continue;
			}
			$record['skills'][] = array(
				'category' => $cat,
				'items'    => $items,
			);
		}
	}
	return $record;
}

/** One experience/projects row (also used as the JS clone template). */
function dhm_resume_job_row_html( $field, $index, $job = null ) {
	$job  = $job ? $job : array(
		'role'     => '',
		'company'  => '',
		'dates'    => '',
		'location' => '',
		'bullets'  => array(),
	);
	$name = $field . '[' . $index . ']';
	ob_start();
	?>
	<div class="dhm-row">
		<button type="button" class="button-link dhm-row-remove" aria-label="Remove">&times;</button>
		<div class="dhm-grid">
			<label>Role
				<input type="text" name="<?php echo esc_attr( $name ); ?>[role]" value="<?php echo esc_attr( $job['role'] ); ?>">
			</label>
			<label>Company
				<input type="text" name="<?php echo esc_attr( $name ); ?>[company]" value="<?php echo esc_attr( $job['company'] ); ?>">
			</label>
			<label>Dates
				<input type="text" name="<?php echo esc_attr( $name ); ?>[dates]" value="<?php echo esc_attr( $job['dates'] ); ?>" placeholder="Nov 2019 – Present">
			</label>
			<label>Location
				<input type="text" name="<?php echo esc_attr( $name ); ?>[location]" value="<?php echo esc_attr( $job['location'] ); ?>">
			</label>
		</div>
		<label class="dhm-bullets">Bullets <span class="description">(one per line)</span>
			<textarea name="<?php echo esc_attr( $name ); ?>[bullets]" rows="4"><?php echo esc_textarea( implode( "\n", $job['bullets'] ) ); ?></textarea>
		</label>
	</div>
	<?php
	return ob_get_clean();
}

/** One skills row (also used as the JS clone template). */
function dhm_resume_skill_row_html( $index, $grp = null ) {
	$grp  = $grp ? $grp : array(
		'category' => '',
		'items'    => '',
	);
	$name = 'skills[' . $index . ']';
	ob_start();
	?>
	<div class="dhm-row">
		<button type="button" class="button-link dhm-row-remove" aria-label="Remove">&times;</button>
		<div class="dhm-grid dhm-grid-skills">
			<label>Category
				<input type="text" name="<?php echo esc_attr( $name ); ?>[category]" value="<?php echo esc_attr( $grp['category'] ); ?>" placeholder="Languages &amp; Frameworks">
			</label>
			<label>Items <span class="description">(comma-separated)</span>
				<input type="text" name="<?php echo esc_attr( $name ); ?>[items]" value="<?php echo esc_attr( $grp['items'] ); ?>">
			</label>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function dhm_resume_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$auds = dhm_resume_audiences();
	$aud  = isset( $_GET['aud'] ) ? sanitize_key( wp_unslash( $_GET['aud'] ) ) : 'finance'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $aud, $auds, true ) ) {
		$aud = 'finance';
	}

	$notice = '';
	if ( isset( $_POST['dhm_resume_save'] ) && check_admin_referer( 'dhm_resume_save_' . $aud ) ) {
		$record = dhm_resume_parse_post();
		dhm_resume_save( $aud, $record );
		$notice = 'Résumé saved. The website, PDF and Word download now reflect these changes.';
	}

	$r        = dhm_resume_get( $aud );
	$base_url = admin_url( 'admin.php?page=dhm-resume' );
	$labels   = array(
		'finance'    => 'Finance',
		'defense'    => 'Defense',
		'healthcare' => 'Healthcare',
		'general'    => 'General',
	);
	?>
	<div class="wrap dhm-resume-wrap">
		<h1>Résumé</h1>
		<p>Edit your résumé once here. Each audience has its own version; saving updates the <strong>website</strong>, the <strong>PDF</strong> and the <strong>Word</strong> download for that audience automatically.</p>

		<h2 class="nav-tab-wrapper">
			<?php foreach ( $auds as $a ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'aud', $a, $base_url ) ); ?>"
					class="nav-tab <?php echo ( $a === $aud ) ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $labels[ $a ] ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! dhm_resume_has_content( $r ) ) : ?>
			<div class="notice notice-info inline"><p>This audience has no résumé content yet. Fill in the fields below and save — until then its résumé sections and download buttons stay hidden on the site.</p></div>
		<?php endif; ?>

		<form method="post" class="dhm-resume-form">
			<?php wp_nonce_field( 'dhm_resume_save_' . $aud ); ?>

			<h2>Header</h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="dhm_name">Name</label></th>
					<td><input type="text" id="dhm_name" name="name" class="regular-text" value="<?php echo esc_attr( $r['name'] ); ?>"></td></tr>
				<tr><th scope="row"><label for="dhm_title">Title line</label></th>
					<td><input type="text" id="dhm_title" name="title_line" class="large-text" value="<?php echo esc_attr( $r['title_line'] ); ?>" placeholder="Senior Software Engineer · Financial Software · Full-Stack / Frontend"></td></tr>
				<tr><th scope="row"><label for="dhm_location">Location</label></th>
					<td><input type="text" id="dhm_location" name="location" class="regular-text" value="<?php echo esc_attr( $r['location'] ); ?>"></td></tr>
				<tr><th scope="row"><label for="dhm_contact">Contact</label></th>
					<td><textarea id="dhm_contact" name="contact" rows="3" class="large-text"><?php echo esc_textarea( implode( "\n", $r['contact'] ) ); ?></textarea>
						<p class="description">One per line (email, LinkedIn, website).</p></td></tr>
				<tr><th scope="row"><label for="dhm_clearance">Clearance</label></th>
					<td><input type="text" id="dhm_clearance" name="clearance" class="large-text" value="<?php echo esc_attr( $r['clearance'] ); ?>">
						<p class="description">Optional. Leave blank to hide (used by the defense version).</p></td></tr>
			</table>

			<h2>Summary</h2>
			<textarea name="summary" rows="5" class="large-text"><?php echo esc_textarea( $r['summary'] ); ?></textarea>

			<h2>Core Competencies / Keywords</h2>
			<textarea name="keywords" rows="3" class="large-text"><?php echo esc_textarea( $r['keywords'] ); ?></textarea>
			<p class="description">Comma-separated (or one per line). These render as a <strong>Core Competencies</strong> section in the PDF/Word downloads and are embedded as document keywords — paste the target role's key skills and terms so ATS / AI screeners match. This does not appear on the website.</p>

			<h2>Experience</h2>
			<div class="dhm-rows" data-field="experience">
				<?php
				foreach ( $r['experience'] as $i => $job ) {
					echo dhm_resume_job_row_html( 'experience', $i, $job ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<button type="button" class="button dhm-add" data-target="experience">+ Add experience</button>

			<h2>Projects</h2>
			<div class="dhm-rows" data-field="projects">
				<?php
				foreach ( $r['projects'] as $i => $job ) {
					echo dhm_resume_job_row_html( 'projects', $i, $job ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<button type="button" class="button dhm-add" data-target="projects">+ Add project</button>

			<h2>Skills</h2>
			<div class="dhm-rows" data-field="skills">
				<?php
				foreach ( $r['skills'] as $i => $grp ) {
					echo dhm_resume_skill_row_html( $i, $grp ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<button type="button" class="button dhm-add" data-target="skills">+ Add skill group</button>

			<h2>Education</h2>
			<input type="text" name="education" class="large-text" value="<?php echo esc_attr( $r['education'] ); ?>">

			<p class="submit">
				<button type="submit" name="dhm_resume_save" value="1" class="button button-primary">Save résumé</button>
				<?php if ( dhm_resume_has_content( $r ) ) : ?>
					<a class="button" href="<?php echo esc_url( home_url( '/resume/' . $aud . '.pdf' ) ); ?>" target="_blank">Preview PDF</a>
					<a class="button" href="<?php echo esc_url( home_url( '/resume/' . $aud . '.docx' ) ); ?>">Download Word</a>
					<a class="button" href="<?php echo esc_url( home_url( '/resume/' . $aud . '.txt' ) ); ?>" target="_blank">Plain text</a>
				<?php endif; ?>
			</p>
		</form>

		<?php
		// JS clone templates (placeholder index __i__ swapped to a unique value).
		$tpl_exp   = dhm_resume_job_row_html( 'experience', '__i__' );
		$tpl_proj  = dhm_resume_job_row_html( 'projects', '__i__' );
		$tpl_skill = dhm_resume_skill_row_html( '__i__' );
		?>
		<script type="text/template" id="dhm-tpl-experience"><?php echo $tpl_exp; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
		<script type="text/template" id="dhm-tpl-projects"><?php echo $tpl_proj; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
		<script type="text/template" id="dhm-tpl-skills"><?php echo $tpl_skill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
	</div>

	<style>
		.dhm-resume-form h2 { margin-top: 1.6em; }
		.dhm-row { position: relative; border: 1px solid #dcdcde; border-radius: 6px; padding: 14px 36px 14px 14px; margin: 0 0 12px; background: #fff; }
		.dhm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px; }
		.dhm-grid-skills { grid-template-columns: 1fr 2fr; }
		.dhm-row label { display: block; font-weight: 600; font-size: 12px; }
		.dhm-row label input, .dhm-row label textarea { width: 100%; margin-top: 3px; font-weight: 400; }
		.dhm-bullets { margin-top: 10px; }
		.dhm-row-remove { position: absolute; top: 8px; right: 10px; color: #b32d2e; font-size: 20px; line-height: 1; text-decoration: none; cursor: pointer; }
		.dhm-add { margin-bottom: 8px; }
	</style>

	<script>
	( function () {
		var seq = Date.now();
		document.querySelectorAll( '.dhm-add' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var target = btn.getAttribute( 'data-target' );
				var tpl = document.getElementById( 'dhm-tpl-' + target );
				var rows = document.querySelector( '.dhm-rows[data-field="' + target + '"]' );
				if ( ! tpl || ! rows ) { return; }
				var html = tpl.innerHTML.replace( /__i__/g, 'n' + ( seq++ ) );
				var wrap = document.createElement( 'div' );
				wrap.innerHTML = html.trim();
				rows.appendChild( wrap.firstElementChild );
			} );
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( e.target && e.target.classList.contains( 'dhm-row-remove' ) ) {
				var row = e.target.closest( '.dhm-row' );
				if ( row ) { row.remove(); }
			}
		} );
	} )();
	</script>
	<?php
}
