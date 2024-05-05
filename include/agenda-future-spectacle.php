<div class="list-body">
  <div class="spectacles-container">
    <?php foreach($spectacles as $spectacle) { ?>
      <div class="spectacle-row">
        <a class="media" href="<?php echo $spectacle['permalink']; ?>">

          <?php if (isset($spectacle['image']) && $spectacle['image']['src'] && substr($spectacle['image']['mimetype'], 0, 5) === 'image') { ?>
            <figure>
              <img
                src="<?php echo $spectacle['image']['src'] ?>"
                width="<?php echo $spectacle['image']['width'] ?>"
                height="<?php echo $spectacle['image']['height'] ?>"
                srcset="<?php echo implode(',', array_map(function($size) {
                  return "{$size['src']} {$size['width']}w";
                }, $spectacle['image']['sizes'])) ?>"
                sizes="14vw"
                draggable="false"
              >
            </figure>
          <?php } else { ?>
            <figure class="hidden"></figure>
          <?php } ?>

          <?php if (isset($spectacle['image']) && $spectacle['image']['src'] && substr($spectacle['image']['mimetype'], 0, 5) === 'video') { ?>
            <figure>
              <img
                src="<?php echo $spectacle['image']['src'] ?>"
                width="<?php echo $spectacle['image']['width'] ?>"
                height="<?php echo $spectacle['image']['height'] ?>"
                draggable="false"
              >
            </figure>
          <?php } else { ?>
            <figure class="hidden"></figure>
          <?php } ?>


        </a>
        <div class="text">
          <div class="spectacle-main">
            <div class="title">
              <?php echo $spectacle['title']; ?>
            </div>
            <div class="subtitle">
              <?php echo $spectacle['subtitle']; ?>
            </div>
            <div class="description">
              <?php echo $spectacle['description']; ?>
            </div>
            <div class="dates">
              <?php echo $spectacle['date']; ?>
            </div>
          </div>
          <div class="spectacle-nav">
            <a href="<?php echo $spectacle['permalink']; ?>" class="button"><?php echo __('Infos', 'tsg'); ?></a>
            <?php if (isset($spectacle['ticket']) && $spectacle['ticket']) { ?>
              <a href="<?php echo $spectacle['ticket']; ?>" class="button"><?php echo __('Billets', 'tsg'); ?></a>
            <?php } ?>
            <?php if (isset($spectacle['mediation']) && $spectacle['mediation']) { ?>
              <a href="<?php echo $spectacle['mediation']; ?>" class="button"><?php echo __('Écoles', 'tsg'); ?></a>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
