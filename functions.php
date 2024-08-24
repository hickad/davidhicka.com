<?php
/**
 * DigitalResume functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package DigitalResume
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function digitalresume_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on DigitalResume, use a find and replace
	 * to change 'digitalresume' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('digitalresume', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'digitalresume'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'digitalresume_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 250,
			'width' => 250,
			'flex-width' => true,
			'flex-height' => true,
		)
	);

}
add_action('after_setup_theme', 'digitalresume_setup');


function create_portfolio_post_type() {
    $args = array(
        'labels' => array(
            'name' => __('Portfolio', 'text-domain'), // 'text-domain' should be replaced with your theme's or plugin's text domain
            'singular_name' => __('Portfolio', 'text-domain'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_icon' => 'dashicons-art', // Use a Dashicon for the menu icon.
        'rewrite' => array('slug' => 'portfolio'),
        'taxonomies' => array('category'), // Associate the standard 'category' taxonomy with this CPT
    );
    register_post_type('portfolio', $args);
}
add_action('init', 'create_portfolio_post_type');




/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function digitalresume_content_width()
{
	$GLOBALS['content_width'] = apply_filters('digitalresume_content_width', 640);
}
add_action('after_setup_theme', 'digitalresume_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function digitalresume_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'digitalresume'),
			'id' => 'sidebar-1',
			'description' => esc_html__('Add widgets here.', 'digitalresume'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action('widgets_init', 'digitalresume_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function digitalresume_scripts_and_styles() {
    
    // Enqueue main stylesheet
    wp_enqueue_style('digitalresume-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css'));

    // Enqueue FontAwesome
    wp_enqueue_style('fontawesome', get_template_directory_uri() . '/assets/plugins/fontawesome/css/all.css', array(), '5.15.1');

    // Enqueue navigation script
    $bundle_path = get_template_directory_uri() . '/bundle.js';
    wp_enqueue_script('digitalresume-js', add_query_arg('ver', filemtime(get_template_directory() . '/bundle.js'), $bundle_path), array(), null, true);

    // Enqueue comment-reply script on single posts/pages when comments are open
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Register the scripts
    // wp_register_script('imagesloaded', get_template_directory_uri() . '/assets/plugins/imagesloaded.pkgd.min.js', array('jquery'), '1.0.0', true);
    wp_register_script('isotope', get_template_directory_uri() . '/assets/plugins/isotope.pkgd.min.js', array('jquery'), '1.0.0', true);
    wp_register_script('isotope-custom', get_template_directory_uri() . '/assets/js/isotope-custom.js', array('jquery', 'isotope'), '1.0.0', true);

    // Enqueue the scripts
    // wp_enqueue_script('imagesloaded');
    wp_enqueue_script('isotope');
    wp_enqueue_script('isotope-custom');

    wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'digitalresume_scripts_and_styles');


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';




class Custom_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        // Determine the icon class based on the menu item title
        $icon_class = '';
        switch ($item->title) {
            case 'About':
                $icon_class = 'fa-user';
                break;
            case 'Blog':
                $icon_class = 'fa-blog';
                break;
            case 'Contact':
                $icon_class = 'fa-envelope-open-text';
                break;
            case 'Resume':
                $icon_class = 'fa-file-alt';
                break;
            case 'Projects':
                $icon_class = 'fa-laptop-code';
                break;
            default:
                $icon_class = 'fa-fw'; // Default icon class if needed
                break;
        }

        // Add main <li> tag with class 'nav-item'
        $output .= "<li class='nav-item'>";

        // Add <a> tag
        $output .= '<a class="nav-link' . ($item->current ? ' active' : '') . '" href="' . esc_url($item->url) . '">';
        
        // Add the icon
        if (!empty($icon_class)) {
            $output .= "<i class='fas " . esc_attr($icon_class) . " fa-fw me-2'></i>";
        }

        $output .= esc_html($item->title);

        if ($item->current) {
            $output .= '<span class="sr-only">(current)</span>';
        }

        $output .= '</a>';
    }
}


add_filter('the_password_form', 'custom_password_form');

function custom_password_form() {
    global $post;

    // Debug: Start logging
    error_log('Starting custom_password_form function.');

    // Check if there's a custom error flag in the URL
    $error = (isset($_GET['password_failed']) && $_GET['password_failed'] == '1');
    if ($error) {
        error_log('Password failed error detected.');
    }

    // Capture the redirect URL if provided
    $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
    error_log('Redirect to: ' . $redirect_to);

    $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
    $errorMessage = $error ? '<div class="alert alert-danger" role="alert">Incorrect password, please try again.</div>' : '';
    $form = '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" class="container post-password-form form-inline" method="POST">
        ' . $errorMessage . '
        <input type="hidden" name="redirect_to" value="' . esc_attr($redirect_to) . '">
        <div class="form-group">
            <label for="' . $label . '" class="label-control">This content is password protected. To view it please enter your password below:</label>
            <div class="d-grid mt-3 gap-2 mx-auto" style="max-width: 250px;">
                <input name="post_password" id="' . $label . '" type="password" class="form-control mr-2" placeholder="Password">
                <button type="submit" class="btn btn-primary" name="Submit">Submit</button>
            </div>
        </div>
    </form>';

    // Debug: Log the entire form output
    error_log('Form HTML: ' . $form);

    return $form;
}


// Handling redirection and password check
add_action('wp', 'check_post_password');

function check_post_password() {
    global $post;
    if (isset($_POST['post_password']) && !empty($post)) {
        // Check if password matches
        if (!wp_check_password($_POST['post_password'], $post->post_password, $post->ID)) {
            // Redirect back to the post with a custom error flag
            wp_redirect(esc_url(add_query_arg('password_failed', '1', get_permalink($post->ID))));
            exit;
        }
    }
}









