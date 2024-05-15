<?php get_header(); ?>
	<h1>FRONT-PAGE</h1>
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

	<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
  
		<article class="post">
			<h2><?php the_title(); ?></h2>
      
        	<?php the_post_thumbnail(); ?>
            
            <p class="post__meta">
                Publié le <?php the_time( get_option( 'date_format' ) ); ?> 
                par <?php the_author(); ?> • <?php comments_number(); ?>
            </p>
            
      		<?php the_excerpt(); ?>
              
      		<p>
                <a href="<?php the_permalink(); ?>" class="post__link">Lire la suite</a>
            </p>
		</article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>

