<?php
/*
Template Name: Login Form
*/

if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error_message = $user->get_error_message();
    } else {
        wp_redirect(home_url());
        exit;
    }
}
get_header();
?>

<div class="login-form">
    <h2>Login</h2>
    <?php if (!empty($error_message)): ?>
        <div class="error">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <input type="submit" value="Login">
        </p>
    </form>
</div>

<?php get_footer(); ?>

