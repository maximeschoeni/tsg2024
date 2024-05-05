<?php /* Template Name: Mediations */ ?>

<?php get_header(); ?>
<body class="page">
  <?php include get_stylesheet_directory().'/include/header.php'; ?>
  <?php
    while (have_posts()) {
      the_post();
      do_action('tsg_mediation_page');
    }
    wp_reset_postdata();
  ?>
  <?php include get_stylesheet_directory().'/include/footer.php'; ?>
	<?php wp_footer(); ?>
</body>
<?php get_footer(); ?>
