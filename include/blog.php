<main class="blog-page">
	<h1><?php echo __('News', 'tsg'); ?></h1>
	<div id="blog-posts" class="paged-<?php echo max(1, get_query_var('paged')); ?>">
		<?php
			require_once get_stylesheet_directory() . '/class-image.php';

			while (have_posts()) {
				the_post();
				include get_stylesheet_directory() . '/include/blog-post.php';
			}

			wp_reset_postdata();
		?>
	</div>
</main>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/vendor/masonry.pkgd.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/vendor/imagesloaded.pkgd.min.js"></script>

<script>
	const element = document.getElementById("blog-posts");
	var msnry = new Masonry(element, {
	  // options
	  itemSelector: '.blog-post',
	  columnWidth: '.post-index-1'
	});

	// msnry.imagesLoaded().progress( function() {
	//   $grid.masonry('layout');
	// });

	imagesLoaded(element).on( 'progress', function() {
	  // layout Masonry after each image loads
	  msnry.layout();
	});

</script>
