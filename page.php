<?php

  $object = get_queried_object();

  if ($object->post_parent != 0) {

    $url = get_permalink($object->post_parent);

    wp_redirect("{$url}#{$object->post_name}");
    exit;

  }

get_header(); ?>
<body class="page">
  <?php include get_stylesheet_directory().'/include/header.php'; ?>
  <?php
    while (have_posts()) {
      the_post();
      // include get_stylesheet_directory().'/include/page.php';

      do_action('tsg_page');
    }
    wp_reset_postdata();
  ?>
  <?php include get_stylesheet_directory().'/include/footer.php'; ?>
	<?php wp_footer(); ?>
</body>
<?php get_footer(); ?>
