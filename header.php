<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0">
	<meta name="description" content="<?php echo get_bloginfo('description'); ?>">
	<title><?php echo get_bloginfo('name'); ?><?php echo wp_title('|'); ?></title>
	<script>
		var TSG = {
			rest_url: "<?php echo rest_url(); ?>",
			theme_url: "<?php echo get_stylesheet_directory_uri(); ?>",
			home_url: "<?php echo home_url(); ?>",
		};
	</script>
	<?php include get_stylesheet_directory().'/include/translations.php'; ?>
	<?php wp_head(); ?>
</head>
