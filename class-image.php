<?php


/*

19/05/2021
- add support gif


*/


class Karma_Image {

  /**
	 * Get image sources
	 */
	public static function get_image_source($img_id, $img_sizes = array('medium', 'large', '1536x1536', '2048x2048'), $include_full = true) {
		static $baseurl;

		if (!isset($baseurl)) {

			$uploads = wp_get_upload_dir();
			$baseurl = $uploads['baseurl'] . '/';

		}

		$sources = array();
    $sources['id'] = $img_id;
    $sources['alt'] = get_post_meta($img_id, '_wp_attachment_image_alt', true);
    $sources['title'] = get_the_title($img_id);
    $sources['caption'] = wp_get_attachment_caption($img_id);

		$metadata = wp_get_attachment_metadata($img_id);
		$path = '';
		$file = get_post_meta($img_id, '_wp_attached_file', true);

    $type = get_post_mime_type($img_id);

		$sources['mimetype'] = $type;

		if ($type === 'image/jpeg' || $type === 'image/jpg' || $type === 'image/png') {

			if (!$img_sizes) {

				$img_sizes = get_intermediate_image_sizes();

			}

			$basename = basename($file);
			$path = str_replace($basename, '', $file);

			foreach ($img_sizes as $img_size) {

				if (isset($metadata['sizes'][$img_size])) {


					$sources['sizes'][] = array(
						'src' => $baseurl . $path . $metadata['sizes'][$img_size]['file'],
						'width' => $metadata['sizes'][$img_size]['width'],
						'height' => $metadata['sizes'][$img_size]['height']
					);

				}

			}

			if (!$sources) {

        $sources['sizes'][] = array();
				// $sources['sizes'][] = array(
				// 	'src' => $baseurl . $file,
				// 	'width' => $metadata['width'],
				// 	'height' => $metadata['height']
				// );

			}

		  // full ->
			if ($include_full) {
				$sources['sizes'][] = array(
					'src' => $baseurl . $file,
					'width' => $metadata['width'],
					'height' => $metadata['height']
				);
			}

			$sources['src'] = $baseurl . $file;
      $sources['width'] = $metadata['width'];
      $sources['height'] = $metadata['height'];

		} else if ($type === 'image/gif') {

			$sources['src'] = $baseurl . $file;
      $sources['width'] = $metadata['width'];
      $sources['height'] = $metadata['height'];
			$sources['sizes'] = array();

		} else if (strpos($type, 'video') !== false) {

      $sources['src'] = $baseurl . $file;
      $sources['width'] = $metadata['width'];
      $sources['height'] = $metadata['height'];

		} else {

			$sources['src'] = $baseurl . $file;
			$sources['meta'] = $metadata;
      // $sources['width'] = $metadata['width'];
      // $sources['height'] = $metadata['height'];

		}

		return $sources;

	}


	/**
	 * Cache images
	 */
	public static function cache_images($ids) {
		global $wpdb;

		if ($ids) {

			$sql_ids = implode(",", array_map('intval', $ids));

			$sql = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ID IN ($sql_ids)";

			$attachments = $wpdb->get_results($sql);

			if ($attachments) {

				update_post_caches($attachments, 'any', false, true);

			}

		}

	}

	/**
	 * Cache images
	 */
	public static function cache_image_query($query, $meta_keys) {

		$ids = array();

		foreach ($query->posts as $post) {

			foreach ($meta_keys as $meta_key) {

				$ids = array_merge($ids, get_post_meta($post->ID, $meta_key));

			}

		}

		$ids = array_unique($ids);

		self::cache_images($ids);

	}


	/**
	 * Cache images in posts
	 */
	public static function cache_posts_images($posts, $meta_keys) {

		$ids = array();

		foreach ($posts as $post) {

			foreach ($meta_keys as $meta_key) {

				$ids = array_merge($ids, get_post_meta($post->ID, $meta_key));

			}

		}

		$ids = array_unique($ids);

		self::cache_images($ids);

	}


}


//
// class Karma_Image {
//
//   /**
// 	 * Get image sources
// 	 */
// 	public static function get_image_source($img_id, $img_sizes = null, $type = null) {
// 		static $baseurl;
//
// 		if (!isset($baseurl)) {
//
// 			$uploads = wp_get_upload_dir();
// 			$baseurl = $uploads['baseurl'] . '/';
//
// 		}
//
// 		$sources = array();
//     $sources['id'] = $img_id;
//     $sources['alt'] = get_post_meta($img_id, '_wp_attachment_image_alt', true);
//     $sources['title'] = get_the_title($img_id);
//     $sources['caption'] = wp_get_attachment_caption($img_id);
// 		$sources['sizes'] = array();
//
// 		$metadata = wp_get_attachment_metadata($img_id);
// 		$path = '';
// 		$file = get_post_meta($img_id, '_wp_attached_file', true);
//
//     if (!isset($type)) {
//
//       $type = get_post_mime_type($img_id);
//
//     }
//
// 		if ($type === 'image/jpeg' || $type === 'image/jpg' || $type === 'image/png') {
//
// 			if (!$img_sizes) {
//
// 				$img_sizes = get_intermediate_image_sizes();
//
// 			}
//
// 			$basename = basename($file);
// 			$path = str_replace($basename, '', $file);
//
// 			foreach ($img_sizes as $img_size) {
//
// 				if (isset($metadata['sizes'][$img_size])) {
//
//
// 					$sources['sizes'][] = array(
// 						'src' => $baseurl . $path . $metadata['sizes'][$img_size]['file'],
// 						'width' => $metadata['sizes'][$img_size]['width'],
// 						'height' => $metadata['sizes'][$img_size]['height']
// 					);
//
// 				}
//
// 			}
//
// 			if (!$sources) {
//
//         $sources['sizes'][] = array();
// 				// $sources['sizes'][] = array(
// 				// 	'src' => $baseurl . $file,
// 				// 	'width' => $metadata['width'],
// 				// 	'height' => $metadata['height']
// 				// );
//
// 			}
//
// 		  // full ->
// 			$sources['src'] = $baseurl . $file;
//       $sources['width'] = $metadata['width'];
//       $sources['height'] = $metadata['height'];
//
// 			if (in_array('full', $img_sizes)) {
//
// 				$sources['sizes'][] = array(
// 					'src' => $baseurl . $file,
// 					'width' => $metadata['width'],
// 					'height' => $metadata['height']
// 				);
//
// 			}
//
// 		} else if (strpos($type, 'video') !== false) {
//
//       $sources['src'] = $baseurl . $file;
//       $sources['width'] = $metadata['width'];
//       $sources['height'] = $metadata['height'];
//
// 		}
//
// 		return $sources;
//
// 	}
//
//
// 	/**
// 	 * Cache images
// 	 */
// 	public static function cache_images($ids) {
// 		global $wpdb;
//
// 		if ($ids) {
//
// 			$sql_ids = implode(",", array_map('intval', $ids));
//
// 			$sql = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ID IN ($sql_ids)";
//
// 			$attachments = $wpdb->get_results($sql);
//
// 			if ($attachments) {
//
// 				update_post_caches($attachments, 'any', false, true);
//
// 			}
//
// 		}
//
// 	}
//
//
// }
//
//
// new Karma_Image;
//
//
//
