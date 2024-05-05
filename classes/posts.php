<?php


class TSG_Posts extends TSG_Spectacles {

  /**
   * constructor
   */
  public function __construct() {

    add_action('tsg_post', array($this, 'print_post'));
    // add_action('add_meta_boxes', array($this, 'meta_boxes'), 10, 2);
    // add_action('edit_form_after_title', array($this, 'edit_form_after_title'));
    add_action('edit_form_after_editor', array($this, 'edit_form_after_editor'));

  }

  /**
	 *	@hook 'tsg_post'
	 */
	public function print_post() {
    global $post, $wpdb;

    $spectacles = array();

    $spectacle_ids = get_post_meta($post->ID, 'spectacle_id');

    if ($spectacle_ids) {

      $ids = implode(',', $spectacle_ids);

      $show_results = $wpdb->get_results(
        "SELECT spectacle_id, MIN(`date`) AS 'start', MAX(`date`) AS 'end'
        FROM {$wpdb->prefix}shows
        WHERE trash = 0 AND spectacle_id IN ($ids)
        GROUP BY spectacle_id"
      );

      $shows = array();

      foreach ($show_results as $show_result) {

        $shows[$show_result->spectacle_id]['start'] = $show_result->start;
        $shows[$show_result->spectacle_id]['end'] = $show_result->end;

      }

      $ordered_spectacle_ids = array_map(function($show_result) {
        return (int) $show_result->spectacle_id;
      }, $show_results);

      $spectacle_query = new WP_Query(array(
        'post_type' => 'spectacle',
        'post_status' => 'publish',
        'post__in' => $ordered_spectacle_ids,
        'orderby' => 'post__in',
        'order' => 'ASC',
        'posts_per_page' => -1
      ));

      foreach ($spectacle_query->posts as $spectacle) {

        $attachment_ids[] = get_post_meta($spectacle->ID, 'image', true);

      }

      require_once get_stylesheet_directory() . '/class-image.php';

      if ($attachment_ids) {

        Karma_Image::cache_images(array_values($attachment_ids));

      }

      foreach ($spectacle_query->posts as $spectacle) {

        if (isset($shows[$spectacle->ID]['start'])) {

          $start = $shows[$spectacle->ID]['start'];

        } else {

          $start = null;

        }

        if (isset($shows[$spectacle->ID]['end'])) {

          $end = $shows[$spectacle->ID]['end'];

        } else {

          $end = $start;

        }

        $text_date = get_post_meta($spectacle->ID, 'text_date', true);

        if (!$text_date && $start) {

          if ($start === $end) {

            $text_date = date_i18n('d F Y', strtotime($start));

          } else {

            $text_date = $this->format_date_range(strtotime($start), strtotime($end));

          }

        }

        $image_id = get_post_meta($spectacle->ID, 'image', true);

        if ($image_id) {

          $image = Karma_Image::get_image_source($image_id);

        } else {

          $image = null;

        }

      }

      $spectacles[] = array(
        'id' => $spectacle->ID,
        'title' => get_the_title($spectacle->ID),
        'ticket' => '#',
        'mediation' => '#',
        'permalink' => get_permalink($spectacle->ID),
        'subtitle' => get_post_meta($spectacle->ID, 'subtitle', true),
        'description' => get_post_meta($spectacle->ID, 'description', true),
        'image' => $image,
        'date' => $text_date,
        'start' => $start,
        'end' => $end
      );

    }

    include get_Stylesheet_directory() . '/include/blog-single.php';

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

    if ($post->post_type === 'post') {

      // do_action('karma_fields_post_field', $post->ID, array(
      //   'children' => array(
      //     array(
      //       'label' => 'Image',
      //       'type' => 'file',
      //       'key' => 'image',
      //       'uploader' => 'wp'
      //     )
      //   )
      // ));

    }

  }



  /**
   * @hook edit_form_after_editor
   */
  public function edit_form_after_editor($post) {

    if ($post->post_type === 'post') {

      do_action('karma_fields_post_field', $post->ID, array(
        // 'hiddenfield' => false,
        'children' => array(
          array(
            'label' => 'Saison',
            'type' => 'dropdown',
            'key' => 'saison',
            'options' => array(array('id' => '', 'name' => '-')),
            'driver' => 'taxonomy',
            'params' => array('taxonomy' => 'infomaniak', 'orderby' => 'name', 'order' => 'desc')
          ),
          // array(
          //   'label' => 'test',
          //   'type' => 'checkbox',
          //   'text' => 'asdfasdf',
          //   'key' => 'x234234'
          // ),
          array(
            'label' => 'Spectacles liés',
            'type' => 'checkboxes',
            'key' => 'spectacle_id',
            'columns' => 2,
            'driver' => 'posts',
            'params' => array(
              'post_type' => 'spectacle',
              'post_status' => 'any',
              'infomaniak' => array('||', array('join', array('getValue', 'saison'), ','), '99999999999'),
              'orderby' => 'post_title',
              'order' => 'asc'
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






}


new TSG_Posts;
