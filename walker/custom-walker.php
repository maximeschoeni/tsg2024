<?php

class Laribot_Walker_Nav_Menu extends Walker_Nav_Menu {

  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= "<div class=\"sub-menu\"><ul>";
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= "</ul></div>";
  }

}
