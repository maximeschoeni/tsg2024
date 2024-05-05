<div class="agenda-container">
<div class="home-agenda" id="calendar">
  <div class="agenda calendar">
    <div class="agenda-header">
      <div class="agenda-title">
        <?php echo date_i18n('F Y'); ?>
      </div>
      <div class="agenda-nav">
        <button><</button>
        <button><?php echo __('Aujourd’hui', 'tsg'); ?></button>
        <button>></button>
        <button><?php echo __('Liste', 'tsg'); ?></button>
        <button>Q</button>
      </div>
      <div class="placeholder"></div>
    </div>
    <div class="calendar-body">
      <div class="calendar-cells-header">
        <?php for ($i = 0; $i < 7; $i++) { ?>
          <div class="calendar-header-cell">
            <?php echo date_i18n('D.', $times[$i]['time']) ?>
          </div>
        <?php } ?>
      </div>
      <div class="calendar-cells-body">
        <?php foreach ($times as $time) { ?>
          <?php
            $classes = array('calendar-cell');
            if ($time['is_today']) {
              $classes[] = 'today';
            }
            if ($time['is_offset']) {
              $classes[] = 'offset';
            }

            $date = $time['date'];
            $items = isset($calendar[$date]) ? $calendar[$date] : array();
            $count = count($items);

            if ($count === 1) {
              $classes[] = 'count-1';
            } else if ($count === 2) {
              $classes[] = 'count-2';
            } else if (count($items) >= 3) {
              $classes[] = 'count-4';
            }

            $day = $time['is_first_day'] ? date_i18n('j M.', $time['time']) : date('j', $time['time']);

          ?>
          <div class="<?php echo implode(' ', $classes); ?>">
            <div class="day"><?php echo $day; ?></div>
            <div class="shows">
              <?php foreach ($items as $i => $item) { ?>
                <a class="show" href="<?php echo $item['permalink'] ?>">
                  <div class="media">
                    <figure>
                      <?php if (isset($item['image']) && substr($item['image']['mimetype'], 0, 5) === 'image') { ?>
                        <img
                          src="<?php echo $item['image']['src'] ?>"
                          width="<?php echo $item['image']['width'] ?>"
                          height="<?php echo $item['image']['height'] ?>"
                          srcset="<?php echo implode(',', array_map(function($size) {
                            return "{$size['src']} {$size['width']}w";
                          }, $item['image']['sizes'])) ?>"
                          sizes="14vw"
                          draggable="false"
                        >
                      <?php } ?>
                    </figure>
                    <figure>
                      <!-- video -->
                    </figure>
                  </div>
                  <h3 class="title"><?php echo $item['title']; ?></h3>
                </a>
              <?php } ?>
            </div>
          </div>

        <?php } ?>
      </div>
    </div>
  </div>
</div>
<div class="home-agenda" id="agenda"></div>
</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abduct.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abductions/calendar.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abductions/agenda.js"></script>
<script>
  TSG.calendar = {
    saisons: <?php echo json_encode((object) array_combine(array_values($current_saison_ids), array_fill(0, count($current_saison_ids), true))); ?>, // queried saison ids
    shows: <?php echo json_encode($calendar); ?> // shows grouped by days
  };
</script>
<script>
  addEventListener("DOMContentLoaded", async event => {
    const calendar = new TSG.Calendar("calendar");
    await calendar.render();

    await TSG.Agenda.preload();

    // const agenda = new TSG.Agenda();
    // await agenda.render();


  })
</script>
