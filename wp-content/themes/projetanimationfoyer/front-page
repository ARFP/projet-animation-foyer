<?php get_header(); ?>
	<h1>FRONT-PAGE</h1>
	<h2>Dernières News</h2>
<?php
	$args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'taxonomy' => 'category'
    );

    $categories = get_categories( $args );

    foreach( $categories as $category ) {
        $args = array(
            'post_type' => 'post',
			'numberposts' => 1,
            // 'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'name',
                    'terms' => $category->name
                )
            )
        );
        $posts = get_posts( $args );
        echo '<h2>' . $category->name. '</h2>';
        echo '<ul>';
        foreach( $posts as $post ) {
            echo '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . the_excerpt() . '</a></li>';
        }
        echo '</ul>';
    } ?>	

<?php get_footer(); ?>

