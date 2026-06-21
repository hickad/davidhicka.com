<?php
/**
 * DigitalResume Modern — child theme functions.
 *
 * Applies the David Hicka Design System on top of the DigitalResume parent
 * theme: loads the stylesheet after the parent's Bootstrap, resolves the
 * audience skin from ?for=, and exposes helpers used by the template overrides.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* -------------------------------------------------------------------------
 * 1. Styles — parent Bootstrap first, then the design system.
 *
 * When a child theme is active the PARENT's own enqueue points
 * get_stylesheet_uri() at the CHILD's style.css, so the parent's compiled
 * Bootstrap (its root style.css) is no longer auto-loaded. We load it
 * explicitly here, then layer the design system on top.
 * ---------------------------------------------------------------------- */
function dhm_enqueue_styles() {
	$parent_css = get_template_directory() . '/style.css';
	wp_enqueue_style(
		'digitalresume-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		file_exists( $parent_css ) ? filemtime( $parent_css ) : '1.0.0'
	);

	$ds = get_stylesheet_directory() . '/assets/css/davidhicka-design-system.css';
	wp_enqueue_style(
		'davidhicka-ds',
		get_stylesheet_directory_uri() . '/assets/css/davidhicka-design-system.css',
		array( 'digitalresume-parent' ),
		file_exists( $ds ) ? filemtime( $ds ) : '1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'dhm_enqueue_styles', 20 );

/* -------------------------------------------------------------------------
 * 2. Audience resolution.
 * Precedence: ?for= URL param (persisted to a cookie) → cookie → 'finance'.
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'digitalresume_audience' ) ) {
	function digitalresume_audience() {
		$valid = array( 'finance', 'defense', 'healthcare' );

		if ( isset( $_GET['for'] ) && in_array( $_GET['for'], $valid, true ) ) {
			$aud = sanitize_key( $_GET['for'] );
			if ( ! headers_sent() ) {
				setcookie( 'dh_audience', $aud, time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
			}
			return $aud;
		}
		if ( isset( $_COOKIE['dh_audience'] ) && in_array( $_COOKIE['dh_audience'], $valid, true ) ) {
			return sanitize_key( $_COOKIE['dh_audience'] );
		}
		return 'finance';
	}
}

if ( ! function_exists( 'digitalresume_audience_label' ) ) {
	function digitalresume_audience_label() {
		$labels = array(
			'finance'    => 'Financial Software',
			'defense'    => 'Defense & Government',
			'healthcare' => 'Healthcare & Compliance',
		);
		$aud = digitalresume_audience();
		return isset( $labels[ $aud ] ) ? $labels[ $aud ] : $labels['finance'];
	}
}

/** Stamp data-audience on <html> so the CSS accents re-skin. */
if ( ! function_exists( 'digitalresume_html_audience' ) ) {
	function digitalresume_html_audience( $output ) {
		return $output . ' data-audience="' . esc_attr( digitalresume_audience() ) . '"';
	}
	add_filter( 'language_attributes', 'digitalresume_html_audience' );
}

/* -------------------------------------------------------------------------
 * 3. Per-audience copy used by the templates.
 * Edit these strings (or wire to ACF / theme options) to tune messaging.
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'digitalresume_audience_content' ) ) {
	function digitalresume_audience_content( $key = null ) {
		$content = array(
			'finance' => array(
				'lead'     => 'I build secure, compliance-driven financial software and ship AI-powered products end to end.',
				'resume'   => '/wp-content/uploads/david-hicka-resume-finance.pdf',
				'featured' => 'A production AI SaaS I built and launched solo — payments, subscriptions and fulfilment wired through Stripe, with real revenue and 2,000+ customers.',
			),
			'defense' => array(
				'lead'     => 'I build mission-critical software with the discipline of a decade in defense training systems — and ship AI products end to end.',
				'resume'   => '/wp-content/uploads/david-hicka-resume-defense.pdf',
				'featured' => 'A production AI SaaS I built and launched solo — proof I can take a complex system from concept to deployed, reliable product on my own.',
			),
			'healthcare' => array(
				'lead'     => 'I build accessible, compliance-minded healthcare software and ship AI-powered products end to end.',
				'resume'   => '/wp-content/uploads/david-hicka-resume-healthcare.pdf',
				'featured' => 'A production AI SaaS I built and launched solo — handling PII, secure auth and payments with the care a regulated environment demands.',
			),
		);
		$aud  = digitalresume_audience();
		$data = isset( $content[ $aud ] ) ? $content[ $aud ] : $content['finance'];
		return $key ? ( isset( $data[ $key ] ) ? $data[ $key ] : '' ) : $data;
	}
}
