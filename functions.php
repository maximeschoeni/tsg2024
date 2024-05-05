<?php



class TSG {

	var $version = '003';

	/**
	 *	Constructor
	 */
	public function __construct() {
		$options = get_option('karma');


		add_action('after_setup_theme', array($this, 'setup'));
    add_action('wp_enqueue_scripts', array($this, 'scripts_styles'), 99);

		require get_template_directory() . '/classes/spectacles.php';
		require get_template_directory() . '/classes/shows.php';
		require get_template_directory() . '/classes/saisons.php';
		require get_template_directory() . '/classes/pages.php';
		require get_template_directory() . '/classes/mediations.php';
		require get_template_directory() . '/classes/posts.php';

		require get_template_directory() . '/classes/options.php';

		// require get_template_directory() . '/classes/dp.php';
		// require get_template_directory() . '/classes/evenements.php';
		// require get_template_directory() . '/classes/resources.php';
		// require get_template_directory() . '/classes/works.php';
		// require get_template_directory() . '/classes/pages.php';
		// require get_template_directory() . '/classes/catalogues.php';


		// add_filter('wp_nav_menu_objects', array($this, 'nav_menu_object'), 10, 2);



		// add_filter( 'image_send_to_editor', array($this, 'insert_image'), 10, 5 );
		// add_filter('disable_captions', function() {return true;});



		// add_filter('image_add_caption_text', array($this, 'image_add_caption_text'), 10, 2);
		// remove_action('media_buttons', 'media_buttons');

	}


	/**
	 * @filter 'image_send_to_editor'
	 */
	public function insert_image($html, $id, $caption, $title = null, $align = null) {

		require_once get_stylesheet_directory() . '/class-image.php';

		$image = Karma_Image::get_image_source($id);

		$html = "class=\"wp-image-{$id}\" width=\"{$image['width']}\" height=\"{$image['height']}\" src=\"{$image['src']}\" alt=\"{$image['alt']}\"";

		if (!empty($image['sizes'])) {

			$srcset = implode(',', array_map(function($size) {
				return "{$size['src']} {$size['width']}w";
			}, $image['sizes']));

			$html = "$html srcset=\"$srcset\" sizes=\"(min-width: 900px) 50vw, 100vw\"";

		}

		$html = "<img $html />";

		if ($caption) {

			$html = "$html<figcaption>$caption</figcaption>";

		}

		$html = "<figure>$html</figure>";

		return $html;

	}



	/** DO NOT WORK ! (this hook is not called when changing caption)
	 * @filter 'image_add_caption_text'
	 */
	public function image_add_caption_text($caption, $id) {

		add_filter('image_add_caption_shortcode', function($shcode, $html) use($caption, $id) {

			return $this->insert_image('', $id, $caption);

		});

		return $caption;
	}





	//
	// /**
	//  * @filter 'image_add_caption_shortcode'
	//  */
	// public function image_add_caption($shcode, $html) {
	//
	//
	//
	//
	// }




	/**
	 *	Theme Setup
	 */
	public function setup() {

		load_theme_textdomain( 'tsg', get_stylesheet_directory() . '/languages' );

		add_theme_support( 'post-thumbnails' );
    add_theme_support( 'align-wide' );

		// add_theme_support( 'html5', array('figure', 'caption'));



		// add_post_type_support( 'page', 'excerpt' );



		add_editor_style();

		register_nav_menus(array(
			'top_menu' => 'Top Menu',
			'footer_menu' => 'Footer Menu'
			// 'category_menu' => 'Category Menu'
		));

		add_action('sublanguage_custom_switch', array($this, 'language_switch'), 10, 2);

		add_action('admin_menu', array($this, 'admin_menu'));

		add_filter('wp_editor_settings', array($this, 'editor_settings'), 10, 2); // Editor remove wp_autop

	}

	/**
	 *	Print theme styles and scripts
	 */
	public function scripts_styles() {

    wp_dequeue_style( 'wp-block-library' );


		// disable cache !
		// $this->version = date('his');


		wp_enqueue_style('stylesheet', get_stylesheet_uri(), array(), $this->version);

		// wp_enqueue_script('cookies', get_stylesheet_directory_uri().'/js/cookies.js', array(), $this->version, false);
		wp_enqueue_script('sticky', get_stylesheet_directory_uri().'/js/sticky.js', $this->version, false);
		wp_enqueue_script('abduct', get_stylesheet_directory_uri().'/js/abduct.js', $this->version, false);
		wp_enqueue_script('newsletter', get_stylesheet_directory_uri().'/js/newsletter.js', $this->version, false);

    // wp_enqueue_script('tracker', get_stylesheet_directory_uri().'/js/tracker.js', array(), $this->version, false);
    // wp_enqueue_script('accordeon', get_stylesheet_directory_uri().'/js/accordeon.js', $this->version, false);


	}

	/**
	 * @hook 'sublanguage_custom_switch'
	 */
	public function language_switch($languages, $sublanguage) {

		include get_stylesheet_directory().'/include/language-switch.php';

	}

	/**
	 * @hook 'wp_nav_menu_objects'
	 */
	public function nav_menu_object($sorted_menu_items, $args) {

		foreach ($sorted_menu_items as $item) {

			$title = $item->title;

			if ($item->description) {

				$title = $item->description;

			}

			$lines = explode("%", $title);

			$item->title = '<span class="stretch">'.implode('</span><span class="stretch">', $lines).'</span>';

		}

		return $sorted_menu_items;

	}

	/**
	 *	@hook admin_menu
	 */
	public function admin_menu() {

  	// remove_menu_page('edit.php');

	}




	/**
	 *	Editor remove wp_autop
	 */
	public function editor_settings($settings, $editor_id) {

		$settings['wpautop'] = false;

		return $settings;
	}

}

new TSG;
