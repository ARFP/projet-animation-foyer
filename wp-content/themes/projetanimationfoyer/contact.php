<?php
/*
  Template Name: Contact
*/
	get_header();
	if ( have_posts() ) : while ( have_posts() ) : the_post();
?>
	
	<div class="contact-container">
	<div class="contact-title">
	<h1><?php the_title(); ?></h1>
	</div>
    <div class="contact-content">

    	<?php the_content(); ?>
		
    </div>
	</div>
<?php
	endwhile; endif;
	get_footer();
?>

