<?php




class TSG_Spectacles {

  /**
   * constructor
   */
  public function __construct() {

    // add_action('laribot_slideshow', array($this, 'print_slideshow'));
    // add_action('laribot_work_slideshow', array($this, 'print_work_slideshow'));
    // add_action('laribot_category_works', array($this, 'print_category_works'));
    // add_action('laribot_works', array($this, 'print_works'));
    add_action('tsg_home_slideshow', array($this, 'print_home_slideshow'));
    add_action('tsg_home_agenda', array($this, 'print_home_agenda'));


    // add_action('tsg_spectacle_slideshow', array($this, 'print_spectacle_slideshow'));
    add_action('tsg_single_spectacle', array($this, 'print_single_spectacle'));

    add_action('tsg_future_spectacles', array($this, 'print_future_spectacles'));



    // add_action('actwall_bio_body', array($this, 'print_body'));

    add_action('init', array($this, 'init'));
    add_action('add_meta_boxes', array($this, 'meta_boxes'), 10, 2);
    // add_action('karma_fields_init', array($this, 'karma_fields_init'));

    // add_action( 'restrict_manage_posts', array($this, 'taxonomy_filter'));

    add_action('edit_form_after_title', array($this, 'subtitle_field'));

    add_action('rest_api_init', array($this, 'rest_api_init'));

    add_filter('karma_fields_posts_meta_sql', array($this, 'karma_fields_posts_meta_sql'), 10, 3);

  }

  /**
	 * @filter 'karma_fields_posts_meta_sql'
	 */
  public function karma_fields_posts_meta_sql($sql, $ids, $ids_string) {
    global $wpdb;

    return "SELECT
      meta_value AS 'value',
      meta_key AS 'key',
      post_id AS 'id'
      FROM $wpdb->postmeta
      WHERE post_id IN ($ids_string) AND meta_key != 'trash' ORDER BY meta_id";

  }

  /**
	 *	@hook 'rest_api_init'
	 */
	public function rest_api_init() {

    register_rest_route('tsg/v1', '/saisons', array(
			'methods' => 'GET',
			'callback' => array($this, 'rest_saisons'),
			'permission_callback' => '__return_true'
		));

    register_rest_route('tsg/v1', '/calendar-shows/(?P<saison_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'rest_calendar_shows'),
			'permission_callback' => '__return_true'
		));

    register_rest_route('tsg/v1', '/future-spectacles', array(
			'methods' => 'GET',
			'callback' => array($this, 'rest_future_spectacles'),
			'permission_callback' => '__return_true'
		));

    register_rest_route('tsg/v1', '/archives-saison/(?P<saison_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'rest_archives_saison'),
			'permission_callback' => '__return_true'
		));



  }



  /**
	 * @hook init
	 */
	public function init() {

    register_post_type('spectacle', array(
			'labels'             => array(
				'name' => 'Spectacle',
				'singular_name' => 'Spectacle'
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies' => array('post_tag'),
			'supports'           => array('title', 'editor')
		));


		register_taxonomy('accessibilite', array('spectacle', 'mediation'), array(
			'hierarchical'          => true,
			'labels'                => array(
				'name'                       => 'Accessibilité',
				'singular_name'              => 'Accessibilité'
			),
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => false,
			'rewrite'               => false
		));

  }


  /**
   * @hook add_meta_boxes
   */
  public function meta_boxes($post_type, $post) {

    // add_meta_box(
    //   'details',
    //   'Details',
    //   array($this, 'details_meta_box'),
    //   array('spectacle'),
    //   'normal',
    //   'default'
    // );

  }

  /**
   * @hook edit_form_after_title
   */
  public function subtitle_field($post) {

    if ($post->post_type === 'spectacle') {

      $this->details_meta_box($post);

    }

  }


  /**
   * @callback add_meta_box
   */
  public function details_meta_box($post) {

    do_action('karma_fields_post_field', $post->ID, array(
      // 'type' => 'group',
      'children' => array(

        array(
          'label' => 'Auteur',
          'type' => 'textarea',
          'key' => 'subtitle'
        ),
        array(
          'label' => 'Infos complémentaires',
          'type' => 'textarea',
          'key' => 'description'
        ),
        array(
          'label' => 'Durée du spectacle',
          'type' => 'input',
          'key' => 'duration',
          'width' => '10em',
          'input' => array('type' => 'number')
        ),
        array(
          'label' => 'Lien de réservation alternatif',
          'type' => 'input',
          'key' => 'alt_res',
          'placeholder' => 'https://...'
        ),


        array(
          'type' => 'group',
          'display' => 'flex',
          'children' => array(
            array(
              'type' => 'files',
              'label' => 'Vignette',
              'max' => 1,
              'key' => 'image',
              'uploader' => 'wp',
              'width' => 'auto'
            ),
            array(
              'type' => 'files',
              'label' => 'Overlay',
              'max' => 1,
              'key' => 'overlay',
              'uploader' => 'wp',
              'width' => 'auto'
            )
          )
        ),
        array(
          'type' => 'files',
          'label' => 'Images',
          'key' => 'images',
          'uploader' => 'wp'
        ),

        array(
          'label' => 'Représentations',
          'type' => 'table',
          'driver' => 'shows',
          'params' => array('spectacle_id' => array('getValue', 'id')),
          'width' => 'auto',
          'body' => array(
            'type' => 'grid',
            'children' => array(
              array(
                'label' => 'Date',
                'type' => 'date',
                'key' => 'date',
                'width' => '9em'
              ),
              array(
                'label' => 'Heure',
                'type' => 'input',
                'key' => 'hour',
                'width' => '6em'
              ),
              array(
                'label' => 'Statut',
                'type' => 'dropdown',
                'key' => 'status',
                'options' => array(
                  array('id' => 0, 'name' => 'Auto'),
                  array('id' => 1, 'name' => 'Presque complet'),
                  array('id' => 2, 'name' => 'Complet'),
                  array('id' => 3, 'name' => 'Pas de réservation')
                )
              ),
              array(
                'label' => 'Jauge places',
                'type' => 'input',
                'key' => 'place',
                'width' => '8em'
              ),
              array(
                'label' => 'Réservations',
                'type' => 'text',
                'content' => '0',
                'width' => '8em'
              ),
              array(
                'label' => 'Statut ventes',
                'type' => 'dropdown',
                'key' => 'infomaniak',
                'options' => array(
                  array('id' => 0, 'name' => 'Auto'),
                  array('id' => 1, 'name' => 'Presque complet'),
                  array('id' => 2, 'name' => 'Complet'),
                  array('id' => 3, 'name' => 'Pas de réservation')
                )
              )

            )
          ),
          'footer' => array(
            'type' => 'footer',
            'children' => array(
              array(
                'type' => 'add',
                'text' => 'Ajouter une date'
              ),
              array(
                'type' => 'delete',
                'text' => 'Supprimer'
              )
            )
          )
        ),

        array(
          'label' => 'Horaire',
          'type' => 'textarea',
          'key' => 'horaire',
          // 'placeholder' => array('join', array('map', array('map', array('query', 'shows', array('spectacle_id' => array('getValue', 'id'))), array('queryValue', 'shows', array('getItem'), 'date')), array('replace', '% : %', '%', array('date', array('getItem'), array('weekday' => 'short', 'day' => '2-digit')), array('date', array('getItem'), array('hour' => '2-digit', 'minute' => '2-digit')))), "\n")
          'placeholder' => array(
            'join',
            array(
              'map',
              array('query', 'shows', array('spectacle_id' => array('getValue', 'id'))),
              array(
                'replace',
                '% : %',
                '%',
                array('date', array('queryValue', 'shows', array('getItem'), 'date'), array('weekday' => 'short', 'day' => '2-digit')),
                array('replace', array('queryValue', 'shows', array('getItem'), 'hour'), ':', 'h')
              )
            ),
            "\n"
          )
        ),
        array(
          'label' => 'Date à afficher',
          'type' => 'textarea',
          'key' => 'text_date'
          // 'placeholder' => array('replace', )
        ),
        array(
          'label' => 'Crédits',
          'type' => 'array',
          'sortable' => true,
          'key' => 'credits',
          'children' => array(
            array(
              'label' => 'Nom(s)',
              'type' => 'input',
              'key' => 'name',
              'width' => '1fr'
            ),
            array(
              'label' => 'Fonction',
              'type' => 'input',
              'key' => 'fonction',
              'width' => '1fr'
            ),
            array(
              'type' => 'text',
              'width' => '10em'
            )

          ),
          'footer' => array(
            'type' => 'footer',
            'children' => array(
              array(
                'type' => 'add',
                'text' => 'Ajouter un crédit'
              ),
              array(
                'type' => 'delete',
                'text' => 'Supprimer'
              )
            )
          )
        ),
        array(
          'label' => 'Soutien',
          'type' => 'tinymce',
          'key' => 'soutien',
          'header' => array(
            'children' => array(
              'bold',
              'italic',
              'link'
            )
          )
        ),
        array(
          'label' => 'Avertissement',
          'type' => 'tinymce',
          'key' => 'avertissement',
          'header' => array(
            'children' => array(
              'bold',
              'italic',
              'link'
            )
          )
        ),


        array(
          'label' => 'Médias',
          'type' => 'textarea',
          'key' => 'medias'
          // 'placeholder' => array('replace', )
        ),

        array(
          'label' => 'Presse',
          'type' => 'array',
          'sortable' => true,
          'key' => 'presses',
          'children' => array(
            array(
              'label' => 'Média',
              'type' => 'input',
              'key' => 'name',
              'width' => '1fr'
            ),
            array(
              'label' => 'Titre',
              'type' => 'input',
              'key' => 'title',
              'width' => '1fr'
            ),
            array(
              'label' => 'Lien',
              'type' => 'input',
              'key' => 'url',
              'width' => '1fr',
              'placeholder' => 'https://'
            ),
            array(
              'type' => 'text',
              'width' => '10em'
            )

          ),
          'footer' => array(
            'type' => 'footer',
            'children' => array(
              array(
                'type' => 'add',
                'text' => 'Ajouter une pièce'
              ),
              array(
                'type' => 'delete',
                'text' => 'Supprimer'
              )
            )
          )
        ),




      )
    ));


  }


  public function prepare_spectacles($queried_spectacles) {
    global $wpdb;

    $output = array();

    $attachment_ids = array();
    $spectacle_ids = array();

    foreach ($queried_spectacles as $spectacle) {

      $spectacle_ids[] = $spectacle->ID;

      $vignette = get_post_meta($spectacle->ID, 'image', true);
      // $vignette = get_post_meta($spectacle->ID, '_thumbnail_id', true);

      if (!$vignette) {

        $vignette = get_post_meta($spectacle->ID, 'images', true);

      }

      if ($vignette) {

        $attachment_ids[] = $vignette;

      }


      $overlay = get_post_meta($spectacle->ID, 'overlay', true);

      if ($overlay) {

        $attachment_ids[] = $overlay;

      }

    }

    require_once get_stylesheet_directory() . '/class-image.php';

    if ($attachment_ids) {

      $attachment_ids = array_unique($attachment_ids);

      Karma_Image::cache_images($attachment_ids);

    }

    $first_dates = array();
    $last_dates = array();

    if ($spectacle_ids) {

      $ids = implode(',', array_map('intval', $spectacle_ids));

      $results = $wpdb->get_results(
        "SELECT spectacle_id, MIN(`date`) AS 'start', MAX(`date`) AS 'end'
        FROM {$wpdb->prefix}shows
        WHERE spectacle_id IN ($ids)
        GROUP BY spectacle_id");

      foreach ($results as $result) {

        $first_dates[$result->spectacle_id] = $result->start;
        $last_dates[$result->spectacle_id] = $result->end;

      }

      $mediations = array();

      $mediation_query = new WP_Query(array(
        'post_type' => 'mediation',
        'post_status' => 'publish',
        'meta_query' => array(
          array(
            'key' => 'parent_spectacle',
            'value' => $spectacle_ids,
            'compare' => 'IN'
          )
        )
      ));


      $mediation_page = home_url('place-publics');

      foreach ($mediation_query->posts as $mediation) {

        $parent_spectacle_ids = get_post_meta($mediation->ID, 'parent_spectacle');

        foreach ($parent_spectacle_ids as $parent_spectacle_id) {

          $mediations[$parent_spectacle_id][] = array(
            'url' => "$mediation_page#mediation-{$mediation->post_name}",
            'name' => get_the_title($mediation)
          );

        }

      }

    }

    foreach ($queried_spectacles as $spectacle) {

      $attachment_id = get_post_meta($spectacle->ID, 'image', true);
      // $attachment_id = get_post_meta($spectacle->ID, '_thumbnail_id', true);

      if (!$attachment_id) {

        $attachment_id = get_post_meta($spectacle->ID, 'images', true);

      }

      if ($attachment_id) {

        $image = Karma_Image::get_image_source($attachment_id);

      } else {

        $image = null;

      }

      $text_date = get_post_meta($spectacle->ID, 'text_date', true);

      if (!$text_date && isset($first_dates[$spectacle->ID], $first_dates[$spectacle->ID])) {

        $first_date = $first_dates[$spectacle->ID];
        $last_date = $last_dates[$spectacle->ID];

        if ($first_date === $last_date) {

          $text_date = date_i18n('d F Y', strtotime($first_date));

        } else {

          $text_date = $this->format_date_range(strtotime($first_date), strtotime($last_date));

        }

      }

      $overlay_id = get_post_meta($spectacle->ID, 'overlay', true);

      if ($overlay_id) {

        $overlay_url = wp_get_attachment_url($overlay_id);

      } else {

        $overlay_url = null;

      }

      $output[] = array(
        'id' => $spectacle->ID,
        'title' => $spectacle->post_title,
        'permalink' => get_permalink($spectacle),
        'image' => $image,
        'date' => $text_date,
        'overlay' => $overlay_url,

        'ticket' => '#',
        'mediation' => '#',

        'mediations' => isset($mediations[$spectacle->ID]) ? $mediations[$spectacle->ID] : null,

        'subtitle' => get_post_meta($spectacle->ID, 'subtitle', true),
        'description' => get_post_meta($spectacle->ID, 'description', true),
        'start' => isset($first_dates[$spectacle->ID]) ? $first_dates[$spectacle->ID] : null,
        'end' => isset($last_dates[$spectacle->ID]) ? $last_dates[$spectacle->ID] : null
      );

      // $items[] = array(
      //   'id' => $spectacle_id,
      //   'title' => get_the_title($spectacle_id),
      //   'ticket' => '#',
      //   'mediation' => '#',
      //   'permalink' => get_permalink($spectacle_id),
      //   'subtitle' => get_post_meta($spectacle_id, 'subtitle', true),
      //   'description' => get_post_meta($spectacle_id, 'description', true),
      //   'image' => isset($images[$spectacle_id]) ? $images[$spectacle_id] : null,
      //   'date' => $text_date,
      //   'start' => $dates['start'],
      //   'end' => $dates['end']
      // );

    }


    return $output;
  }

  /**
   * @hook tsg_home_slideshow
   */
  public function print_home_slideshow() {
    global $wpdb;

    $options = get_option('tsg', array());

    if (isset($options['slideshow'])) {

      $spectacle_ids = array();

      foreach ($options['slideshow'] as $item) {

        $spectacle_ids[] = $item['spectacle_id'];

      }

      $slides_query = new WP_query(array(
        'post_type' => 'spectacle',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__in' => $spectacle_ids,
        'orderby' => 'post__in',
        'order' => 'ASC'
      ));

      if ($slides_query->posts) {

        $slides = $this->prepare_spectacles($slides_query->posts);

        // $attachment_ids = array();
        // $spectacle_ids = array();
        //
        // foreach ($slides_query->posts as $spectacle) {
        //
        //   $spectacle_ids[] = $spectacle->ID;
        //
        //   $vignette = get_post_meta($spectacle->ID, 'image', true);
        //
        //   if ($vignette) {
        //
        //     $attachment_ids[] = $vignette;
        //
        //   }
        //
        //   $overlay = get_post_meta($spectacle->ID, 'overlay', true);
        //
        //   if ($overlay) {
        //
        //     $attachment_ids[] = $overlay;
        //
        //   }
        //
        //
        //
        // }
        //
        // require_once get_stylesheet_directory() . '/class-image.php';
        //
        // if ($attachment_ids) {
        //
        //   Karma_Image::cache_images($attachment_ids);
        //
        // }
        //
        // $first_dates = array();
        // $last_dates = array();
        //
        // if ($spectacle_ids) {
        //
        //   $ids = implode(',', array_map('intval', $spectacle_ids));
        //
        //   $results = $wpdb->get_results(
        //     "SELECT spectacle_id, MIN(`date`) AS 'start', MAX(`date`) AS 'end'
        //     FROM {$wpdb->prefix}shows
        //     WHERE spectacle_id IN ($ids)
        //     GROUP BY spectacle_id");
        //
        //   // echo '<pre>'; var_dump($results); die();
        //
        //   foreach ($results as $result) {
        //
        //     $first_dates[$result->spectacle_id] = $result->start;
        //     $last_dates[$result->spectacle_id] = $result->end;
        //
        //   }
        //
        // }
        //
        // $slides = array();
        //
        // foreach ($slides_query->posts as $spectacle) {
        //
        //   $attachment_id = get_post_meta($spectacle->ID, 'image', true);
        //
        //   if ($attachment_id) {
        //
        //     $image = Karma_Image::get_image_source($attachment_id);
        //
        //   } else {
        //
        //     $image = null;
        //
        //   }
        //
        //   $text_date = get_post_meta($spectacle->ID, 'text_date', true);
        //
        //   if (!$text_date && isset($first_dates[$spectacle->ID], $first_dates[$spectacle->ID])) {
        //
        //     $first_date = $first_dates[$spectacle->ID];
        //     $last_date = $last_dates[$spectacle->ID];
        //
        //     if ($first_date === $last_date) {
        //
        //       $text_date = date_i18n('d F Y', strtotime($first_date));
        //
        //     } else {
        //
        //       $text_date = $this->format_date_range(strtotime($first_date), strtotime($last_date));
        //
        //     }
        //
        //   }
        //
        //   $overlay_id = get_post_meta($spectacle->ID, 'overlay', true);
        //
        //   if ($overlay_id) {
        //
        //     $overlay_url = wp_get_attachment_url($overlay_id);
        //
        //   } else {
        //
        //     $overlay_url = null;
        //
        //   }
        //
        //   $slides[] = array(
        //     'id' => $spectacle->ID,
        //     'title' => $spectacle->post_title,
        //     'permalink' => get_permalink($spectacle),
        //     'image' => $image,
        //     'date' => $text_date,
        //     'overlay' => $overlay_url
        //   );
        //
        // }

        // echo '<pre>'; var_dump($first_dates, $last_dates, $slides); die();

        include get_stylesheet_directory().'/include/home-slideshow.php';

      }

    }

  }

  // archéolab pully

	/**
	 * get date range from sql dates
	 */
	public function format_date_range($t1, $t2) {

		$d1 = date('d', $t1);
		$m1 = date_i18n('F', $t1);
		$y1 = date('Y', $t1);
		$d2 = date('d', $t2);
		$m2 = date_i18n('F', $t2);
		$y2 = date('Y', $t2);

		if ($y1 === $y2) {

			if ($m1 === $m2) {

				if ($d1 === $d2) {

					return date_i18n('d F Y', $t2);

				} else {

					return date('d', $t1) . ' — ' . date_i18n('d F Y', $t2);

				}

			} else {

				return date_i18n('d F', $t1) . ' — ' . date_i18n('d F Y', $t2);

			}

		} else {

			return date_i18n('d F Y', $t1) . ' — ' . date_i18n('d F Y', $t2);

		}

	}


  /**
   *
   */
  public function get_current_saison_ids() {

    return TSG_Saisons::get_current_saison_ids();

    // $now = date('Y-m-d');
    //
    // if (is_user_logged_in()) {
    //
    //   return get_terms(array(
    //     'taxonomy' => 'infomaniak',
    //     'hide_empty' => false,
    //     'meta_query' => array(
    //       array(
    //         'key' => 'end',
    //         'value' => $now,
    //         'compare' => '>'
    //       ),
    //       array(
    //         'key' => 'status',
    //         'value' => array('publish', 'private'),
    //         'compare' => 'IN'
    //       )
    //     ),
    //     'fields' => 'ids'
    //   ));
    //
    // } else {
    //
    //   return get_terms(array(
    //     'taxonomy' => 'infomaniak',
    //     'hide_empty' => false,
    //     'meta_query' => array(
    //       array(
    //         'key' => 'end',
    //         'value' => $now,
    //         'compare' => '>'
    //       ),
    //       array(
    //         'key' => 'status',
    //         'value' => 'publish',
    //         'compare' => '='
    //       )
    //     ),
    //     'fields' => 'ids'
    //   ));
    //
    // }

  }

  /**
   * @rest 'wp-json/actwall/v1/saisons'
   */
  public function rest_saisons($request) {
    global $wpdb;

    $term_ids = get_terms(array(
      'taxonomy' => 'infomaniak',
      'hide_empty' => false,
      'orderby' => 'meta_value',
      'order' => 'DESC',
      'meta_key' => 'end',
      'fields' => 'ids'
    ));

    $saisons = array();

    foreach ($term_ids as $term_id) {

      $saisons[] = array(
        'id' => $term_id,
        'start' => get_term_meta($term_id, 'start', true),
        'end' => get_term_meta($term_id, 'end', true)
      );

    }

    return $saisons;

  }


  /**
   * @rest 'wp-json/actwall/v1/calendar-shows'
   */
  public function rest_calendar_shows($request) {
    global $wpdb;

    $saison_id = $request->get_param('saison_id');

    return $this->get_calendar_shows(array($saison_id));

  }

  /**
   * @hook tsg_home_agenda
   */
  public function print_home_agenda() {

    $current_saison_ids = $this->get_current_saison_ids();

    $calendar = $this->get_calendar_shows($current_saison_ids);

    $year = date('Y');
    $month = date('m');
    $times = $this->get_month_days($year, $month);

    include get_stylesheet_directory().'/include/home-agenda.php';

  }

  /**
   * get_calendar_shows
   */
  public function get_calendar_shows($saison_ids) {

    $show_items = $this->get_shows($saison_ids);

    $calendar = array();

    foreach ($show_items as $item) {

      $date = $item['date'];

      $calendar[$date][] = $item;

    }

    return $calendar;

  }

  /**
   *
   */
  public function get_shows($saison_ids) {
    global $wpdb;

    $items = array();

    $spectacles_query = new WP_query(array(
      'post_type' => 'spectacle',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'infomaniak',
          'field' => 'term_id',
          'terms' => $saison_ids,
        )
      )
    ));

    if ($spectacles_query->posts) {

      $attachment_ids = array();
      $spectacle_ids = array();

      foreach ($spectacles_query->posts as $spectacle) {

        $spectacle_ids[] = (int) $spectacle->ID;

        $image_id = get_post_meta($spectacle->ID, 'image', true);
        // $image_id = get_post_meta($spectacle->ID, '_thumbnail_id', true);

        if (!$image_id) {

          $image_id = get_post_meta($spectacle->ID, 'images', true);

        }

        if ($image_id) {

          $attachment_ids[$spectacle->ID] = $image_id;

        }

      }

      require_once get_stylesheet_directory() . '/class-image.php';

      if ($attachment_ids) {

        Karma_Image::cache_images(array_values($attachment_ids));

      }

      $images = array();

      foreach ($attachment_ids as $spectacle_id => $attachment_id) {

        if ($attachment_id) {

          $images[$spectacle_id] = Karma_Image::get_image_source($attachment_id);

        }

      }

      $ids = implode(',', $spectacle_ids);

      $shows = $wpdb->get_results(
        "SELECT spectacle_id, `date`
        FROM {$wpdb->prefix}shows
        WHERE spectacle_id IN ($ids) AND trash = 0"
      );

      foreach ($shows as $show) {

        $items[] = array(
          'id' => $show->spectacle_id,
          'title' => get_the_title($show->spectacle_id),
          'permalink' => get_permalink($show->spectacle_id),
          'image' => isset($images[$show->spectacle_id]) ? $images[$show->spectacle_id] : null,
          // 'date_sql' => $show->date,
          'date' => substr($show->date, 0, 10)
          // 'date_day' => substr($show->date, 8, 2)
        );

      }

    }

    return $items;

  }


  /**
   * @rest 'wp-json/actwall/v1/future-spectacles'
   */
  public function rest_future_spectacles($request) {
    global $wpdb;

    $current_saison_ids = $this->get_current_saison_ids();

    $spectacles = $this->query_spectacles($current_saison_ids);

    $now = date('Y-m-d');

    $spectacles = array_filter($spectacles, function($spectacle) use($now) {
      return $spectacle['end'] >= $now;
    });

    $spectacles = array_reverse($spectacles);

    return $spectacles;

  }

  /**
   * @hook tsg_future_spectacles'
   */
  public function print_future_spectacles() {

    $current_saison_ids = $this->get_current_saison_ids();

    $spectacles = $this->query_spectacles($current_saison_ids);

    $now = date('Y-m-d');

    $spectacles = array_filter($spectacles, function($spectacle) use($now) {
      return $spectacle['end'] >= $now;
    });

    $spectacles = array_reverse($spectacles);

    include get_stylesheet_directory().'/include/agenda-future-spectacle.php';

  }

  /**
   * @rest 'wp-json/actwall/v1/archives-saison/{saison_id}'
   */
  public function rest_archives_saison($request) {
    global $wpdb;

    $saison_id = $request->get_param('saison_id');

    $spectacles = $this->query_spectacles(array($saison_id));

    return $spectacles;

  }


  /**
   *
   */
  public function query_spectacles($saison_ids) {
    global $wpdb;

    $items = array();

    $spectacles_query = new WP_query(array(
      'post_type' => 'spectacle',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'infomaniak',
          'field' => 'term_id',
          'terms' => $saison_ids,
        )
      )
    ));



    if ($spectacles_query->posts) {

      $items = $this->prepare_spectacles($spectacles_query->posts);


      // $spectacle_ids = array();
      // $attachment_ids = array();
      //
      // foreach ($spectacles_query->posts as $spectacle) {
      //
      //
      //
      //   $spectacle_id = (int) $spectacle->ID;
      //   $spectacle_ids[] = $spectacle_id;
      //   $attachment_ids[$spectacle_id] = get_post_meta($spectacle_id, 'image', true);
      //
      // }
      //
      //
      //
      // require_once get_stylesheet_directory() . '/class-image.php';
      //
      // if ($attachment_ids) {
      //
      //   Karma_Image::cache_images(array_values($attachment_ids));
      //
      // }
      //
      // $images = array();
      //
      // foreach ($attachment_ids as $spectacle_id => $attachment_id) {
      //
      //   if ($attachment_id) {
      //
      //     $images[$spectacle_id] = Karma_Image::get_image_source($attachment_id);
      //
      //   }
      //
      // }
      //
      // $ids = implode(',', array_map('intval', $spectacle_ids));
      //
      // $show_results = $wpdb->get_results(
      //   "SELECT spectacle_id, MIN(`date`) AS 'start', MAX(`date`) AS 'end'
      //   FROM {$wpdb->prefix}shows
      //   WHERE trash = 0 AND spectacle_id IN ($ids)
      //   GROUP BY spectacle_id
      //   ORDER BY MAX(`date`) DESC"
      // );
      //
      // $shows = array();
      //
      // foreach ($show_results as $show_result) {
      //
      //   $shows[$show_result->spectacle_id]['start'] = $show_result->start;
      //   $shows[$show_result->spectacle_id]['end'] = $show_result->end;
      //
      // }
      //
      // foreach ($shows as $spectacle_id => $dates) {
      //
      //   $text_date = get_post_meta($spectacle_id, 'text_date', true);
      //
      //   if (!$text_date) {
      //
      //     if ($dates['start'] === $dates['end']) {
      //
      //       $text_date = date_i18n('d F Y', strtotime($dates['start']));
      //
      //     } else {
      //
      //       $text_date = $this->format_date_range(strtotime($dates['start']), strtotime($dates['end']));
      //
      //     }
      //
      //   }
      //
      //   $items[] = array(
      //     'id' => $spectacle_id,
      //     'title' => get_the_title($spectacle_id),
      //     'ticket' => '#',
      //     'mediation' => '#',
      //     'permalink' => get_permalink($spectacle_id),
      //     'subtitle' => get_post_meta($spectacle_id, 'subtitle', true),
      //     'description' => get_post_meta($spectacle_id, 'description', true),
      //     'image' => isset($images[$spectacle_id]) ? $images[$spectacle_id] : null,
      //     'date' => $text_date,
      //     'start' => $dates['start'],
      //     'end' => $dates['end']
      //   );
      //
      // }

    }

    return $items;

  }

  public function get_month_days($year, $month) {

    $times = array();
    $month_first_day = strtotime("$year-$month-01");
    $last_day_prev_month = strtotime('-1 day', $month_first_day);
    $first_day_next_month = strtotime('+1 month', $month_first_day);
    $last_day_prev_month_weekday = date('w', $last_day_prev_month);
    $cursor_day = strtotime("-$last_day_prev_month_weekday day", $month_first_day);
    $today = strtotime(date('Y-m-d'));

    while ($cursor_day < $first_day_next_month || date('w', $cursor_day) !== '1') {
      $times[] = array(
        'time' => $cursor_day,
        'date' => date('Y-m-d', $cursor_day),
        'is_day_before' => $cursor_day === $last_day_prev_month,
        'is_day_after' => $cursor_day === $first_day_next_month,
        'is_first_day' => $cursor_day === $month_first_day,
        'is_offset' => $cursor_day <= $last_day_prev_month || $cursor_day >= $first_day_next_month,
        'is_today' => $cursor_day === $today
      );

      $cursor_day = strtotime('+1 day', $cursor_day);
    }

    return $times;
  }



  /**
   * @hook 'tsg_single_spectacle'
   */
  public function print_single_spectacle() {
    global $post, $wpdb;

    // echo '<pre>';
    // var_dump(get_post_meta($post->ID)); die();


    $image_ids = get_post_meta($post->ID, 'images');



    require_once get_stylesheet_directory() . '/class-image.php';

    if ($image_ids) {

      Karma_Image::cache_images($image_ids);

    }

    $spectacle_id = (int) $post->ID;

    $show_results = $wpdb->get_results(
      "SELECT * FROM {$wpdb->prefix}shows
      WHERE trash = 0 AND spectacle_id = $spectacle_id
      ORDER BY `date` ASC"
    );

    $shows = array();

    foreach ($show_results as $show_result) {

      $timestamp = strtotime($show_result->date);

      if ($show_result->nohour) {

        $hour = '';

      } else {

        $hour = date('H \h i', $timestamp);

      }

      $shows[] = array(
        'weekday' => date_i18n('D', $timestamp),
        'date' => date_i18n('d M', $timestamp),
        'hour' => $hour
      );

    }


    $post_query = new WP_query(array(
      'post_type' => 'post',
      'post_status' => 'publish',
      'posts_per_page' => 1,
      'meta_query' => array(
        array(
          'key' => 'spectacle_id',
          'value' => $spectacle_id
        )
      )
    ));

    foreach ($post_query->posts as $post) {

      $blog_url = get_permalink($post);

      break;

    }

    $mediation_query = new WP_Query(array(
      'post_type' => 'mediation',
      'post_status' => 'publish',
      'meta_query' => array(
        array(
          'key' => 'parent_spectacle',
          'value' => $spectacle_id
        )
      )
    ));

    foreach ($mediation_query->posts as $mediation) {

      // $mediation_url = get_permalink($mediation);
      $mediation_page = home_url('place-publics');
      $mediation_url = "$mediation_page#mediation-{$mediation->post_name}";
      $mediation_name = get_the_title($mediation);

      break;

    }



    include get_stylesheet_directory().'/include/single-spectacle.php';
    // include get_stylesheet_directory().'/include/spectacle-slideshow.php';

  }


}


new TSG_Spectacles;
