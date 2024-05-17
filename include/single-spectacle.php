<?php
  $subtitle = get_post_meta($post->ID, 'subtitle', true);
  $description = get_post_meta($post->ID, 'description', true);
  $duration = get_post_meta($post->ID, 'duration', true);

  $simple = get_post_meta($post->ID, 'simple', true);

  $credits = get_post_meta($post->ID, 'credits');
  $soutien = get_post_meta($post->ID, 'soutien', true);
  $detail = get_post_meta($post->ID, 'detail', true);

  $avertissement = get_post_meta($post->ID, 'avertissement', true);
  $atelier = null;
  $presses = get_post_meta($post->ID, 'presses');


  $ticket_url = '#';
  // $mediation_url = '#';
  // $blog_url = '#';



?>
<main class="single-spectacle-page">
  <h1><?php the_title(); ?></h1>
  <?php include get_stylesheet_directory().'/include/spectacle-slideshow.php'; ?>
  <div class="single-spectacle-columns">
    <div class="single-spectacle-column left-column">
    </div>
    <div class="single-spectacle-column center-column">
      <div class="subtitle">
        <?php if ($subtitle) { ?>
          <h2 class="auteur"><?php echo $subtitle; ?></h2>
        <?php } ?>
        <?php if ($description) { ?>
          <div class="description"><?php echo $description; ?></div>
        <?php } ?>
      </div>
      <?php if ($duration) { ?>
        <div class="duration"><span><?php echo gmdate('G \h i', $duration*60); ?></span></div>
      <?php } ?>
    </div>
    <div class="single-spectacle-column right-column">
    </div>
  </div>

  <div class="single-spectacle-columns">
    <div class="single-spectacle-column left-column">
      <div class="single-spectacle-aside">
        <?php if ($ticket_url) { ?>
          <a class="button" href="<?php echo $ticket_url; ?>"><?php echo __('Billets', 'tsg'); ?></a>
        <?php } ?>
        <?php if ($shows) { ?>
          <ul class="shows">
            <?php foreach ($shows as $show) { ?>
              <li>
                <span class="weekday"><?php echo $show['weekday']; ?></span>
                <span class="date"><?php echo $show['date']; ?></span>
                <span class="hour"><?php echo $show['hour']; ?></span>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>

        <?php if (isset($blog_url) && $blog_url) { ?>
          <a class="button" href="<?php echo $blog_url; ?>"><?php echo __('Voir articles liés sur le blog', 'tsg'); ?></a>
        <?php } ?>
        <?php if (isset($mediation_url) && $mediation_url) { ?>
          <a class="button" href="<?php echo $mediation_url; ?>"><?php echo $mediation_name; // __('Atelier d’écriture', 'tsg'); ?></a>
        <?php } ?>
        <?php if ($simple) { ?>
          <a class="button" onclick=""><?php echo __('Dit simplement', 'tsg'); ?></a>
        <?php } ?>
      </div>
    </div>
    <div class="single-spectacle-column center-column">
      <div class="content">
        <?php the_content(); ?>
      </div>
      <div class="simple-content">
        <?php echo $simple; ?>
      </div>
      <?php if ($credits || $soutien || $detail) { ?>
        <div class="credits">
          <?php if ($credits) { ?>
            <table>
              <?php foreach ($credits as $credit) { ?>
                <tr>
                  <td class="name"><?php echo $credit['name']; ?></td>
                  <td class="fonction"><?php echo $credit['fonction']; ?></td>
                </tr>
              <?php } ?>
            </table>
          <?php } ?>
          <?php if ($soutien) { ?>
            <div class="soutien">
              <?php echo $soutien; ?>
            </div>
          <?php } ?>
          <?php if ($detail) { ?>
            <p class="soutien">
              <?php echo nl2br($detail); ?>
            </p>
          <?php } ?>
        </div>
      <?php } ?>
      <?php if ($avertissement) { ?>
        <div class="avertissement">
          <h3><?php echo __('Avertissement au public', 'tsg'); ?></h3>
          <?php echo $avertissement; ?>
        </div>
      <?php } ?>
      <?php if ($atelier) { ?>
        <div class="atelier">
          <h3><?php echo get_the_title($atelier); ?></h3>
          <?php echo $atelier->post_content; ?>
        </div>
      <?php } ?>
      <?php if ($presses) { ?>
        <div class="press">
          <h3><?php echo __('Le coin presse', 'tsg'); ?></h3>
          <ul>
            <?php foreach ($presses as $press) { ?>
              <li>
                <?php if (isset($press['name']) && $press['name']) { ?>
                  <span class="press-name"><?php echo $press['name']; ?>: </span>
                <?php } ?>
                <?php if (isset($press['title'])) { ?>
                  <?php if (isset($press['url'])) { ?>
                    <a href="<?php echo $press['url']; ?>" target="_blank" class="press-title">
                  <?php } ?>
                  <?php echo $press['title']; ?>
                  <?php if (isset($press['url'])) { ?>
                    </a>
                  <?php } ?>
                <?php } ?>
              </li>
            <?php } ?>
          </ul>
        </div>
      <?php } ?>

    </div>
    <div class="single-spectacle-column right-column">
    </div>
  </div>
  <div class="single-spectacle-prochainement">
    <h3><?php echo __('Prochainement', 'tsg'); ?></h3>
    <?php do_action('tsg_future_spectacles'); ?>
  </div>
</main>
