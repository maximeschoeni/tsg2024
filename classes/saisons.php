<?php




class TSG_Saisons {

  /**
   * get_current_saison_ids
   */
  public static function get_current_saison_ids() {

    $now = date('Y-m-d');

    if (is_user_logged_in()) {

      return get_terms(array(
        'taxonomy' => 'infomaniak',
        'hide_empty' => false,
        'meta_query' => array(
          array(
            'key' => 'end',
            'value' => $now,
            'compare' => '>'
          ),
          array(
            'key' => 'status',
            'value' => array('publish', 'private'),
            'compare' => 'IN'
          )
        ),
        'fields' => 'ids'
      ));

    } else {

      return get_terms(array(
        'taxonomy' => 'infomaniak',
        'hide_empty' => false,
        'meta_query' => array(
          array(
            'key' => 'end',
            'value' => $now,
            'compare' => '>'
          ),
          array(
            'key' => 'status',
            'value' => 'publish',
            'compare' => '='
          )
        ),
        'fields' => 'ids'
      ));

    }

  }

  /**
   * constructor
   */
  public function __construct() {


    // add_action('actwall_bio_body', array($this, 'print_body'));

    add_action('init', array($this, 'init'));
    // add_action('add_meta_boxes', array($this, 'meta_boxes'), 10, 2);
    // add_action('rest_api_init', array($this, 'rest_api_init'));
    // add_action('karma_fields_init', array($this, 'karma_fields_init'));

    // add_action( 'restrict_manage_posts', array($this, 'taxonomy_filter'));


  }



  /**
	 * @hook init
	 */
	public function init() {

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

    add_action( 'infomaniak_edit_form_fields', array($this, 'add_meta_field') );

  }

  /**
   *	@hook {term}_edit_form_fields
   */
	public function add_meta_field( $term ) {

    do_action('karma_fields_term_field', $term->term_id, 'Details', array(
      'children' => array(
        array(
          'label' => 'Start',
          'type' => 'date',
          'key' => 'start'
        ),
        array(
          'label' => 'End',
          'type' => 'date',
          'key' => 'end'
        ),
        array(
          'label' => 'Statut',
          'type' => 'dropdown',
          'key' => 'status',
          'options' => array(
            array('id' => '', 'name' => 'Brouillon'),
            array('id' => 'publish', 'name' => 'Publié'),
            array('id' => 'private', 'name' => 'Privé'),
          )
        )
      )
    ));


  }




}


new TSG_Saisons;
