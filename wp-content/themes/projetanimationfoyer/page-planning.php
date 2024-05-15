<?php get_header(); ?>
<h1>planning</h1>
<?php
if ( ! post_password_required() ) {
    echo do_shortcode( '[shiftcontroller4 hideui="filter-calendar,download"]' );
}
?>
<?php get_footer(); ?>

