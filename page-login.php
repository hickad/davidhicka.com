<?php
/*
Template Name: Login
*/

// Start session if not already started
if (!session_id()) {
    session_start();
}

get_header();

// Define a function to handle redirect
function redirect_to_page($url) {
    if (!headers_sent()) {
        wp_redirect(esc_url($url));
        exit; // Always call exit after wp_redirect
    } else {
        echo 'Error: headers already sent, cannot redirect';
    }
}

echo 'Trace: ' . $_POST['post_password'] . '</br>';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   

    if (post_password_required() && isset($_POST['post_password'])) {

        if (wp_check_password($_POST['post_password'], $post->post_password, $post->ID)) {

            $_SESSION['correct_password_entered'] = true;

            // Check if a redirection URL has been provided in the form
            if (!empty($_GET['redirect_to'])) 
            {
                redirect_to_page($_GET['redirect_to']);
            } else {
                // Optionally, redirect to a default page if no specific redirect was provided
                redirect_to_page(home_url());
            }
        } else {
            // Handle incorrect password case
            echo '<p>Incorrect password, please try again.</p>';
        }
    }
}

// Redirect if already logged in and redirect_to is present
if (!empty($_SESSION['correct_password_entered']) && $_SESSION['correct_password_entered'] && !empty($_GET['redirect_to'])) {
    redirect_to_page($_GET['redirect_to']);
}

echo "Password Required: " . (post_password_required() ? 'true' : 'false');
?>

<div class="main-wrapper">
    <!-- Show password form -->
    <div class="container text-center mt-5">
        <?php 
        // Modify the form to include hidden input for redirection
        if (isset($_GET['redirect_to'])) {
            $form = get_the_password_form();
            $hidden_input = '<input type="hidden" name="redirect_to" value="' . esc_attr($_GET['redirect_to']) . '">';
            $form = str_replace('</form>', $hidden_input . '</form>', $form);
            echo $form;
        } else {
            echo get_the_password_form();
        }
        ?>
    </div>
</div><!--//main-wrapper-->
<?php
get_footer();
?>
