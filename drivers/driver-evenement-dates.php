<?php


Class Laribot_Driver_Evenement_Dates {

  /**
	 * query
	 */
  public function query($params) {
    global $wpdb;

    $table = "{$wpdb->prefix}evenements";

    $sql = "SELECT DISTINCT YEAR(start_date) AS year
      FROM $table
      WHERE trash = 0
      ORDER BY start_date DESC";

    $results = $wpdb->get_results($sql);

    $output = array();

    foreach ($results as $result) {

      $output[] = array(
        'id' => "$result->year",
        'name' => "$result->year",
      );

    }

    return $output;

  }

}
