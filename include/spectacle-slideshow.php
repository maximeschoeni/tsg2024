<div class="slideshow spectacle-slideshow" id="spectacle-slideshow">
  <div class="slides">
    <?php foreach ($image_ids as $image_id) { ?>
      <?php
        $image = Karma_Image::get_image_source($image_id);
      ?>
      <div class="slide">
        <figure>
          <?php if ($image) { ?>
            <img
              src="<?php echo $image['src'] ?>"
              width="<?php echo $image['width'] ?>"
              height="<?php echo $image['height'] ?>"
              srcset="<?php echo implode(',', array_map(function($size) {
                return "{$size['src']} {$size['width']}w";
              }, $image['sizes'])) ?>"
              sizes="100vw"
              draggable="false"
            >
          <?php } ?>
          <small><?php echo wp_get_attachment_caption($image_id); ?></small>
        </figure>
      </div>
    <?php } ?>
  </div>
  <div class="nav" id="slideshow-nav"></div>
  <div class="slideshow-pagination">
    <ul>
      <?php foreach ($image_ids as $image_id) { ?>
        <li></li>
      <?php } ?>
    </ul>
  </div>

</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/player-v5.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/tracker.js"></script>
<script>
  addEventListener("DOMContentLoaded", event => {
    const container = document.getElementById("spectacle-slideshow");
    const player = new KarmaPlayer();
    player.loop = true;
    const transitionDuration = 400;
    const viewer = container.querySelector(".slides");
    const items = [...viewer.children];
    const nav = document.getElementById("slideshow-nav");
    const tracker = new Tracker(nav);
    tracker.threshold = Infinity;
    const bullets = container.querySelectorAll(".slideshow-pagination ul li");
    bullets[0].classList.add("active");
    for (let i = 0; i < bullets.length; i++) {
      bullets[i].onclick = () => {
        player.change(i);
      }
    }

    tracker.onupdate = () => {
      // player.shift(tracker.diffX/tracker.box.width);
    }

    tracker.oncomplete = () => {
      // if (tracker.swipeRight) {
      //   player.prev();
      // } else if (tracker.swipeLeft) {
      //   player.next();
      // } else if (tracker.swipeFail) {
      //   player.offset = 0;
      //   player.change(player.index);
      // } else
      if (tracker.click) {
        if (tracker.nX > 0.5) {
          player.next();
        } else {
          player.prev();
        }
      }
    }

    for (let item of items) {
      player.add(item);
    }

    player.onShift = (element, relativeIndex) => {
      // element.style.opacity = Math.min(Math.abs(1 - relativeIndex), 1);
      // element.style.transition = "none";
    };

    player.onSlide = (element, direction, relativeIndex, index) => {
      element.classList.toggle("current", relativeIndex === 0);
      if (relativeIndex === 0) {
        element.style.opacity = 0;
        element.style.transition = "none";
        element.offsetTop; // force reflow
        element.style.zIndex = player.slides.length;
        element.style.opacity = 1;
        element.style.transition = `opacity ${transitionDuration}ms`;
      } else if (index - player.index === 0) {
        element.style.zIndex = player.slides.length - 1;
        element.style.opacity = 0;
        element.style.transition = `opacity ${transitionDuration}ms ${transitionDuration}ms`;
      } else {
        element.style.zIndex = 0;
        element.style.opacity = 0;
      }
      bullets[index].classList.toggle("active", relativeIndex === 0);
    };

    player.onInit = (element, isCurrent, relativeIndex) => {
      // element.style.transform = `translateX(${relativeIndex*100}%)`;
      // element.style.transition = "none";
      element.style.opacity = relativeIndex === 0 ? 1 : 0;
      element.style.transition = "none";
      element.classList.toggle("current", relativeIndex === 0);
    };

    // player.onInit =
    //
    // this.onChange(nextIndex, this.index, direction);

    player.init();
  });
</script>
