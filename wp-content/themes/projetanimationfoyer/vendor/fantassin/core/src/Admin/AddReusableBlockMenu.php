<?php

namespace Fantassin\Core\WordPress\Admin;

use Fantassin\Core\WordPress\Contracts\AdminHooks;

class AddReusableBlockMenu implements AdminHooks {

	public function hooks() {
		add_action( 'admin_menu', [ $this, 'add_reusable_block_menu' ] );
	}

	function add_reusable_block_menu() {
		add_menu_page(
			__( 'Reusable Blocks', 'gutenberg' ),
			__( 'Reusable Blocks', 'gutenberg' ),
			'edit_posts',
			'edit.php?post_type=wp_block',
			'',
			'dashicons-block-default',
			30
		);
	}
}
