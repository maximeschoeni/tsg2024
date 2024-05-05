<?php


Class TSG_Driver_Shows {

  /**
	 * update
	 */
  public function update($item, $id) {
    global $wpdb;

    if (!is_user_logged_in()) {
      return;
    }

    $table = "{$wpdb->prefix}shows";

    $values = array();
    $value_types = array();

    foreach ($item as $key => $value) {

      switch ($key) {

        case 'date':
          $date = isset($value[0]) && preg_match('/(\d{4}).(\d{2}).(\d{2})/', $value[0]);
          $date = preg_replace('/(\d{4}).(\d{2}).(\d{2})/', '$1-$2-$3', $hour);

          $hour = isset($item['hour'][0]) && preg_match('/(\d{2}).(\d{2})/', $item['hour'][0]) ? $item['hour'][0] : '00:00';
          $hour = preg_replace('/(\d{2}).(\d{2})/', '$1:$2', $hour);

          $values['nohour'] = $hour === '00:00' ? 1 : 0;
          $value_types[] = '%d';

          $values['date'] = substr($date, 0, 10) . ' ' . $hour . ':00';
          $value_types[] = '%s';
          break;

        case 'place':
        case 'spectacle_id':
        case 'status':
        case 'infomaniak':
        // case 'nohour':
        case 'trash':
          $values[$key] = intval($value[0]);
          $value_types[] = '%d';
          break;

      }

    }

    if ($values && $value_types) {

      $wpdb->update($table, $values, array('id' => $id), $value_types, array('%d'));

    }

    return true;

  }


  /**
	 * add
	 */
  public function add() {
    global $wpdb;

    $table = "{$wpdb->prefix}shows";

    $wpdb->insert($table, array('trash' => 0), array('%d'));

    return $wpdb->insert_id;

  }

	/**
	 * query
	 */
  public function query($params) {
    global $wpdb;

    $table = "{$wpdb->prefix}shows";

    // order

    // $order = '';
    //
    // if (isset($params['orderby'])) {
    //
    //   $dir = isset($params['order']) && $params['order'] === 'desc' ? 'DESC' : 'ASC';
    //
    //   switch ($params['orderby']) {
    //
    //     case 'name':
    //       $order = "ORDER BY name $dir";
    //       break;
    //
    //     default:
    //       $order = "ORDER BY start_date $dir, name ASC";
    //       break;
    //
    //   }
    //
    // }

    $order = "ORDER BY date ASC";


    // limit

    $limit = '';

    if (isset($params['ppp'])) {

      $ppp = intval($params['ppp']);

      if ($ppp > 0) {

        if (isset($params['page'])) {

          $page = intval($params['page']);

        } else {

          $page = 1;

        }

        $offset = $ppp*($page-1);

        $limit = "LIMIT $offset, $ppp";

      }

    }


    // where

    $where_clauses = array();

    $where_clauses[] = "trash = 0";


    if (isset($params['spectacle_id'])) {

      $spectacle_id = intval($params['spectacle_id']);
      $where_clauses[] = "spectacle_id = $spectacle_id";

    }

    // if (isset($params['date'])) {
    //
    //   $year = intval($params['date']);
    //   $next_year = $year + 1;
    //   $where_clauses[] = "start_date >= '$year' AND start_date < '$next_year'";
    //
    // }

    if (isset($params['ids']) ) {

      $ids = explode(',', $params['ids']);

      if ($ids) {

        $ids = array_map('intval', $ids);
        $ids = implode(',', $ids);

        $where_clauses[] = "id IN ($ids)";

      }

    }

    if ($where_clauses) {

      $where = "WHERE " .implode(' AND ', $where_clauses);

    } else {

      $where = '';
    }

    $sql = "SELECT
      id,
      DATE(date) AS 'date',
      DATE_FORMAT(date, '%H:%i') AS 'hour',
      status,
      infomaniak,
      place
      FROM $table
      $where
      $order
      $limit";

    return $wpdb->get_results($sql);

  }

  /**
	 * count
	 */
  public function count($params) {
    global $wpdb;

    $table = "{$wpdb->prefix}shows";

    // where

    $where_clauses = array();

    $where_clauses[] = "trash = 0";

    // if (isset($params['search'])) {
    //
    //   $search = esc_sql($params['search']);
    //   $where_clauses[] = "(name LIKE '%$search%' OR content LIKE '%$search%')";
    //
    // }

    if (isset($params['spectacle_id'])) {

      $spectacle_id = intval($params['spectacle_id']);
      $where_clauses[] = "spectacle_id = $spectacle_id";

    }

    // if (isset($params['date'])) {
    //
    //   $year = intval($params['date']);
    //   $next_year = $year + 1;
    //   $where_clauses[] = "start_date >= '$year' AND start_date < '$next_year'";
    //
    // }

    if (isset($params['ids']) ) {

      $ids = explode(',', $params['ids']);

      if ($ids) {

        $ids = array_map('intval', $ids);
        $ids = implode(',', $ids);

        $where_clauses[] = "id IN ($ids)";

      }

    }

    if ($where_clauses) {

      $where = "WHERE " .implode(' AND ', $where_clauses);

    } else {

      $where = '';
    }

    $sql = "SELECT COUNT(id) FROM $table $where";

    return $wpdb->get_var($sql);

  }




}
