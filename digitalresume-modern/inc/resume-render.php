<?php
/**
 * Résumé rendering — shared between the website sections and the generated PDF.
 *
 * Website partials echo into the existing design-system markup
 * (dh-timeline / dh-skill chips). The document builder returns a standalone,
 * print-styled HTML page that mPDF turns into the PDF, so the PDF and the site
 * are rendered from the same data by the same code path.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Render one timeline item (used by the website Experience section). */
function dhm_resume_timeline_item_html( $job, $muted = false, $badge = '' ) {
	$classes = 'dh-tl-item' . ( $muted ? ' muted' : '' );
	ob_start();
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<?php if ( $badge ) : ?>
			<span class="dh-tl-badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<h3 class="dh-tl-role">
			<?php
			echo esc_html( $job['role'] );
			if ( $job['role'] && $job['company'] ) {
				echo ' — ';
			}
			echo esc_html( $job['company'] );
			?>
		</h3>
		<?php
		$meta = array_filter( array( $job['dates'], $job['location'] ) );
		if ( $meta ) :
			?>
			<p class="dh-tl-meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $job['bullets'] ) ) : ?>
			<ul>
				<?php foreach ( $job['bullets'] as $b ) : ?>
					<li><?php echo esc_html( $b ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/** Website: the Experience timeline, driven by résumé data. */
function dhm_resume_experience_html( $r ) {
	if ( empty( $r['experience'] ) ) {
		return '';
	}
	$out = '<div class="dh-timeline">';
	foreach ( $r['experience'] as $i => $job ) {
		$badge = ( 0 === $i ) ? 'Most relevant' : '';
		$muted = ( $i >= 2 );
		$out  .= dhm_resume_timeline_item_html( $job, $muted, $badge );
	}
	$out .= '</div>';
	return $out;
}

/** Website: Skills as chips (categories flattened to comma-separated items). */
function dhm_resume_skills_html( $r ) {
	if ( empty( $r['skills'] ) ) {
		return '';
	}
	$chips = array();
	foreach ( $r['skills'] as $grp ) {
		foreach ( explode( ',', $grp['items'] ) as $item ) {
			$item = trim( $item );
			if ( '' !== $item ) {
				$chips[] = $item;
			}
		}
	}
	if ( empty( $chips ) ) {
		return '';
	}
	$out = '<div class="dh-skills">';
	foreach ( $chips as $c ) {
		$out .= '<span class="dh-skill">' . esc_html( $c ) . '</span>';
	}
	$out .= '</div>';
	return $out;
}

/** Website: the education line (matches the existing inline-styled paragraph). */
function dhm_resume_education_html( $r ) {
	if ( '' === $r['education'] ) {
		return '';
	}
	return '<p style="font-family:var(--font-mono);font-size:0.78rem;color:var(--ink-3);margin-top:2.5rem;padding-top:1.25rem;border-top:1px solid var(--line);letter-spacing:0.02em;">'
		. 'EDUCATION — ' . esc_html( $r['education'] ) . '</p>';
}

/* -------------------------------------------------------------------------
 * Standalone document (PDF) — ATS-optimized. Single column, linear reading
 * order, standard section headings, design-system accent. Acronyms are spelled
 * out on first use (document only — the website keeps the short forms).
 * ---------------------------------------------------------------------- */

/** Acronyms expanded on first use in the generated documents. Longest first. */
function dhm_resume_acronyms() {
	return array(
		'SOC 2 Type II' => 'SOC 2 Type II (System and Organization Controls)',
		'PCI DSS'       => 'PCI DSS (Payment Card Industry Data Security Standard)',
		'WCAG 2.0 AA'   => 'WCAG 2.0 AA (Web Content Accessibility Guidelines)',
		'WCAG'          => 'WCAG (Web Content Accessibility Guidelines)',
		'SSRS'          => 'SSRS (SQL Server Reporting Services)',
		'CRM'           => 'CRM (Customer Relationship Management)',
		'SaaS'          => 'SaaS (Software as a Service)',
		'AFSOC'         => 'AFSOC (Air Force Special Operations Command)',
		'USMC'          => 'USMC (United States Marine Corps)',
		'USN'           => 'USN (United States Navy)',
	);
}

/**
 * Expand each acronym's FIRST use across the document. $seen is shared across
 * all calls (passed by reference) so a term is expanded once per document, in
 * reading order. Skips terms already written as "TERM (…)" or "(TERM)".
 */
function dhm_resume_doc_text( $text, &$seen ) {
	$map = dhm_resume_acronyms();
	foreach ( $map as $term => $full ) {
		if ( ! empty( $seen[ $term ] ) ) {
			continue;
		}
		$pattern = '/(?<!\()' . preg_quote( $term, '/' ) . '(?!\s*\()/';
		if ( preg_match( $pattern, $text, $m, PREG_OFFSET_CAPTURE ) ) {
			$pos  = $m[0][1];
			$text = substr( $text, 0, $pos ) . $full . substr( $text, $pos + strlen( $term ) );
			$seen[ $term ] = true;
			// Mark shorter related acronyms seen so they don't re-expand inside this expansion.
			foreach ( array_keys( $map ) as $other ) {
				if ( $other !== $term && false !== strpos( $term, $other ) ) {
					$seen[ $other ] = true;
				}
			}
		}
	}
	return $text;
}

/** Linear job block for the printed document (no floats/columns). */
function dhm_resume_doc_jobs_html( $jobs, &$seen ) {
	$out = '';
	foreach ( $jobs as $job ) {
		$out  .= '<div class="job">';
		$title = esc_html( $job['role'] );
		if ( $job['role'] && $job['company'] ) {
			$title .= ', ';
		}
		$title .= esc_html( $job['company'] );
		$out   .= '<p class="job-title"><strong>' . $title . '</strong></p>';
		$meta   = array_filter( array( $job['dates'], $job['location'] ) );
		if ( $meta ) {
			$out .= '<p class="job-meta">' . esc_html( implode( ' | ', $meta ) ) . '</p>';
		}
		if ( ! empty( $job['bullets'] ) ) {
			$out .= '<ul>';
			foreach ( $job['bullets'] as $b ) {
				$out .= '<li>' . esc_html( dhm_resume_doc_text( $b, $seen ) ) . '</li>';
			}
			$out .= '</ul>';
		}
		$out .= '</div>';
	}
	return $out;
}

/**
 * Plain-text résumé (maximally ATS-safe; good for copy-paste into application
 * forms). Same section order, headings, and acronym expansion as the documents.
 *
 * @param array $r normalized résumé record.
 * @return string UTF-8 plain text.
 */
function dhm_resume_document_text( $r ) {
	$seen = array();
	$nl   = "\n";
	$out  = array();

	$out[] = $r['name'];
	if ( $r['title_line'] ) {
		$out[] = $r['title_line'];
	}
	$contact_bits = $r['contact'];
	if ( $r['location'] ) {
		array_unshift( $contact_bits, $r['location'] );
	}
	if ( $contact_bits ) {
		$out[] = implode( ' | ', $contact_bits );
	}
	if ( $r['clearance'] ) {
		$out[] = $r['clearance'];
	}

	$jobs_text = function ( $jobs ) use ( &$seen ) {
		$lines = array();
		foreach ( $jobs as $job ) {
			$title = trim( $job['role'] . ( ( $job['role'] && $job['company'] ) ? ', ' : '' ) . $job['company'] );
			$lines[] = '';
			$lines[] = $title;
			$meta    = array_filter( array( $job['dates'], $job['location'] ) );
			if ( $meta ) {
				$lines[] = implode( ' | ', $meta );
			}
			foreach ( $job['bullets'] as $b ) {
				$lines[] = '- ' . dhm_resume_doc_text( $b, $seen );
			}
		}
		return $lines;
	};

	if ( $r['summary'] ) {
		$out[] = '';
		$out[] = 'PROFESSIONAL SUMMARY';
		$out[] = dhm_resume_doc_text( $r['summary'], $seen );
	}
	if ( '' !== trim( (string) $r['keywords'] ) ) {
		$out[] = '';
		$out[] = 'CORE COMPETENCIES';
		$out[] = $r['keywords'];
	}
	if ( ! empty( $r['skills'] ) ) {
		$out[] = '';
		$out[] = 'TECHNICAL SKILLS';
		foreach ( $r['skills'] as $grp ) {
			$out[] = $grp['category'] . ': ' . $grp['items'];
		}
	}
	if ( ! empty( $r['experience'] ) ) {
		$out[] = '';
		$out[] = 'WORK EXPERIENCE';
		$out   = array_merge( $out, $jobs_text( $r['experience'] ) );
	}
	if ( ! empty( $r['projects'] ) ) {
		$out[] = '';
		$out[] = 'PROJECTS';
		$out   = array_merge( $out, $jobs_text( $r['projects'] ) );
	}
	if ( $r['education'] ) {
		$out[] = '';
		$out[] = 'EDUCATION';
		$out[] = $r['education'];
	}

	return implode( $nl, $out ) . $nl;
}

/**
 * Full standalone HTML for the printed PDF (ATS-optimized).
 *
 * @param array $r normalized résumé record.
 * @return string complete HTML document.
 */
function dhm_resume_document_html( $r ) {
	$accent = '#54b689';
	$seen   = array();
	ob_start();
	?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
	@page { margin: 14mm 14mm 14mm 14mm; }
	body { font-family: "DejaVu Sans", sans-serif; color: #16181d; font-size: 9.5pt; line-height: 1.4; }
	.name { font-size: 20pt; font-weight: 700; margin: 0 0 2pt; letter-spacing: .2pt; }
	.title-line { color: <?php echo $accent; ?>; font-size: 10pt; font-weight: 700; margin: 0 0 4pt; }
	.contact { color: #555; font-size: 8.5pt; margin: 0 0 2pt; }
	.clearance { color: #16181d; font-size: 8.5pt; font-weight: 700; margin: 4pt 0 0; }
	h2.section { font-size: 10.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1pt;
		color: #16181d; border-bottom: 1.5pt solid <?php echo $accent; ?>; padding-bottom: 2pt;
		margin: 13pt 0 6pt; }
	.summary { margin: 0; text-align: left; }
	.competencies { margin: 0; }
	.job { margin: 0 0 7pt; }
	.job-title { margin: 0; }
	.job-meta { color: #555; font-size: 8.5pt; margin: 1pt 0 0; }
	ul { margin: 3pt 0 0; padding-left: 14pt; }
	li { margin: 0 0 2pt; }
	.skill-grp { margin: 0 0 3pt; }
	.skill-cat { font-weight: 700; }
	.edu { margin: 0; }
</style>
</head>
<body>
	<div class="name"><?php echo esc_html( $r['name'] ); ?></div>
	<?php if ( $r['title_line'] ) : ?>
		<div class="title-line"><?php echo esc_html( $r['title_line'] ); ?></div>
	<?php endif; ?>
	<?php
	$contact_bits = $r['contact'];
	if ( $r['location'] ) {
		array_unshift( $contact_bits, $r['location'] );
	}
	if ( $contact_bits ) :
		?>
		<div class="contact"><?php echo esc_html( implode( ' | ', $contact_bits ) ); ?></div>
	<?php endif; ?>
	<?php if ( $r['clearance'] ) : ?>
		<div class="clearance"><?php echo esc_html( $r['clearance'] ); ?></div>
	<?php endif; ?>

	<?php if ( $r['summary'] ) : ?>
		<h2 class="section">Professional Summary</h2>
		<p class="summary"><?php echo esc_html( dhm_resume_doc_text( $r['summary'], $seen ) ); ?></p>
	<?php endif; ?>

	<?php if ( '' !== trim( (string) $r['keywords'] ) ) : ?>
		<h2 class="section">Core Competencies</h2>
		<p class="competencies"><?php echo esc_html( $r['keywords'] ); ?></p>
	<?php endif; ?>

	<?php if ( ! empty( $r['skills'] ) ) : ?>
		<h2 class="section">Technical Skills</h2>
		<?php foreach ( $r['skills'] as $grp ) : ?>
			<div class="skill-grp"><span class="skill-cat"><?php echo esc_html( $grp['category'] ); ?>:</span>
				<?php echo esc_html( $grp['items'] ); ?></div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( ! empty( $r['experience'] ) ) : ?>
		<h2 class="section">Work Experience</h2>
		<?php echo dhm_resume_doc_jobs_html( $r['experience'], $seen ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php endif; ?>

	<?php if ( ! empty( $r['projects'] ) ) : ?>
		<h2 class="section">Projects</h2>
		<?php echo dhm_resume_doc_jobs_html( $r['projects'], $seen ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php endif; ?>

	<?php if ( $r['education'] ) : ?>
		<h2 class="section">Education</h2>
		<p class="edu"><?php echo esc_html( $r['education'] ); ?></p>
	<?php endif; ?>
</body>
</html>
	<?php
	return ob_get_clean();
}
