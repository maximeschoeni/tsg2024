<?php



class TSG_Options {

  /**
   * constructor
   */
  public function __construct() {

    if (is_admin()) {

			add_action('admin_menu', array($this, 'admin_menu'));


		}


  }


  /**
	 *	Create admin menu
	 */
	public function admin_menu() {

    add_menu_page(
  		'Options',
  		'Options',
  		'edit_posts',
  		'tsg-options',
  		array($this, 'print_options'),
      'dashicons-admin-generic',
  		40
  	);

	}

  public function print_options() {

    echo '<h1>Options</h1>';

    do_action('karma_fields_option_field', 'tsg', array(

      'children' => array(
        array(
          'type' => 'group',
          'key' => 'option_value',
          'width' => 'auto',
          // 'align' => 'flex-start',
          'children' => array(

            array(
              'label' => 'Slideshow',
              'type' => 'array',
              'key' => 'slideshow',
              'sortable' => true,

              'children' => array(
                array(
                  'label' => 'Saison',
                  'type' => 'dropdown',
                  'key' => 'saison_id',
                  // 'options' => array(
                  //   array('id' => '', 'name' => '-'),
                  // ),
                  'driver' => 'taxonomy',
                  'params' => array('taxonomy' => 'infomaniak', 'orderby' => 'start', 'order' => 'desc')
                ),
                array(
                  'label' => 'spectacle',
                  'type' => 'dropdown',
                  'key' => 'spectacle_id',
                  'options' => array(
                    array('id' => '', 'name' => '-'),
                  ),
                  'driver' => 'posts',
                  'params' => array('post_type' => 'spectacle', 'post_status' => 'publish', 'infomaniak' => array('getValue', 'saison_id'))
                )
              )
            ),


            array(
              'type' => 'submit'
            )

          )
        )
      )
    ));


  }



}


new TSG_Options;
