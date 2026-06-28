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
 * 0. Résumé subsystem — single source of truth (Dashboard → Résumé) that
 * drives the website sections plus the generated PDF/Word downloads.
 * ---------------------------------------------------------------------- */
require get_stylesheet_directory() . '/inc/resume-data.php';
require get_stylesheet_directory() . '/inc/resume-render.php';
require get_stylesheet_directory() . '/inc/resume-export.php';
if ( is_admin() ) {
	require get_stylesheet_directory() . '/inc/resume-admin.php';
	require get_stylesheet_directory() . '/inc/resume-linkedin.php';
}

/** Push curated seed updates (e.g. the ATS content pass) to the live option. */
add_action( 'init', 'dhm_resume_maybe_reseed', 1 );

/** Flush rewrites once so /resume/{audience}.{pdf,docx} resolves after deploy. */
function dhm_resume_flush_rewrites() {
	if ( function_exists( 'dhm_resume_rewrite_rules' ) ) {
		dhm_resume_rewrite_rules();
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dhm_resume_flush_rewrites' );

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

/* The parent theme's bundle.js forces a default "dark-mode" body class (which
 * overrides the light design system) and errors on the dark-mode toggle that
 * this header no longer has. The modern child theme is light-only, so drop it
 * and the isotope helper none of the child templates use. */
function dhm_dequeue_parent_scripts() {
	wp_dequeue_script( 'digitalresume-js' );
	wp_dequeue_script( 'isotope-custom' );
}
add_action( 'wp_enqueue_scripts', 'dhm_dequeue_parent_scripts', 100 );

/* -------------------------------------------------------------------------
 * 2. Audience resolution.
 * Precedence: ?for= URL param (persisted to a cookie) → cookie → 'finance'.
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'digitalresume_audience' ) ) {
	function digitalresume_audience() {
		$valid = array( 'finance', 'defense', 'healthcare', 'general' );

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
			'general'    => 'Software Engineering',
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
				'lead'       => 'I build secure, compliance-driven financial software and ship AI-powered products end to end.',
				'resume'     => '/wp-content/uploads/2026/06/david_hicka_resume_finance.pdf',
				'resume_doc' => '/wp-content/uploads/2026/06/david_hicka_resume_finance.docx',
				'featured'   => 'A production AI SaaS I built and launched solo — payments, subscriptions and fulfilment wired through Stripe, with real revenue and 2,000+ customers.',
			),
			'defense' => array(
				'lead'       => 'I build mission-critical software with the discipline of a decade in defense training systems — and ship AI products end to end.',
				'resume'     => '/wp-content/uploads/2026/06/david_hicka_resume_defense.pdf',
				'resume_doc' => '/wp-content/uploads/2026/06/david_hicka_resume_defense.docx',
				'featured'   => 'A production AI SaaS I built and launched solo — proof I can take a complex system from concept to deployed, reliable product on my own.',
			),
			'healthcare' => array(
				'lead'       => 'I build accessible, compliance-minded healthcare software and ship AI-powered products end to end.',
				'resume'     => '/wp-content/uploads/2026/06/david_hicka_resume_healthcare.pdf',
				'resume_doc' => '/wp-content/uploads/2026/06/david_hicka_resume_healthcare.docx',
				'featured'   => 'A production AI SaaS I built and launched solo — handling PII, secure auth and payments with the care a regulated environment demands.',
			),
			'general'    => array(
				'lead'       => 'I build secure, reliable software and ship AI-powered products end to end — across finance, defense and healthcare.',
				'resume'     => '',
				'resume_doc' => '',
				'featured'   => 'A production AI SaaS I built and launched solo — payments, subscriptions and fulfilment via Stripe, with real revenue and 2,000+ customers.',
			),
		);
		$aud  = digitalresume_audience();
		$data = isset( $content[ $aud ] ) ? $content[ $aud ] : $content['finance'];
		return $key ? ( isset( $data[ $key ] ) ? $data[ $key ] : '' ) : $data;
	}
}

/* -------------------------------------------------------------------------
 * 4. Password-gated portfolio.
 *
 * Serves a standalone portfolio document (kept OUTSIDE the web root and out
 * of this public repo, at PORTFOLIO_FILE) at the /portfolio URL behind HTTP
 * Basic Auth. Access is granted to a master credential defined in
 * wp-config.php (not in version control) OR any custom credential added
 * under Settings -> Portfolio Access (for per-employer submissions).
 * ---------------------------------------------------------------------- */

if ( ! defined( 'PORTFOLIO_FILE' ) ) {
	define( 'PORTFOLIO_FILE', '/opt/bitnami/portfolio-private/portfolio.html' );
}

/** Custom (DB-stored) credentials, e.g. one per employer submission. */
function dhm_portfolio_creds() {
	$creds = get_option( 'dhm_portfolio_creds', array() );
	return is_array( $creds ) ? $creds : array();
}

/**
 * Check credentials. On success returns array( 'audience' => ?, 'label' => ? );
 * on failure returns false. The master (wp-config) login returns empty values.
 */
function dhm_portfolio_match( $user, $pass ) {
	$user = (string) $user;
	$pass = (string) $pass;
	if ( '' === $user || '' === $pass ) {
		return false;
	}
	if ( defined( 'PORTFOLIO_AUTH_USER' ) && defined( 'PORTFOLIO_AUTH_PASS' )
		&& hash_equals( (string) PORTFOLIO_AUTH_USER, $user )
		&& hash_equals( (string) PORTFOLIO_AUTH_PASS, $pass ) ) {
		return array(
			'audience' => '',
			'label'    => '',
		);
	}
	foreach ( dhm_portfolio_creds() as $c ) {
		if ( ! empty( $c['user'] ) && isset( $c['pass'] )
			&& hash_equals( (string) $c['user'], $user )
			&& hash_equals( (string) $c['pass'], $pass ) ) {
			return array(
				'audience' => isset( $c['audience'] ) ? (string) $c['audience'] : '',
				'label'    => isset( $c['label'] ) ? (string) $c['label'] : '',
			);
		}
	}
	return false;
}

/** The label of the credential the current visitor signed in with (display only). */
function dhm_viewer_label() {
	return isset( $_COOKIE['dh_label'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['dh_label'] ) ) : '';
}

/** Signed session token so the access cookie cannot be forged. */
function dhm_portfolio_make_token( $exp ) {
	return $exp . '.' . hash_hmac( 'sha256', 'dh-portfolio|' . $exp, wp_salt( 'auth' ) );
}
function dhm_portfolio_token_valid( $token ) {
	if ( ! is_string( $token ) || false === strpos( $token, '.' ) ) {
		return false;
	}
	list( $exp, $sig ) = explode( '.', $token, 2 );
	if ( ! ctype_digit( $exp ) || (int) $exp < time() ) {
		return false;
	}
	return hash_equals( hash_hmac( 'sha256', 'dh-portfolio|' . $exp, wp_salt( 'auth' ) ), $sig );
}

/** Output the standalone portfolio document and stop. */
function dhm_portfolio_output() {
	if ( ! is_readable( PORTFOLIO_FILE ) ) {
		status_header( 500 );
		echo 'Portfolio document is unavailable.';
		exit;
	}
	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'X-Robots-Tag: noindex, nofollow', true );
	readfile( PORTFOLIO_FILE );
	exit;
}

/** Render the light, branded login form and stop. */
function dhm_portfolio_login_form( $error = '' ) {
	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'X-Robots-Tag: noindex, nofollow', true );
	$nonce = wp_create_nonce( 'dh_portfolio_login' );
	?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Portfolio Access — David Hicka</title>
<style>
  :root { --accent:#54b689; --accent-600:#4aa87c; --bg:#f6f7f5; --card:#ffffff; --ink:#16181d; --muted:#6b7178; --line:#e4e6e3; }
  * { box-sizing:border-box; }
  body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
    background:var(--bg); color:var(--ink); padding:24px;
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; }
  .card { width:100%; max-width:380px; background:var(--card); border:1px solid var(--line);
    border-radius:16px; padding:32px; box-shadow:0 12px 44px rgba(22,24,29,.08); }
  .brand { display:flex; align-items:center; gap:10px; margin-bottom:22px; }
  .brand .mark { width:32px; height:32px; border-radius:8px; background:var(--accent); color:#fff;
    font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center; }
  .brand strong { font-size:.95rem; }
  h1 { font-size:1.15rem; margin:0 0 4px; }
  p.sub { margin:0 0 22px; color:var(--muted); font-size:.9rem; }
  label { display:block; font-size:.8rem; color:var(--muted); margin:0 0 6px; }
  input[type=text], input[type=password] { width:100%; padding:11px 13px; margin-bottom:16px;
    background:#fff; border:1px solid var(--line); border-radius:9px; color:var(--ink); font-size:.95rem; }
  input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(84,182,137,.18); }
  button { width:100%; padding:12px; background:var(--accent); color:#fff; border:0; border-radius:9px;
    font-size:.95rem; font-weight:600; cursor:pointer; }
  button:hover { background:var(--accent-600); }
  .error { background:#fdecec; border:1px solid #f5c2c2; color:#a23b3b;
    padding:10px 12px; border-radius:9px; font-size:.85rem; margin-bottom:18px; }
</style>
</head>
<body>
<form class="card" method="post" action="">
  <div class="brand"><span class="mark">DH</span><strong>David Hicka</strong></div>
  <h1>Portfolio Access</h1>
  <p class="sub">Enter your credentials to view the portfolio.</p>
  <?php if ( $error ) : ?><div class="error"><?php echo esc_html( $error ); ?></div><?php endif; ?>
  <label for="dh_user">Username</label>
  <input id="dh_user" name="dh_user" type="text" autocomplete="username" autofocus required>
  <label for="dh_pass">Password</label>
  <input id="dh_pass" name="dh_pass" type="password" autocomplete="current-password" required>
  <input type="hidden" name="dh_portfolio_login" value="1">
  <input type="hidden" name="dh_pf_nonce" value="<?php echo esc_attr( $nonce ); ?>">
  <button type="submit">Sign in</button>
</form>
</body>
</html>
	<?php
	exit;
}

/** Gate the entire front-end behind the credential login + cookie. */
function dhm_site_gate() {
	// Never gate the admin, AJAX, the REST API (CF7 submits via REST), or cron.
	if ( is_admin() || wp_doing_ajax()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		|| ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
		return;
	}

	// The site owner (any logged-in WP user) bypasses the gate; the portfolio
	// archive still serves its standalone document.
	if ( is_user_logged_in() ) {
		if ( is_post_type_archive( 'portfolio' ) ) {
			dhm_portfolio_output();
		}
		return;
	}

	$authed = isset( $_COOKIE['dh_portfolio_auth'] ) && dhm_portfolio_token_valid( wp_unslash( $_COOKIE['dh_portfolio_auth'] ) );
	$error  = '';

	// Handle a login submission (the form posts back to the current URL).
	if ( ! $authed && isset( $_POST['dh_portfolio_login'] ) ) {
		$nonce = isset( $_POST['dh_pf_nonce'] ) ? wp_unslash( $_POST['dh_pf_nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'dh_portfolio_login' ) ) {
			$error = 'Your session expired. Please try again.';
		} else {
			$u     = isset( $_POST['dh_user'] ) ? wp_unslash( $_POST['dh_user'] ) : '';
			$p     = isset( $_POST['dh_pass'] ) ? wp_unslash( $_POST['dh_pass'] ) : '';
			$match = dhm_portfolio_match( $u, $p );
			if ( false !== $match ) {
				$exp = time() + 8 * HOUR_IN_SECONDS;
				setcookie(
					'dh_portfolio_auth',
					dhm_portfolio_make_token( $exp ),
					array(
						'expires'  => $exp,
						'path'     => '/',
						'secure'   => is_ssl(),
						'httponly' => true,
						'samesite' => 'Lax',
					)
				);
				// Theme the whole site for this credential's audience.
				if ( '' !== $match['audience'] && in_array( $match['audience'], array( 'finance', 'defense', 'healthcare', 'general' ), true ) ) {
					setcookie(
						'dh_audience',
						$match['audience'],
						array(
							'expires'  => time() + WEEK_IN_SECONDS,
							'path'     => '/',
							'secure'   => is_ssl(),
							'samesite' => 'Lax',
						)
					);
				}
				// Remember the credential's label for a personalized greeting.
				if ( '' !== $match['label'] ) {
					setcookie(
						'dh_label',
						$match['label'],
						array(
							'expires'  => $exp,
							'path'     => '/',
							'secure'   => is_ssl(),
							'samesite' => 'Lax',
						)
					);
				}
				// Post/Redirect/Get back to the requested page.
				$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '/';
				wp_safe_redirect( '/' . ltrim( (string) $path, '/' ) );
				exit;
			}
			$error = 'Incorrect username or password.';
		}
	}

	// Not signed in: show the login form in place of the requested page.
	if ( ! $authed ) {
		dhm_portfolio_login_form( $error );
	}

	// Signed in: the portfolio archive serves the standalone document; every
	// other page renders normally.
	if ( is_post_type_archive( 'portfolio' ) ) {
		dhm_portfolio_output();
	}
}
add_action( 'template_redirect', 'dhm_site_gate' );

/* Admin: Settings -> Portfolio Access (manage per-employer credentials). */
function dhm_portfolio_admin_menu() {
	add_options_page( 'Site Access', 'Site Access', 'manage_options', 'dhm-portfolio-access', 'dhm_portfolio_admin_page' );
}
add_action( 'admin_menu', 'dhm_portfolio_admin_menu' );

function dhm_portfolio_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$notice = '';
	if ( isset( $_POST['dhm_portfolio_action'] ) && check_admin_referer( 'dhm_portfolio_save' ) ) {
		$creds  = dhm_portfolio_creds();
		$action = sanitize_text_field( wp_unslash( $_POST['dhm_portfolio_action'] ) );
		if ( 'add' === $action ) {
			$label = sanitize_text_field( wp_unslash( isset( $_POST['label'] ) ? $_POST['label'] : '' ) );
			$user  = sanitize_text_field( wp_unslash( isset( $_POST['user'] ) ? $_POST['user'] : '' ) );
			$pass  = (string) wp_unslash( isset( $_POST['pass'] ) ? $_POST['pass'] : '' );
			$aud   = sanitize_key( wp_unslash( isset( $_POST['audience'] ) ? $_POST['audience'] : 'general' ) );
			if ( ! in_array( $aud, array( 'finance', 'defense', 'healthcare', 'general' ), true ) ) {
				$aud = 'general';
			}
			if ( '' !== $user && '' !== $pass ) {
				$creds[] = array(
					'label'    => $label,
					'user'     => $user,
					'pass'     => $pass,
					'audience' => $aud,
					'created'  => current_time( 'mysql' ),
				);
				update_option( 'dhm_portfolio_creds', $creds );
				$notice = 'Credential added.';
			} else {
				$notice = 'Username and password are both required.';
			}
		} elseif ( 'delete' === $action ) {
			$i = isset( $_POST['index'] ) ? intval( $_POST['index'] ) : -1;
			if ( isset( $creds[ $i ] ) ) {
				unset( $creds[ $i ] );
				update_option( 'dhm_portfolio_creds', array_values( $creds ) );
				$notice = 'Credential removed.';
			}
		}
	}
	$creds = dhm_portfolio_creds();
	$url   = home_url( '/' );
	?>
	<div class="wrap">
		<h1>Site Access</h1>
		<p>Credentials below are required to view the <a href="<?php echo esc_url( $url ); ?>" target="_blank">whole site</a> (Work, Experience, Skills, Contact and the portfolio). Add one per employer/submission, choose the site version they see, and share that login. A master login is also defined in <code>wp-config.php</code>, and you (logged into WordPress) always bypass the gate.</p>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>

		<h2>Add a credential</h2>
		<form method="post">
			<?php wp_nonce_field( 'dhm_portfolio_save' ); ?>
			<input type="hidden" name="dhm_portfolio_action" value="add">
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="dhm_label">Label</label></th><td><input name="label" id="dhm_label" type="text" class="regular-text" placeholder="e.g. Acme Corp - Senior SWE"></td></tr>
				<tr><th scope="row"><label for="dhm_user">Username</label></th><td><input name="user" id="dhm_user" type="text" class="regular-text" required></td></tr>
				<tr><th scope="row"><label for="dhm_pass">Password</label></th><td><input name="pass" id="dhm_pass" type="text" class="regular-text" required></td></tr>
				<tr><th scope="row"><label for="dhm_aud">Site type</label></th><td>
					<select name="audience" id="dhm_aud">
						<option value="general">General</option>
						<option value="finance">Finance</option>
						<option value="defense">Defense</option>
						<option value="healthcare">Healthcare</option>
					</select>
					<p class="description">Which version of the site this person sees after signing in.</p>
				</td></tr>
			</table>
			<?php submit_button( 'Add credential' ); ?>
		</form>

		<h2>Existing credentials</h2>
		<?php if ( empty( $creds ) ) : ?>
			<p>No custom credentials yet.</p>
		<?php else : ?>
			<table class="widefat striped">
				<thead><tr><th>Label</th><th>Username</th><th>Password</th><th>Site type</th><th>Added</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $creds as $i => $c ) : ?>
					<tr>
						<td><?php echo esc_html( isset( $c['label'] ) ? $c['label'] : '' ); ?></td>
						<td><code><?php echo esc_html( isset( $c['user'] ) ? $c['user'] : '' ); ?></code></td>
						<td><code><?php echo esc_html( isset( $c['pass'] ) ? $c['pass'] : '' ); ?></code></td>
						<td><?php echo esc_html( ucfirst( isset( $c['audience'] ) && $c['audience'] ? $c['audience'] : 'general' ) ); ?></td>
						<td><?php echo esc_html( isset( $c['created'] ) ? $c['created'] : '' ); ?></td>
						<td>
							<form method="post" onsubmit="return confirm('Remove this credential?');" style="margin:0;">
								<?php wp_nonce_field( 'dhm_portfolio_save' ); ?>
								<input type="hidden" name="dhm_portfolio_action" value="delete">
								<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
								<button type="submit" class="button-link delete">Remove</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}
