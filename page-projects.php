<?php
/*
Template Name: projects
*/

// Start session if not already started
if (!session_id()) {
    session_start();
}

get_header();

// If password is posted and correct, set the session flag
if (post_password_required()) {
    if (isset($_POST['post_password']) && wp_check_password($_POST['post_password'], $post->post_password, $post->ID)) {
        $_SESSION['correct_password_entered'] = true;
    } else {
        $_SESSION['correct_password_entered'] = false; // Ensure flag is reset if password fails
    }
}

// Check if the correct password has been entered
$correct_password_entered = isset($_SESSION['correct_password_entered']) && $_SESSION['correct_password_entered'];
echo "Password Required: " . (post_password_required() ? 'true' : 'false');

?>
<div class="main-wrapper">
    <section class="cta-section theme-bg-light py-5">
        <div class="container text-center single-col-max-width">
            <h2 class="heading">Projects</h2>
            <div class="intro">
                <p>Welcome to my online projects. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. I'm taking on freelance work at the moment. Want some help building your software?</p>
            </div>
            <a class="btn btn-primary" href="<?php echo home_url('/contact/'); ?>">
                <i class="fas fa-paper-plane me-2"></i>Contact Me
            </a>
        </div>
    </section>

    <?php if ($correct_password_entered): ?>

        <!-- Display project sections only if password is correct -->
        <section class="projects-list px-3 py-5 p-md-5">
            <div class="container">
                <div class="text-center">
                    <ul id="filters" class="filters mb-5 mx-auto ps-0">
                        <li class="type active mb-3 mb-lg-0" data-filter="*">All</li>
                        <?php
                        $categories = get_categories(array(
                            'orderby' => 'name',
                            'order'   => 'ASC',
                            'hide_empty' => true, // Optionally change to false to show empty categories
                        ));

                        foreach ($categories as $category) {
                            $filter_class = strtolower(preg_replace('/\s+/', '', $category->name));
                            echo '<li class="type mb-3 mb-lg-0" data-filter=".' . esc_attr($filter_class) . '">' . esc_html($category->name) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <!-- Portfolio grid -->
                <div class="project-cards row isotope">
                    <?php
                    $args = array(
                        'post_type' => 'portfolio',
                        'posts_per_page' => -1,
                    );
                    $portfolio_query = new WP_Query($args);
                    if ($portfolio_query->have_posts()) : 
                        while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                            $categories = get_the_terms(get_the_ID(), 'category');
                            $class_names = array_map(function($category) { return strtolower($category->slug); }, (array) $categories);
                            $class_string = join(" ", $class_names);
                            ?>
                            <div class="isotope-item col-md-6 mb-5 <?php echo esc_attr($class_string); ?>">
                                <div class="card project-card">
                                    <!-- Card content -->
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
