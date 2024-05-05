<main class="single-page page-mediations">
	<h1><?php the_title(); ?></h1>

	<div class="single-page-columns">
		<div class="single-page-column left-column">
			<nav class="subpages-nav">
				<ul>
					<?php /* foreach ($directory as $term_id => $mediations) { ?>
						<?php $term = get_term($term_id); ?>
						<li><a class="button" href="#<?php echo $term->slug; ?>"><?php echo $term->name; ?></a></li>
					<?php } */ ?>
					<?php foreach ($categories as $term) { ?>
						<li><a class="button" href="#<?php echo $term->slug; ?>"><?php echo $term->name; ?></a></li>
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
			<div class="single-column">
				<div class="content main-page-content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
		<div class="single-page-column right-column">
		</div>
	</div>


	<?php /* foreach ($directory as $term_id => $mediations) { ?>
		<?php $term = get_term($term_id);*/ ?>
	<?php foreach ($categories as $term) { ?>
		<section class="mediation-category" id="section-<?php echo $term->slug; ?>">
			<h2><?php echo $term->name; ?></h2>
			<div class="mediation-column">
				<div class="content mediation-category-description"><?php echo $term->description; ?></div>
			</div>
			<?php if (isset($directory[$term->term_id]) && $directory[$term->term_id]) { ?>
				<div class="mediation-category-items">
					<?php foreach ($directory[$term->term_id] as $mediation) { ?>
						<?php
							$image_id = get_post_meta($mediation->ID, '_thumbnail_id', true);
							if ($image_id) {
								$image = Karma_Image::get_image_source($image_id);
							} else {
								$image = null;
							}
						?>
						<div class="mediation" id="mediation-<?php echo $mediation->post_name; ?>">
							<h3><?php echo get_the_title($mediation); ?></h3>
							<div class="single-column">
								<?php if (isset($image)) { ?>
									<?php include get_stylesheet_directory().'/include/media.php'; ?>
									<?php /* <figure class="subpage-media">
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
								<div class="content mediation-content"><?php echo apply_filters('the_content', $mediation->post_content); ?></div>
							</div>
							<?php $spectacle_parents = get_post_meta($mediation->ID, 'parent_spectacle'); ?>
							<?php if ($spectacle_parents) { ?>
								<div class="mediation-spectacles relation-spectacles">
									<h4><?php echo __('Vers le spectacle', 'tsg'); ?></h4>
									<?php
										$spectacles = array();
										foreach ($spectacle_parents as $spectacle_id) {
											if (isset($mediation_spectacles[$spectacle_id])) {
												$spectacles[] = $mediation_spectacles[$spectacle_id];
											}
										}
										include get_stylesheet_directory().'/include/agenda-future-spectacle.php';
									?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</section>
	<?php } ?>
</main>
