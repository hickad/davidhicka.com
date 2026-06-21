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

/** True if user/pass matches the master credential or any custom one. */
function dhm_portfolio_authorize( $user, $pass ) {
	$user = (string) $user;
	$pass = (string) $pass;
	if ( '' === $user || '' === $pass ) {
		return false;
	}
	if ( defined( 'PORTFOLIO_AUTH_USER' ) && defined( 'PORTFOLIO_AUTH_PASS' )
		&& hash_equals( (string) PORTFOLIO_AUTH_USER, $user )
		&& hash_equals( (string) PORTFOLIO_AUTH_PASS, $pass ) ) {
		return true;
	}
	foreach ( dhm_portfolio_creds() as $c ) {
		if ( ! empty( $c['user'] ) && isset( $c['pass'] )
			&& hash_equals( (string) $c['user'], $user )
			&& hash_equals( (string) $c['pass'], $pass ) ) {
			return true;
		}
	}
	return false;
}

/** Read submitted Basic Auth credentials (with a PHP-FPM header fallback). */
function dhm_portfolio_submitted() {
	$u = isset( $_SERVER['PHP_AUTH_USER'] ) ? $_SERVER['PHP_AUTH_USER'] : '';
	$p = isset( $_SERVER['PHP_AUTH_PW'] ) ? $_SERVER['PHP_AUTH_PW'] : '';
	if ( '' === $u ) {
		$h = '';
		if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			$h = $_SERVER['HTTP_AUTHORIZATION'];
		} elseif ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
			$h = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
		}
		if ( 0 === stripos( $h, 'basic ' ) ) {
			$decoded = base64_decode( substr( $h, 6 ), true );
			if ( false !== $decoded && false !== strpos( $decoded, ':' ) ) {
				list( $u, $p ) = explode( ':', $decoded, 2 );
			}
		}
	}
	return array( $u, $p );
}

/** Serve the gated portfolio at the portfolio archive URL (/portfolio/). */
function dhm_portfolio_serve() {
	if ( ! is_post_type_archive( 'portfolio' ) ) {
		return;
	}
	list( $u, $p ) = dhm_portfolio_submitted();
	if ( ! dhm_portfolio_authorize( $u, $p ) ) {
		nocache_headers();
		header( 'WWW-Authenticate: Basic realm="David Hicka - Portfolio"' );
		status_header( 401 );
		echo 'Authorization required.';
		exit;
	}
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
add_action( 'template_redirect', 'dhm_portfolio_serve' );

/* Admin: Settings -> Portfolio Access (manage per-employer credentials). */
function dhm_portfolio_admin_menu() {
	add_options_page( 'Portfolio Access', 'Portfolio Access', 'manage_options', 'dhm-portfolio-access', 'dhm_portfolio_admin_page' );
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
			if ( '' !== $user && '' !== $pass ) {
				$creds[] = array(
					'label'   => $label,
					'user'    => $user,
					'pass'    => $pass,
					'created' => current_time( 'mysql' ),
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
	$url   = home_url( '/portfolio/' );
	?>
	<div class="wrap">
		<h1>Portfolio Access</h1>
		<p>Credentials below grant access to <a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $url ); ?></a>. Add one per employer/submission and share that login. A master login is also defined in <code>wp-config.php</code>.</p>
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
			</table>
			<?php submit_button( 'Add credential' ); ?>
		</form>

		<h2>Existing credentials</h2>
		<?php if ( empty( $creds ) ) : ?>
			<p>No custom credentials yet.</p>
		<?php else : ?>
			<table class="widefat striped">
				<thead><tr><th>Label</th><th>Username</th><th>Password</th><th>Added</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $creds as $i => $c ) : ?>
					<tr>
						<td><?php echo esc_html( isset( $c['label'] ) ? $c['label'] : '' ); ?></td>
						<td><code><?php echo esc_html( isset( $c['user'] ) ? $c['user'] : '' ); ?></code></td>
						<td><code><?php echo esc_html( isset( $c['pass'] ) ? $c['pass'] : '' ); ?></code></td>
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
