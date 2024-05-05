<div class="blog-post post-index-<?php echo $wp_query->current_post; ?>">

		<div class="single-column">
			<h2><?php the_title(); ?></h2>
			<div class="date">
				<?php echo get_the_date('d F Y'); ?>
			</div>
			<?php $image_id = get_post_meta($post->ID, '_thumbnail_id', true); ?>
			<?php if ($image_id) { ?>
				<a href="<?php echo get_permalink($post); ?>" class="media <?php ?>">
					<?php
						$image = Karma_Image::get_image_source($image_id);
						include get_stylesheet_directory().'/include/media.php';
					?>
				</a>
			<?php } ?>
			<?php /*if ($image_id) { ?>
				<a href="<?php echo get_permalink($post); ?>" class="media <?php ?>">
					<?php $mime = get_post_mime_type($image_id); ?>
					<?php if (strpos($mime, 'video') !== false) { ?>
						<video width="250" autoplay muted loop playsinline>
							<source src="<?php echo wp_get_attachment_url($image_id) ?>" type="<?php echo $mime; ?>">
						</video>
					<?php } else { ?>
						<?php
							$image = Karma_Image::get_image_source($image_id); // array('medium', 'medium_large', 'large')
							$width = (int) $image['width'];
							$height = (int) $image['height'];
							$classes = array();
							if ($width/$height > 1.2) {
								$classes[] = 'panorama';
							} else {
								$classes[] = 'portrait';
							}
						?>
						<img
							class="<?php echo implode(' ', $classes); ?>"
							src="<?php echo $image['src'] ?>"
							width="<?php echo $image['width']; ?>"
							height="<?php echo $image['height']; ?>"
							alt="<?php echo $image['alt'] ?>"
							srcset="<?php echo implode(', ', array_map(function($size) {
								return $size['src'].' '.$size['width'].'w';
							}, $image['sizes'])) ?>"
							sizes="(min-width: 900px) 50vw, 100vw"
						>
					<?php } ?>
				</a>
			<?php } */ ?>
			<div class="excerpt">
				<?php the_excerpt() ?>
			</div>
			<a class="readmore" href="<?php echo get_permalink($post); ?>"><?php echo __('Plus', 'tsg'); ?></a>
		</div>

		
</div>
