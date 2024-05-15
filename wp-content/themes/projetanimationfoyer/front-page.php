<?php get_header(); ?>
	<h1>FRONT-PAGE</h1>

	<?php
	$recent_posts = wp_get_recent_posts(array(
		'numberposts' => 1, 
		'post_status' => 'publish',
		'post_category' => 'benevoles'
	));
	foreach( $recent_posts as $post_item ) : ?>
		<li>
			<a href="<?php echo get_permalink($post_item['ID']) ?>">
				<?php echo get_the_post_thumbnail($post_item['ID'], 'full'); ?>
				<p class="slider-caption-class"><?php echo $post_item['post_title'] ?></p>
			</a>
		</li>
	<?php endforeach; ?>

<?php get_footer(); ?>

