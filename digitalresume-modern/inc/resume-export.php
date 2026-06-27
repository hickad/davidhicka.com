<?php
/**
 * Résumé export — generate the PDF (mPDF) and Word doc (PHPWord) on demand from
 * the same data the website renders, and serve them at:
 *
 *   /resume/{audience}.pdf
 *   /resume/{audience}.docx
 *
 * (a ?dh_resume=…&dh_format=… query fallback also works if rewrites aren't
 * flushed). Access reuses the site gate: logged-in WP users and visitors with a
 * valid portfolio-auth cookie may download; everyone else hits the login form.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Composer autoloader (mPDF + PHPWord). */
function dhm_resume_autoload() {
	static $loaded = null;
	if ( null !== $loaded ) {
		return $loaded;
	}
	$autoload = get_stylesheet_directory() . '/vendor/autoload.php';
	if ( is_readable( $autoload ) ) {
		require_once $autoload;
		$loaded = true;
	} else {
		$loaded = false;
	}
	return $loaded;
}

/* ---- Routing -------------------------------------------------------------- */

function dhm_resume_query_vars( $vars ) {
	$vars[] = 'dh_resume';
	$vars[] = 'dh_format';
	return $vars;
}
add_filter( 'query_vars', 'dhm_resume_query_vars' );

function dhm_resume_rewrite_rules() {
	add_rewrite_rule(
		'^resume/(finance|defense|healthcare|general)\.(pdf|docx|txt)/?$',
		'index.php?dh_resume=$matches[1]&dh_format=$matches[2]',
		'top'
	);
}
add_action( 'init', 'dhm_resume_rewrite_rules' );

/**
 * Don't let WordPress's canonical redirect bounce the download URLs (it would
 * otherwise 301 /resume/x.pdf → /resume/x.pdf/ and lose the request).
 */
function dhm_resume_skip_canonical( $redirect_url ) {
	if ( '' !== get_query_var( 'dh_resume' ) ) {
		return false;
	}
	return $redirect_url;
}
add_filter( 'redirect_canonical', 'dhm_resume_skip_canonical' );

/** Resolve the requested (audience, format) from query vars or $_GET fallback. */
function dhm_resume_requested() {
	$aud = get_query_var( 'dh_resume' );
	$fmt = get_query_var( 'dh_format' );
	if ( '' === $aud && isset( $_GET['dh_resume'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$aud = sanitize_key( wp_unslash( $_GET['dh_resume'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( '' === $fmt && isset( $_GET['dh_format'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$fmt = sanitize_key( wp_unslash( $_GET['dh_format'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( ! in_array( $aud, dhm_resume_audiences(), true ) ) {
		return null;
	}
	if ( ! in_array( $fmt, array( 'pdf', 'docx', 'txt' ), true ) ) {
		return null;
	}
	return array( $aud, $fmt );
}

/** Same access rule as dhm_site_gate(): logged in OR valid portfolio cookie. */
function dhm_resume_viewer_allowed() {
	if ( is_user_logged_in() ) {
		return true;
	}
	return isset( $_COOKIE['dh_portfolio_auth'] )
		&& function_exists( 'dhm_portfolio_token_valid' )
		&& dhm_portfolio_token_valid( wp_unslash( $_COOKIE['dh_portfolio_auth'] ) );
}

/**
 * Download handler. Runs at priority 11 — after dhm_site_gate() (10), which
 * already shows the login form (and exits) for unauthenticated visitors.
 */
function dhm_resume_download_route() {
	$req = dhm_resume_requested();
	if ( null === $req ) {
		return;
	}
	list( $aud, $fmt ) = $req;

	if ( ! dhm_resume_viewer_allowed() ) {
		return; // The gate handles unauthenticated access.
	}

	$r = dhm_resume_get( $aud );
	if ( ! dhm_resume_has_content( $r ) ) {
		status_header( 404 );
		nocache_headers();
		echo 'No résumé is available for this selection yet.';
		exit;
	}

	// PDF and Word need the Composer libraries; plain text does not.
	if ( 'txt' !== $fmt && ! dhm_resume_autoload() ) {
		status_header( 500 );
		nocache_headers();
		echo 'Résumé generator is unavailable (dependencies not installed).';
		exit;
	}

	$filename = 'David_Hicka_Resume_' . ucfirst( $aud ) . '.' . $fmt;

	try {
		if ( 'pdf' === $fmt ) {
			$bytes = dhm_resume_pdf_bytes( $r );
			$mime  = 'application/pdf';
		} elseif ( 'docx' === $fmt ) {
			$bytes = dhm_resume_docx_bytes( $r );
			$mime  = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		} else {
			$bytes = dhm_resume_document_text( $r );
			$mime  = 'text/plain; charset=utf-8';
		}
	} catch ( \Throwable $e ) {
		status_header( 500 );
		nocache_headers();
		echo 'Could not generate the résumé.';
		exit;
	}

	nocache_headers();
	header( 'Content-Type: ' . $mime );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	header( 'Content-Length: ' . strlen( $bytes ) );
	header( 'X-Robots-Tag: noindex, nofollow', true );
	echo $bytes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'dhm_resume_download_route', 11 );

/* ---- PDF (mPDF) ----------------------------------------------------------- */

function dhm_resume_pdf_bytes( $r ) {
	$tmp = trailingslashit( get_temp_dir() ) . 'mpdf';
	if ( ! is_dir( $tmp ) ) {
		wp_mkdir_p( $tmp );
	}
	$mpdf = new \Mpdf\Mpdf(
		array(
			'mode'          => 'utf-8',
			'format'        => 'Letter',
			'tempDir'       => $tmp,
			'margin_top'    => 14,
			'margin_bottom' => 14,
			'margin_left'   => 14,
			'margin_right'  => 14,
		)
	);
	$mpdf->SetTitle( $r['name'] . ' — Résumé' );
	$mpdf->SetAuthor( $r['name'] );
	if ( '' !== trim( (string) $r['keywords'] ) ) {
		$mpdf->SetKeywords( $r['keywords'] );
	}
	if ( $r['title_line'] ) {
		$mpdf->SetSubject( $r['title_line'] );
	}
	$mpdf->WriteHTML( dhm_resume_document_html( $r ) );
	return $mpdf->Output( '', \Mpdf\Output\Destination::STRING_RETURN );
}

/* ---- Word (PHPWord) ------------------------------------------------------- */

function dhm_resume_docx_bytes( $r ) {
	$accent = '54B689';
	$gray   = '555555';
	$ink    = '16181D';
	$seen   = array(); // Shared acronym first-use tracker (document only).

	$word = new \PhpOffice\PhpWord\PhpWord();
	$word->setDefaultFontName( 'Calibri' );
	$word->setDefaultFontSize( 10 );

	// Document properties — some ATS parsers read these.
	$info = $word->getDocInfo();
	$info->setCreator( $r['name'] );
	$info->setTitle( $r['name'] . ' — Résumé' );
	$info->setDescription( $r['title_line'] );
	$info->setSubject( $r['title_line'] );
	if ( '' !== trim( (string) $r['keywords'] ) ) {
		$info->setKeywords( $r['keywords'] );
	}

	$section = $word->addSection(
		array(
			'marginTop'    => 720,
			'marginBottom' => 720,
			'marginLeft'   => 720,
			'marginRight'  => 720,
		)
	);

	// Header.
	$section->addText( $r['name'], array( 'size' => 20, 'bold' => true, 'color' => $ink ), array( 'spaceAfter' => 20 ) );
	if ( $r['title_line'] ) {
		$section->addText( $r['title_line'], array( 'size' => 11, 'bold' => true, 'color' => $accent ), array( 'spaceAfter' => 20 ) );
	}
	$contact_bits = $r['contact'];
	if ( $r['location'] ) {
		array_unshift( $contact_bits, $r['location'] );
	}
	if ( $contact_bits ) {
		$section->addText( implode( ' | ', $contact_bits ), array( 'size' => 9, 'color' => $gray ), array( 'spaceAfter' => 20 ) );
	}
	if ( $r['clearance'] ) {
		$section->addText( $r['clearance'], array( 'size' => 9, 'bold' => true, 'color' => $ink ), array( 'spaceAfter' => 20 ) );
	}

	$heading_font = array( 'size' => 11, 'bold' => true, 'color' => $ink, 'allCaps' => true );
	$heading_para = array( 'spaceBefore' => 220, 'spaceAfter' => 80, 'borderBottomSize' => 12, 'borderBottomColor' => $accent );

	// Linear job block: "Role, Company" (bold), then "Dates | Location", then bullets.
	$add_jobs = function ( $jobs ) use ( $section, $gray, $ink, &$seen ) {
		foreach ( $jobs as $job ) {
			$line = trim( $job['role'] . ( ( $job['role'] && $job['company'] ) ? ', ' : '' ) . $job['company'] );
			$section->addText( $line, array( 'size' => 10, 'bold' => true, 'color' => $ink ), array( 'spaceBefore' => 120, 'spaceAfter' => 0 ) );
			$meta = array_filter( array( $job['dates'], $job['location'] ) );
			if ( $meta ) {
				$section->addText( implode( ' | ', $meta ), array( 'size' => 9, 'color' => $gray ), array( 'spaceAfter' => 40 ) );
			}
			foreach ( $job['bullets'] as $b ) {
				$section->addListItem( dhm_resume_doc_text( $b, $seen ), 0, array( 'size' => 10 ), array( 'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED ), array( 'spaceAfter' => 30 ) );
			}
		}
	};

	if ( $r['summary'] ) {
		$section->addText( 'PROFESSIONAL SUMMARY', $heading_font, $heading_para );
		$section->addText( dhm_resume_doc_text( $r['summary'], $seen ), array( 'size' => 10 ), array( 'spaceAfter' => 40 ) );
	}
	if ( '' !== trim( (string) $r['keywords'] ) ) {
		$section->addText( 'CORE COMPETENCIES', $heading_font, $heading_para );
		$section->addText( $r['keywords'], array( 'size' => 10 ), array( 'spaceAfter' => 40 ) );
	}
	if ( ! empty( $r['skills'] ) ) {
		$section->addText( 'TECHNICAL SKILLS', $heading_font, $heading_para );
		foreach ( $r['skills'] as $grp ) {
			$run = $section->addTextRun( array( 'spaceAfter' => 30 ) );
			$run->addText( $grp['category'] . ':  ', array( 'size' => 10, 'bold' => true ) );
			$run->addText( $grp['items'], array( 'size' => 10 ) );
		}
	}
	if ( ! empty( $r['experience'] ) ) {
		$section->addText( 'WORK EXPERIENCE', $heading_font, $heading_para );
		$add_jobs( $r['experience'] );
	}
	if ( ! empty( $r['projects'] ) ) {
		$section->addText( 'PROJECTS', $heading_font, $heading_para );
		$add_jobs( $r['projects'] );
	}
	if ( $r['education'] ) {
		$section->addText( 'EDUCATION', $heading_font, $heading_para );
		$section->addText( $r['education'], array( 'size' => 10 ) );
	}

	$tmp_file = trailingslashit( get_temp_dir() ) . uniqid( 'dhm_resume_', true ) . '.docx';
	$writer   = \PhpOffice\PhpWord\IOFactory::createWriter( $word, 'Word2007' );
	$writer->save( $tmp_file );
	$bytes = (string) file_get_contents( $tmp_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	wp_delete_file( $tmp_file );
	return $bytes;
}
