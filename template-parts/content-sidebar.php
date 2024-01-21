<?php
/**
 * Template part for displaying side navigation
 *
 * @package DigitalResume
 */

?>



<header class="header text-center">	    
		<div class="force-overflow">
			<h1 class="blog-name pt-lg-4 mb-0"><a class="no-text-decoration" href="index.html">David Hicka</a></h1>
			
			<nav class="navbar navbar-expand-lg navbar-dark" >
				
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				
				<div id="navigation" class="collapse navbar-collapse flex-column" >
					<div class="profile-section pt-3 pt-lg-0">
						<img class="profile-image mb-3 rounded-circle mx-auto" src="./assets/images/profile.png" alt="image" >			
						
						<div class="bio mb-3">Hi, my name is David Hicka and I'm a senior software engineer. Welcome to my personal website!</div><!--//bio-->
						<ul class="social-list list-inline py-2 mx-auto">
							<li class="list-inline-item"><a href="#"><i class="fab fa-twitter fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="#"><i class="fab fa-linkedin-in fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="#"><i class="fab fa-github-alt fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="#"><i class="fab fa-stack-overflow fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="#"><i class="fab fa-codepen fa-fw"></i></a></li>
						</ul><!--//social-list-->
						<hr> 
					</div><!--//profile-section-->
					
					<div class="my-2">
						<a class="btn btn-primary" href="contact.html" target="_blank"><i class="fas fa-paper-plane me-2"></i>Hire Me</a>
					</div>
					
					<div class="dark-mode-toggle text-center w-100">
						<hr class="mb-4">
					    <h4 class="toggle-name mb-3 "><i class="fas fa-adjust me-1"></i>Dark Mode</h4>
					    
					    <input class="toggle" id="darkmode" type="checkbox">
					    <label class="toggle-btn mx-auto mb-0" for="darkmode"></label>
					    
					</div><!--//dark-mode-toggle-->
					
				</div>
				
			</nav>
		</div><!--//force-overflow-->
	</header>