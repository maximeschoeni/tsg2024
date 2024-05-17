<?php get_header(); ?>
<body class="single-spectacle">
  <?php include get_stylesheet_directory().'/include/header-single.php'; ?>

  <?php

    while (have_posts()) {

      the_post();

      do_action('tsg_single_spectacle', $post);


      // do_action('single_spectacle');
      // do_action('tsg_single_spectacle');


    }

  ?>
  <?php include get_stylesheet_directory().'/include/footer.php'; ?>
	<?php wp_footer(); ?>
</body>
<?php get_footer(); ?>
