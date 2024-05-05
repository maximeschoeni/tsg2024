<?php




class TSG_Mediations extends TSG_Spectacles {

  /**
   * constructor
   */
  public function __construct() {

    add_action('init', array($this, 'init'));
    add_action('edit_form_after_title', array($this, 'edit_form_after_title'));
    add_action('tsg_mediation_page', array($this, 'print_mediation_page'));

  }




  /**
	 * @hook init
	 */
	public function init() {

    register_post_type('mediation', array(
			'labels'             => array(
				'name' => 'Mediations',
				'singular_name' => 'Mediation'
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'rewrite'            => array(
				'slug' => 'spectacle-mediation',
				'feeds' => false,
				'pages' => false
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array('title', 'editor', 'thumbnail')
		));

		register_taxonomy('mediation-category', array('mediation'), array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => 'Catégorie',
				'singular_name'              => 'Catégorie'
			),
			// 'public' => false,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'has_archive'        => true,
			'rewrite'               => array(
				'slug' => 'saison'
			)
		));

		register_taxonomy('infomaniak', array('spectacle', 'mediation'), array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => 'Saisons',
				'singular_name'              => 'Saison'
			),
			// 'public' => false,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'has_archive'        => true,
			'rewrite'               => array(
				'slug' => 'saison'
			)
		));

  }

  /**
   * @hook edit_form_after_title
   */
  public function edit_form_after_title($post) {

    if ($post->post_type === 'mediation') {

      do_action('karma_fields_post_field', $post->ID, array(
        // 'type' => 'group',
        'children' => array(

          array(
            'label' => 'Auteur',
            'type' => 'textarea',
            'key' => 'mediation_auteur'
          ),
          array(
            'label' => 'Titre (??!!)',
            'type' => 'textarea',
            'key' => 'mediation_title'
          ),
          array(
            'type' => 'group',
            'display' => 'flex',
            'children' => array(
              array(
                'label' => 'Date de début',
                'type' => 'date',
                'key' => 'start',
                'width' => 'auto'
              ),
              array(
                'label' => 'Date de fin',
                'type' => 'date',
                'key' => 'end',
                'width' => 'auto'
              ),
              array(
                'label' => 'Dates à afficher',
                'type' => 'input',
                'key' => 'date_range',
                'width' => 'auto'
              )
            )
          ),
          // array(
          //   'label' => 'Dates',
          //   'type' => 'input',
          //   'key' => 'date_range',
          //   'width' => 'auto'
          // ),
          array(
            'label' => 'Durée',
            'type' => 'input',
            'key' => 'duration',
            'width' => 'auto',
            'input' => array('type' => 'number')
          ),
          array(
            'label' => 'Médiation infos',
            'type' => 'textarea',
            'key' => 'info',
            'height' => '9em'
          ),
          // array(
          //   'label' => 'Image',
          //   'type' => 'file',
          //   'key' => '_thumbnail_id',
          //   'uploader' => 'wp'
          // ),
          // array(
          //   'label' => 'TEST',
          //   'type' => 'text',
          //   'content' => array('getValue', 'infomaniak')
          // ),
          array(
            'label' => 'Spectacles liés',
            'type' => 'checkboxes',
            'key' => 'parent_spectacle',
            'columns' => 2,
            'driver' => 'posts',
            'params' => array(
              'post_type' => 'spectacle',
              'post_status' => 'any',
              'infomaniak' => array('||', array('join', array('getValue', 'infomaniak'), ','), '99999999999'),
              'orderby' => 'post_title',
              'order' => 'asc'
            )
          )

        )
      ));

    }

  }


  /**
	 *	@hook 'tsg_mediation_page'
	 */
	public function print_mediation_page() {
    global $post, $wpdb;

    // $current_saison_ids = TSG_Saisons::get_current_saison_ids();

    $current_saison_ids = $this->get_current_saison_ids();

    $mediation_query = new WP_Query(array(
      'post_type' => 'mediation',
      'post_status' => 'publish',
      'orderby' => 'meta_value',
      'order' => 'DESC',
      'meta_key' => 'end',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'infomaniak',
          'field' => 'term_id',
          'terms' => $current_saison_ids,
        )
      )
    ));



    $attachment_ids = array();
    $spectacle_ids = array();
    $directory = array();

    foreach ($mediation_query->posts as $mediation) {

      $spectacle_parent_ids = get_post_meta($mediation->ID, 'parent_spectacle');

      $spectacle_ids = array_merge(
        $spectacle_ids,
        $spectacle_parent_ids
      );

      $attachment_ids[] = get_post_meta($mediation->ID, '_thumbnail_id', true);

      $categories = get_the_terms($mediation, 'mediation-category');

      if ($categories) {

        foreach ($categories as $category) {

          $directory[$category->term_id][] = $mediation;

        }

      }

    }



    if ($spectacle_ids) {

      $spectacle_ids = array_map('intval', $spectacle_ids);

      $spectacle_query = new WP_Query(array(
        'post_type' => 'spectacle',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__in' => $spectacle_ids
      ));

      foreach ($spectacle_query->posts as $spectacle) {

        $attachment_ids[] = get_post_meta($spectacle->ID, 'image', true);

      }

    }

    require_once get_stylesheet_directory() . '/class-image.php';

    if ($attachment_ids) {

      Karma_Image::cache_images(array_values($attachment_ids));

    }


    $categories = get_terms(array(
      'taxonomy' => 'mediation-category',
      'hide_empty' => false
    ));




    // $spectacles = array();
    //
    // foreach ($mediation_query->posts as $mediation) {
    //
    //   $spectacle_parent_ids = get_post_meta()
    //
    //   foreach ($spectacle_parents as $spectacle_id) {
    //     $image = null;
    //     $image_id = get_post_meta($spectacle_id, 'image', true);
    //     if () {
    //
    //     }
    //     $spectacles[] = array(
    //       'permalink' => get_permalink($spectacle_id),
    //
    //     );
    //   }
    //
    // }

    $mediation_spectacles = array();

    if ($spectacle_ids) {

      $ids = implode(',', array_map('intval', $spectacle_ids));

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

      foreach ($spectacle_query->posts as $spectacle) {

        $spectacle_id = $spectacle->ID;

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


        $mediation_spectacles[$spectacle->ID] = array(
          'id' => $spectacle->ID,
          'title' => get_the_title($spectacle->ID),
          'ticket' => '#',
          // 'mediation' => '#',
          'permalink' => get_permalink($spectacle->ID),
          'subtitle' => get_post_meta($spectacle->ID, 'subtitle', true),
          'description' => get_post_meta($spectacle->ID, 'description', true),
          'image' => $image,
          'date' => $text_date,
          'start' => $start,
          'end' => $end
        );

      }

    }


    include get_Stylesheet_directory() . '/include/page-mediation.php';

  }



}


new TSG_Mediations;
