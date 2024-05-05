<?php


class TSG_Blog {

	public $version = '8';

	public function __construct() {

		add_action('tsg_blog', array($this, 'print_blog'));

		// add_action('wp_enqueue_scripts', array($this, 'scripts_styles'), 99);


	}

	//
	// /**
	//  *	Print theme styles and scripts
	//  */
	// public function scripts_styles() {
	//
	// 	wp_enqueue_script('tsg-build-7.1.2', get_template_directory_uri() . '/blog/js/build-v7.1.2.js', array(), $this->version, true);
	// 	wp_enqueue_script('tsg-blog', get_template_directory_uri() . '/blog/js/blog.js', array(), $this->version, true);
	// 	wp_enqueue_script('tsg-template-single-post', get_template_directory_uri() . '/blog/js/template-single-post.js', array('tsg-build-7.1.2', 'tsg-blog'), $this->version, true);
	// 	wp_enqueue_script('tsg-template-single-post-spectacles', get_template_directory_uri() . '/blog/js/template-single-post-spectacles.js', array('tsg-build-7.1.2', 'tsg-blog'), $this->version, true);
	//
	//
	// }

	/**
	 * @hook 'tsg_blog'
	 */
	public function print_blog() {
		global $wp_query;

		// $categories = get_terms(array(
		// 	'hide_empty' => false,
		// 	'taxonomy' => 'category'
		// ));

		$queried_obj = get_queried_object();

		// var_dump($queried_obj, is_a($queried_obj, 'WP_TERM'));

		// require_once get_template_directory() . '/class-image.php';

		require_once get_stylesheet_directory() . '/class-image.php';

		include get_stylesheet_directory().'/include/blog.php';

	}

}


new TSG_Blog;
