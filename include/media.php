<?php
  $width = isset($image['width']) ? (int) $image['width'] : 0;
  $height = isset($image['height']) ? (int) $image['height'] : 0;

  if ($width > $height) {

    $class = 'panorama';

  } else {

    $class = 'portrait';

  }
?>
<figure class="<?php echo $class; ?>">
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
</figure>
