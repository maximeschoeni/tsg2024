<?php
	$image_id = get_post_meta($post->ID, 'image', true);
	if ($image_id) {
		$image = Karma_Image::get_image_source($image_id);
	}
	$template = get_page_template_slug($post);
	if ($template) {
		$template = substr($template, 0, -4);
	}
?>
<main class="single-page <?php echo $template; ?>">
	<h1><?php the_title(); ?></h1>
	<div class="single-column">
		<?php if (isset($image)) { ?>
			<?php include get_stylesheet_directory().'/include/media.php'; ?>
			<?php /* <figure class="main-page-media">
				<?php if (substr($image['mimetype'], 0, 5) === "image") { ?>
					<img
						width="<?php echo $image['width']; ?>"
						height="<?php echo $image['height']; ?>"
						src="<?php echo $image['src']; ?>"
						<?php if (!empty($image['sizes'])) { ?>
							srcset="<?php echo implode(',', array_map(function($size) {
								return "{$size['src']} {$size['width']}w";
							}, $image['sizes'])); ?>"
							sizes="(min-width: 900px) 50vw, 100vw"
						<?php } ?>
					/>
				<?php } else if (substr($image['mimetype'], 0, 5) === "video") { ?>
					<video width="250" autoplay muted loop playsinline>
						<source src="<?php echo $image['src'] ?>" type="<?php echo $image['mimetype']; ?>">
					</video>
				<?php } ?>
				<?php if ($image['caption']) { ?>
					<figcaption class="caption"><?php echo $image['caption']; ?></figcaption>
				<?php } ?>
			</figure> */ ?>
		<?php } ?>
		<div class="content main-page-content">
			<?php the_content(); ?>
		</div>
	</div>

	<?php if ($subpages_query->posts) { ?>
		<div class="single-page-columns">
	    <div class="single-page-column left-column">
				<nav class="subpages-nav">
					<ul>
						<?php foreach ($subpages_query->posts as $subpage) { ?>
							<li><a class="button" href="#<?php echo $subpage->post_name; ?>"><?php echo get_the_title($subpage); ?></a></li>
						<?php } ?>
					</ul>
					<script>
						const buttons = document.querySelectorAll(".subpages-nav a");
						for (let button of buttons) {
							button.onclick = event => {
								event.preventDefault();
								const hash = button.hash.slice(1);
								const section = document.getElementById(`section-${hash}`);
								scrollTo({
									top: section.offsetTop,
									behavior: "smooth"
								})
							}
						}
					</script>
				</nav>
	    </div>
	    <div class="single-page-column center-column">
				<div class="subpages">
					<?php foreach ($subpages_query->posts as $subpage) { ?>
						<?php
							$image_id = get_post_meta($subpage->ID, 'image', true);
							if ($image_id) {
								$image = Karma_Image::get_image_source($image_id);
							} else {
								$image = null;
							}
							$template = get_page_template_slug($subpage);
							if ($template) {
								$template = substr($template, 0, -4);
							}
						?>
						<section class="<?php echo $template; ?>" id="section-<?php echo $subpage->post_name; ?>">
							<h2><?php echo get_the_title($subpage); ?></h2>
							<div class="single-column">
								<?php if (isset($image)) { ?>
									<?php include get_stylesheet_directory().'/include/media.php'; ?>
									<?php /*
									<figure class="subpage-media">
										<?php if (substr($image['mimetype'], 0, 5) === "image") { ?>
											<img
												width="<?php echo $image['width']; ?>"
												height="<?php echo $image['height']; ?>"
												src="<?php echo $image['src']; ?>"
												<?php if (!empty($image['sizes'])) { ?>
													srcset="<?php echo implode(',', array_map(function($size) {
														return "{$size['src']} {$size['width']}w";
													}, $image['sizes'])); ?>"
													sizes="(min-width: 900px) 50vw, 100vw"
												<?php } ?>
											/>
										<?php } else if (substr($image['mimetype'], 0, 5) === "video") { ?>
											<video width="250" autoplay muted loop playsinline>
												<source src="<?php echo $image['src'] ?>" type="<?php echo $image['mimetype']; ?>">
											</video>
										<?php } ?>
										<?php if ($image['caption']) { ?>
											<figcaption class="caption"><?php echo $image['caption']; ?></figcaption>
										<?php } ?>
									</figure> */ ?>
								<?php } ?>
								<div class="content subpage-content"><?php echo apply_filters('the_content', $subpage->post_content); ?></div>
								<?php $left_column = get_post_meta($subpage->ID, 'left-column', true); ?>
								<?php $right_column = get_post_meta($subpage->ID, 'right-column', true); ?>
								<?php if ($left_column || $right_column) { ?>
									<div class="subpage-columns">
										<div class="subpage-column left-column content">
											<?php echo $left_column; ?>
										</div>
										<div class="subpage-column right-column content">
											<?php echo $right_column; ?>
										</div>
									</div>
								<?php } ?>
							</div>
						</section>
					<?php } ?>
				</div>
	    </div>
	    <div class="single-page-column right-column">
	    </div>
	  </div>
	<?php } ?>


</main>
