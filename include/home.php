<main class="home">
  <div id="intro">
    <img src="<?php echo get_stylesheet_directory_uri().'/images/maison-saint-gervais.png'; ?>" />
    <?php //include get_stylesheet_directory().'/images/maison-saint-gervais.svg'; ?>
  </div>
  <script>
  {
    const intro = document.getElementById("intro");
    intro.onclick = () => {
      intro.classList.add("done");
    }
    const onwheel = () => {
      intro.classList.add("done");
      window.removeEventListener("wheel", onwheel);
    }
    window.addEventListener("wheel", onwheel);
    intro.ontransitionend = () => {
      intro.style.display = "none";
      document.body.classList.remove("frozen");
    }
    document.body.classList.add("frozen");
  }

  </script>
  <?php do_action('tsg_home_slideshow'); ?>
  <?php do_action('tsg_home_agenda'); ?>
  <div class="home-archives">
    <a href="<?php echo home_url('archives'); ?>"><?php echo __('Archives', 'tsg'); ?></a>
  </div>
</main>
