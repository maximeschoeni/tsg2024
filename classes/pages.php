<?php


class TSG_Pages {

  /**
   * constructor
   */
  public function __construct() {

    add_action('tsg_page', array($this, 'print_page'));

    add_action('add_meta_boxes', array($this, 'meta_boxes'), 10, 2);

    add_action('edit_form_after_title', array($this, 'edit_form_after_title'));
    add_action('edit_form_after_editor', array($this, 'edit_form_after_editor'));

  }

  /**
	 *	@hook 'tsg_page'
	 */
	public function print_page() {
    global $post;


    $subpages_query = new WP_Query(array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'post_parent' => $post->ID,
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'posts_per_page' => -1
    ));

    require_once get_stylesheet_directory() . '/class-image.php';

    $attachment_ids = array();

    $image_id = get_post_meta($post->ID, 'image', true);

    if ($image_id) {

      $attachment_ids[] = $image_id;

    }

    foreach ($subpages_query->posts as $subpage) {

      $image_id = get_post_meta($subpage->ID, 'image', true);

      if ($image_id) {

        $attachment_ids[] = $image_id;

      }

    }

    if ($attachment_ids) {

      Karma_Image::cache_images(array_values($attachment_ids));

    }


    include get_Stylesheet_directory() . '/include/page.php';

  }




  /**
   * @hook add_meta_boxes
   */
  public function meta_boxes($post_type, $post) {

    // add_meta_box(
    //   'columns',
    //   'Columns',
    //   array($this, 'columns_meta_box'),
    //   array('page'),
    //   'normal',
    //   'default'
    // );

  }




  /**
   * @hook edit_form_after_title
   */
  public function edit_form_after_title($post) {

    if ($post->post_type === 'page') {

      do_action('karma_fields_post_field', $post->ID, array(
        'children' => array(
          array(
            'label' => 'Image',
            'type' => 'file',
            'key' => 'image',
            'uploader' => 'wp'
          )
        )
      ));

    }

  }



  /**
   * @hook edit_form_after_editor
   */
  public function edit_form_after_editor($post) {

    if ($post->post_type === 'page') {

      do_action('karma_fields_post_field', $post->ID, array(
        'hiddenfield' => false,
        'children' => array(
          array(
            'type' => 'group',
            'display' => 'flex',
            'children' => array(
              array(
                'type' => 'tinymce',
                'key' => 'left-column',
                'label' => 'Left column',
                'width' => '1fr',
                'header' => array(
                  'children' => array(
                    array(
                      'type' => 'format',
                      'options' => array(
                        array('id' => "", 'name' => "Format"),
                        array('id' => "h3", 'name' => "H3")
                      )
                    ),
                    'bold',
                    'italic',
                    'link'
                  )
                )
              ),
              array(
                'type' => 'tinymce',
                'key' => 'right-column',
                'label' => 'Right column',
                'width' => '1fr',
                'header' => array(
                  'children' => array(
                    array(
                      'type' => 'format',
                      'options' => array(
                        array('id' => "", 'name' => "Format"),
                        array('id' => "h3", 'name' => "H3")
                      )
                    ),
                    'bold',
                    'italic',
                    'link'
                  )
                )
              )
            )
          )
        )
      ));

    }

  }



  /**
   * @callback add_meta_box
   */
  public function columns_meta_box($post) {



  }


  //
  // /**
	//  *	@hook 'actwall_home_header'
	//  */
	// public function print_our_engagement_body() {
  //   global $post;
  //
  //   include get_Stylesheet_directory() . '/include/page/body-our-engagement.php';
  //
  // }
  //
  //




}


new TSG_Pages;
