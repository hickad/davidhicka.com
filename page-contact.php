<?php
/*
Template Name: contact
*/

get_header();
?>

    <div class="main-wrapper">
	    <section class="cta-section theme-bg-light py-5">
		    <div class="container text-center single-col-max-width">
			    <h2 class="heading">Contact</h2>
			    <div class="intro">
			    <p>Want to get connected? Follow me on LinkedIn.</p>
			    <ul class="list-inline mb-0">		            
				<li class="list-inline-item mb-3"><a class="linkedin" href="https://www.linkedin.com/in/davidhicka/" target="_blank"><i class="fab fa-linkedin-in fa-fw fa-lg"></i></a></li>
	            </ul><!--//social-list-->
			    
			</div><!--//container-->
	    </section>
	    <section class="contact-section px-3 py-5 p-md-5">
		    <div class="container">
			<?php echo do_shortcode('[contact-form-7 id="01d4522" title="Contact"]'); ?>
		    </div><!--//container-->
	    </section>
	
    </div><!--//main-wrapper-->

    <?php
get_footer();