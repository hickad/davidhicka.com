<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DigitalResume
 */
?>

<footer class="page-footer">
  <div class="container">
    <div class="row">
      <div class="col l6 s12">
        <h5 class="white-text">Footer Content</h5>
        <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
      </div>
      <div class="col l4 offset-l2 s12">
        <ul>
          <?php
          wp_nav_menu(
            array(
              'theme_location' => 'menu-1',
              'menu_id' => 'primary-menu',
            )
          );
          ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-copyright">
    <div class="container">

    </div>
  </div>
</footer>
</div><!-- #page -->


	<!-- Javascript -->
	<script src="assets/plugins/popper.min.js"></script> 
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	
	<script src="assets/plugins/tiny-slider/min/tiny-slider.js"></script>
	<script src="assets/js/testimonials.js"></script>

	<!-- Style Switcher (REMOVE ON YOUR PRODUCTION SITE) -->
	<script src="assets/js/demo/style-switcher.js"></script>
	
	<!-- Dark Mode -->
	<script src="assets/plugins/js-cookie.min.js"></script>
	<script src="assets/js/dark-mode.js"></script>   


<?php wp_footer(); ?>

</body>

</html>