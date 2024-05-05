<div class="agenda-container">
  <div class="home-agenda" id="calendar"></div>
  <div class="home-agenda" id="agenda"></div>
  <div class="home-agenda" id="archives"></div>
</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abduct.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abductions/calendar.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abductions/agenda.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/abductions/archives.js"></script>

<script>
  addEventListener("DOMContentLoaded", async event => {
    const archives = new TSG.Archives("archives");
    await archives.render();
    // await TSG.Agenda.preload();
  });
</script>
