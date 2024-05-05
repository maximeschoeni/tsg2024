<?php




class TSG_Shows {

  /**
   * constructor
   */
  public function __construct() {

    if (is_admin()) {

			// add_action('admin_menu', array($this, 'admin_menu'));
			add_action('init', array($this, 'create_table'));


		}

    // add_action('rest_api_init', array($this, 'rest_api_init'));
    add_action('karma_fields_init', array($this, 'karma_fields_init'));


    add_action('laribot_agenda', array($this, 'print_agenda'));
    add_action('laribot_home_calendar', array($this, 'print_home_calendar'));
    add_action('laribot_work_calendar', array($this, 'print_work_calendar'));
    add_action('laribot_calendar_section', array($this, 'print_calendar_section'));

    add_action('laribot_calendar', array($this, 'print_calendar'));





  }

  /**
	 *	create formats table
	 */
   public function create_table(){
 		global $wpdb;

 		$table_version = 9;

    $options = get_option('tsg', array());

 		if (empty($options['shows_table_version']) || $options['shows_table_version'] !== $table_version) {

      $options['shows_table_version'] = $table_version;

      update_option('karma', $options);

 			// $table = $wpdb->prefix.$this->table_name;

 			$charset_collate = '';

 			if (!empty($wpdb->charset)){
 				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
 			}

 			if (!empty($wpdb->collate)){
 				$charset_collate .= " COLLATE $wpdb->collate";
 			}

 			$mysql = "CREATE TABLE {$wpdb->prefix}shows (
 				id INT(20) NOT NULL AUTO_INCREMENT,
 				spectacle_id INT(20) NOT NULL,
 				date DATETIME NOT NULL,
 				place INT(20) NOT NULL,
 				status TINYINT(1) NOT NULL,
 				nohour TINYINT(1) NOT NULL,
 				trash TINYINT(1) NOT NULL,
 				infomaniak TINYINT(1) NOT NULL,
        KEY spectacle_id (spectacle_id),
        KEY date (date),
        KEY trash (trash),
 				PRIMARY KEY  (id)
 			) $charset_collate;";

 			// status: 0 = free, 1 = presque complet, 2 = complet, 3 = pas de réservation, 4 = seulement achat, 5 pas d'achat
 			// nohour: 0 = normal, 1 = hour not set

 			// @from aout2019
 			// status: 0: auto, 1: presque complet, 2: complet, 3: pas de réservation
 			// infomaniak: 0: auto, 1: presque complet, 2: complet, 3: pas d'achat


 			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 			dbDelta($mysql);



 		}

 	}

  /**
	 *	Create admin menu
	 */
	public function admin_menu() {

    // add_submenu_page(
    //   'edit.php?post_type=work',
    //   'Evenements',
		// 	'Evenements',
    //   'read',
		// 	'evenements',
		// 	array($this, 'print_evenements')
    // );

	}

  public function print_evenements() {

  }

  /**
   * @hook karma_fields_init
   */
  public function karma_fields_init($karma_fields) {

    $karma_fields->register_driver(
			'shows',
			get_template_directory().'/drivers/driver-shows.php',
			'TSG_Driver_Shows',
      array(),
      array()
		);

    // $karma_fields->register_driver(
		// 	'evenement-dates',
		// 	get_template_directory().'/drivers/driver-evenement-dates.php',
		// 	'Laribot_Driver_Evenement_Dates',
    //   array(),
    //   array()
		// );

    // $karma_fields->register_menu_item('evenements', '?post_type=work&page=evenements');
    // $karma_fields->register_table('evenements', array(
    //   'header' => array(
		// 		'type' => 'group',
    //     'children' => array(
    //       array(
    //         'type' => 'header',
    //         'title' => 'Représentations'
    //       ),
    //       array(
    //         'type' => 'header',
    //         'children' => array(
    //           array(
    //             'label' => 'Date',
    //             'type' => 'dropdown',
    //             'key' => 'date',
    //             'driver' => 'evenement-dates',
    //             // 'params' => array(),
    //             'options' => array(array('id' => '', 'name' => '-')),
    //             'width' => '20em'
    //           ),
    //           array(
    //             'label' => 'Work',
    //             'type' => 'dropdown',
    //             'key' => 'work_id',
    //             'driver' => 'posts',
    //             'params' => array(
    //               'post_type' => 'work',
    //               'post_status' => 'publish',
    //               'orderby' => 'post_title',
    //               'order' => 'asc'
    //               // 'date' => array('getParam', 'date')
    //             ),
    //             'options' => array(array('id' => '', 'name' => '-')),
    //             'width' => '20em'
    //           )
    //
    //         )
    //       )
    //     )
		// 	),
    //   'body' => array(
		// 		'type' => 'grid',
		// 		'driver' => 'evenements',
    //     'params' => array(
    //       'orderby' => 'start_date',
    //       'order' => 'desc',
		// 			'ppp' => 100
		// 		),
    //     'children' => array(
    //       // array(
    //       //   'type' => 'index',
    //       //   'width' => '3em'
    //       // ),
    //       array(
    //         'label' => 'Date de début',
    //         'type' => 'text',
    //         'content' => array('date', array('getValue', 'start_date')),
    //         'width' => 'auto'
    //       ),
    //       array(
    //         'label' => 'Date de fin',
    //         'type' => 'text',
    //         'content' => array('date', array('getValue', 'end_date')),
    //         'width' => 'auto'
    //       ),
    //       array(
    //         'label' => 'Nom du lieu',
    //         'type' => 'text',
    //         'content' => array('getValue', 'name'),
    //         'width' => 'auto'
    //       ),
    //       array(
    //         'label' => 'Ville, pays',
    //         'type' => 'text',
    //         'content' => array('replace', '% %', '%', array('getValue', 'ville'), array('getValue', 'pays')),
    //         'width' => 'auto'
    //       ),
    //       array(
    //         'label' => 'Work',
    //         'type' => 'text',
    //         'content' => array('||', array('getValue', 'title'), array('queryValue', 'posts', array('getValue', 'work_id'), 'post_title')),
    //         'width' => '1fr'
    //       )
    //     ),
    //     'modal' => array(
    //       'width' => '40em',
    //       'children' => array(
    //         array(
    //           'label' => 'Work',
    //           'type' => 'dropdown',
    //           'key' => 'work_id',
    //           'driver' => 'posts',
    //           'params' => array(
    //             'post_type' => 'work',
    //             'post_status' => 'publish',
    //             'orderby' => 'post_title',
    //             'order' => 'asc'
    //             // 'date' => array('getParam', 'date')
    //           ),
    //           'options' => array(array('id' => '', 'name' => '-')),
    //           'default' => array('getParam', 'work_id')
    //           // 'width' => '20em'
    //         ),
    //         array(
    //           'label' => 'Date de début',
    //           'type' => 'date',
    //           'key' => 'start_date'
    //         ),
    //         array(
    //           'label' => 'Date de fin',
    //           'type' => 'date',
    //           'key' => 'end_date'
    //         ),
    //         array(
    //           'label' => 'Titre de l’événement',
    //           'type' => 'input',
    //           'key' => 'title',
    //           'placeholder' => array('queryValue', 'posts', array('getValue', 'work_id'), 'post_title')
    //         ),
    //         array(
    //           'label' => 'Contexte',
    //           'type' => 'input',
    //           'key' => 'context'
    //         ),
    //         array(
    //           'label' => 'Nom du lieu',
    //           'type' => 'input',
    //           'key' => 'name',
    //           'width' => '1fr'
    //         ),
    //
    //         array(
    //           'label' => 'Ville',
    //           'type' => 'input',
    //           'key' => 'ville'
    //         ),
    //         array(
    //           'label' => 'Pays',
    //           'type' => 'input',
    //           'key' => 'pays'
    //         ),
    //         array(
    //           'label' => 'Site web',
    //           'type' => 'input',
    //           'key' => 'web'
    //         )
    //       )
    //     )
    //   )
    //   // 'filters' => array(
    //   //   'children' => array(
    //   //     array(
    //   //       'label' => 'Search',
    //   //       'key' => 'search',
    //   //       'type' => 'input',
    //   //       'width' => '30em'
    //   //     )
    //   //   )
    //   // )
    // ));






  }

  // /**
  //  * @hook 'laribot_agenda'
  //  */
  // public function print_agenda() {
  //   global $wpdb;
  //
  //   $now = date('Y-m-d');
  //   $now = '0000-00-00';
  //
  //   $evenements = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}evenements WHERE start_date > '$now' AND trash = 0", ARRAY_A);
  //
  //   // var_dump($evenements);
  //
  //   $work_ids = array();
  //
  //   foreach ($evenements as $evenement) {
  //
  //     $work_ids[] = $evenement['work_id'];
  //
  //   }
  //
  //   if ($work_ids) {
  //
  //     $work_ids = array_map('intval', $work_ids);
  //     $in = implode(',', $work_ids);
  //
  //     $works = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID IN ($in)");
  //
  //     update_post_caches($works, 'any', false, true);
  //
  //   }
  //
  //   include get_stylesheet_directory().'/include/agenda.php';
  //
  //
  // }
  //
  // /**
  //  * @hook 'laribot_home_calendar'
  //  */
  // public function print_home_calendar() {
  //   global $wpdb;
  //
  //   $now = date('Y-m-d');
  //
  //
  //
  //   $results = $wpdb->get_results(
  //     "SELECT * FROM {$wpdb->prefix}evenements
  //     WHERE trash = 0 AND start_date >= '$now'
  //     ORDER BY start_date ASC",
  //     ARRAY_A
  //   );
  //
  //   $work_ids = array();
  //
  //   foreach ($results as $evenement) {
  //
  //     $work_ids[] = $evenement['work_id'];
  //
  //   }
  //
  //   if ($work_ids) {
  //
  //     $work_ids = array_map('intval', $work_ids);
  //     $in = implode(',', $work_ids);
  //
  //     $works = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID IN ($in)");
  //
  //     update_post_caches($works, 'any', false, true);
  //
  //   }
  //
  //   $calendar = array();
  //
  //   if ($results) {
  //
  //     foreach ($results as $evenement) {
  //
  //       $year = substr($evenement['start_date'], 0, 4);
  //
  //       $calendar[$year][] = array(
  //         'date' => $this->format_date($evenement['start_date'], $evenement['end_date']),
  //         'title' => $evenement['title'],
  //         'work' => get_the_title($evenement['work_id']),
  //         'permalink' => get_permalink($evenement['work_id']),
  //         'name' => $evenement['name'],
  //         'context' => $evenement['context'],
  //         'web' => $evenement['web'],
  //         'ville' => $evenement['ville'],
  //         'pays' => $evenement['pays']
  //       );
  //
  //     }
  //
  //   }
  //
  //   include get_stylesheet_directory().'/include/calendar.php';
  //
  // }
  //
  // /**
  //  * @hook 'laribot_calendar'
  //  */
  // public function print_calendar() {
  //   global $wpdb;
  //
  //   $results = $wpdb->get_results(
  //     "SELECT * FROM {$wpdb->prefix}evenements
  //     WHERE trash = 0
  //     ORDER BY start_date DESC",
  //     ARRAY_A
  //   );
  //
  //   $work_ids = array();
  //
  //   foreach ($results as $evenement) {
  //
  //     $work_ids[] = $evenement['work_id'];
  //
  //   }
  //
  //   if ($work_ids) {
  //
  //     $work_ids = array_map('intval', $work_ids);
  //     $in = implode(',', $work_ids);
  //
  //     $works = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID IN ($in)");
  //
  //     update_post_caches($works, 'any', false, true);
  //
  //   }
  //
  //   $calendar = array();
  //
  //   if ($results) {
  //
  //     foreach ($results as $evenement) {
  //
  //       $year = substr($evenement['start_date'], 0, 4);
  //
  //       $calendar[$year][] = array(
  //         'date' => $this->format_date($evenement['start_date'], $evenement['end_date']),
  //         'work' => get_the_title($evenement['work_id']),
  //         'permalink' => get_permalink($evenement['work_id']),
  //         'name' => $evenement['name'],
  //         'title' => $evenement['title'],
  //         'context' => $evenement['context'],
  //         'web' => $evenement['web'],
  //         'ville' => $evenement['ville'],
  //         'pays' => $evenement['pays']
  //       );
  //
  //     }
  //
  //   }
  //
  //   include get_stylesheet_directory().'/include/calendar.php';
  //
  // }
  //
  //
  // /**
  //  * DEPRECATED: use laribot_calendar_section
  //  * @hook 'laribot_work_calendar'
  //  */
  // public function print_work_calendar($work) {
  //   global $wpdb;
  //
  //   $work_id = intval($work->ID);
  //
  //   $results = $wpdb->get_results(
  //     "SELECT * FROM {$wpdb->prefix}evenements
  //     WHERE work_id = $work_id AND trash = 0
  //     ORDER BY start_date DESC",
  //     ARRAY_A
  //   );
  //
  //   $calendar = array();
  //
  //   if ($results) {
  //
  //     $title = get_the_title($work);
  //
  //     foreach ($results as $evenement) {
  //
  //       $year = substr($evenement['start_date'], 0, 4);
  //
  //       $calendar[$year][] = array(
  //         'date' => $this->format_date($evenement['start_date'], $evenement['end_date']),
  //         'work' => $title,
  //         'permalink' => '',
  //         'name' => $evenement['name'],
  //         'title' => $evenement['title'],
  //         'context' => $evenement['context'],
  //         'web' => $evenement['web'],
  //         'ville' => $evenement['ville'],
  //         'pays' => $evenement['pays']
  //       );
  //
  //     }
  //
  //   }
  //
  //   include get_stylesheet_directory().'/include/calendar.php';
  //
  // }
  //
  //
  //
  // /**
  //  * @hook 'laribot_calendar_section'
  //  */
  // public function print_calendar_section($work) {
  //   global $wpdb;
  //
  //   $work_id = intval($work->ID);
  //
  //   $results = $wpdb->get_results(
  //     "SELECT * FROM {$wpdb->prefix}evenements
  //     WHERE work_id = $work_id AND trash = 0
  //     ORDER BY start_date DESC",
  //     ARRAY_A
  //   );
  //
  //   $calendar = array();
  //
  //   if ($results) {
  //
  //     $title = get_the_title($work);
  //
  //     foreach ($results as $evenement) {
  //
  //       $year = substr($evenement['start_date'], 0, 4);
  //
  //       $calendar[$year][] = array(
  //         'date' => $this->format_date($evenement['start_date'], $evenement['end_date']),
  //         'work' => $title,
  //         'permalink' => '',
  //         'name' => $evenement['name'],
  //         'title' => $evenement['title'],
  //         'context' => $evenement['context'],
  //         'web' => $evenement['web'],
  //         'ville' => $evenement['ville'],
  //         'pays' => $evenement['pays']
  //       );
  //
  //     }
  //
  //   }
  //
  //   if ($calendar) {
  //
  //     include get_stylesheet_directory().'/include/calendar-section.php';
  //
  //   }
  //
  // }
  //
  //
  //
  // public function format_date($start, $end) {
  //
  //   if ($end) {
  //
  //     $time_start = strtotime($start);
  //     $time_end = strtotime($end);
  //
  //     $year_start = date('Y', $time_start);
  //     $year_end = date('Y', $time_end);
  //
  //     if ($year_start === $year_end) {
  //
  //       $month_start = date('m', $time_start);
  //       $month_end = date('m', $time_end);
  //
  //       // if ($month_start === $month_end) {
  //       //
  //       //   $date1 = date('d', $time_start);
  //       //   $date2 = date('d.m.Y', $time_end);
  //       //
  //       //   $date = "$date1 – $date2";
  //       //
  //       // } else {
  //
  //         $date1 = date('d.m', $time_start);
  //         $date2 = date('d.m.Y', $time_end);
  //
  //         $date = "$date1 – $date2";
  //
  //       // }
  //
  //     } else {
  //
  //       $date1 = date('d.m.Y', $time_start);
  //       $date2 = date('d.m.Y', $time_end);
  //
  //       $date = "$date1 – $date2";
  //
  //     }
  //
  //   } else {
  //
  //     $time = strtotime($start);
  //     $date = date('d.m.Y', $time);
  //
  //   }
  //
  //   return $date;
  // }


}


new TSG_Shows;
