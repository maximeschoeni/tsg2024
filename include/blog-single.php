<h1><?php the_title(); ?></h1>
<div class="single-column">
	<div class="date">
		<?php echo get_the_date('d F Y'); ?>
	</div>
	<div class="content">
		<?php the_content(); ?>
	</div>
</div>
<?php if ($spectacles) { ?>
	<div class="mediation-spectacles relation-spectacles">
		<h4><?php echo __('Spectacles liés', 'tsg') // count($spectacles) > 1 ? __('Spectacles liés', 'tsg') : __('Spectacle lié', 'tsg'); ?></h4>
		<?php
			include get_stylesheet_directory().'/include/agenda-future-spectacle.php';
		?>
	</div>
<?php } ?>
