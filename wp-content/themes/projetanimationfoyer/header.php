<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <?php wp_head(); ?>
</head>

<header>

    <body <?php body_class(); ?>>
        <?php 
if ( is_user_logged_in() ):
	$current_user = wp_get_current_user(); 
?>
        <p>
            <?php echo $current_user->user_firstname; ?>
            <a href="<?php echo wp_logout_url(); ?>"> Déconnexion </a>
        </p>
        <?php else: ?>
        <p>
            <a href="<?php echo wp_login_url(); ?>"> Connexion </a>
        </p>
        <?php endif; ?>
        <div clas="crm-banner">
            <a href="<?php echo home_url( '/' ); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo-centre-de-readaptation-de-mulhouse-colors.svg"
                    alt="Logo">
            </a>
        </div>
        <div class="crm__header__menu">
        <?php 
	wp_nav_menu( 
        array( 
            'theme_location' => 'main', 
            'container' => 'ul', 
            'menu_class' => 'crm__header__menu', 
        ) 
    ); 
?>
        </div>
        <?php get_search_form(); ?>
</header>
<?php wp_body_open(); ?>