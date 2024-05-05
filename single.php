<?php get_header(); ?>
<body class="blog">
  <?php include get_stylesheet_directory().'/include/header.php'; ?>
  <?php // include get_stylesheet_directory().'/include/blog-single.php'; ?>
  <main class="blog-single">
		<?php
			while (have_posts()) {
				the_post();
        do_action('tsg_post');
      }
			wp_reset_postdata();
		?>
  </main>
  <?php include get_stylesheet_directory().'/include/footer.php'; ?>
	<?php wp_footer(); ?>
</body>
<?php get_footer(); ?>
