<div class="slideshow home-slideshow" id="home-slideshow">
  <div class="slides">
    <?php foreach ($slides as $slide) { ?>
      <div class="slide">
        <a href="<?php echo $slide['permalink']; ?>" draggable="false">
          <figure>
            <?php if (isset($slide['image'])) { ?>
              <?php //var_dump($slide['image']['sizes']); ?>
              <img
                src="<?php echo $slide['image']['src'] ?>"
                width="<?php echo $slide['image']['width'] ?>"
                height="<?php echo $slide['image']['height'] ?>"
                srcset="<?php echo implode(',', array_map(function($size) {
                  return "{$size['src']} {$size['width']}w";
                }, $slide['image']['sizes'])) ?>"
                sizes="50vw"
                draggable="false"
              >
            <?php } ?>
          </figure>
          <h3 class="title"><?php echo $slide['title']; ?></h3>
          <div class="date"><?php echo $slide['date']; ?></div>
        </a>
      </div>
    <?php } ?>
  </div>
  <div class="nav" id="slideshow-nav">
    <!-- <div class="nav-left" id="nav-left">
    </div>
    <div class="nav-right" id="nav-right">
    </div> -->
  </div>
</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/player-v5.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/tracker.js"></script>
<script>
  addEventListener("DOMContentLoaded", event => {
    const container = document.getElementById("home-slideshow");
    const player = new KarmaPlayer();
    player.loop = false;
    const transitionDuration = 400;
    const viewer = container.querySelector(".slides");
    const items = [...viewer.children];

    // const navLeft = document.getElementById("nav-left");
    // const navRight = document.getElementById("nav-right");
    const nav = document.getElementById("slideshow-nav");

    const tracker = new Tracker(nav);

    tracker.onupdate = () => {
      player.shift(tracker.diffX/tracker.box.width);
    }

    tracker.oncomplete = () => {
      if (tracker.swipeRight) {
        // tracker.event.preventDefault();
        player.prev();
      } else if (tracker.swipeLeft) {
        // tracker.event.preventDefault();
        player.next();
      } else if (tracker.swipeFail) {
        // tracker.event.preventDefault();
        player.offset = 0;
        player.change(player.index);
      } else if (tracker.click) {
        // tracker.event.preventDefault();
        if (tracker.nX > 0.75) {
          player.next();
        } else if (tracker.nX > 0.25) {
          const a = items[player.index] && items[player.index].querySelector("a");
          if (a) {
            location.href = a.href;
          }
          // console.log(player.index, items[player.index]);
        } else {
          player.prev();
        }
      }
    }

    for (let item of items) {
      player.add(item);
    }

    player.onShift = (element, index) => {
      element.style.transform = `translateX(${(index)*100}%)`;
      element.style.transition = "none";
    };

    player.onSlide = (element, direction, relativeIndex) => {
      element.style.transform = `translateX(${(relativeIndex)*100}%)`;
      element.style.transition = `transform ${transitionDuration}ms`;
      element.classList.toggle("current", relativeIndex === 0);
    };

    player.onInit = (element, isCurrent, relativeIndex) => {
      element.style.transform = `translateX(${relativeIndex*100}%)`;
      element.style.transition = "none";
      element.classList.toggle("current", relativeIndex === 0);
    };

    player.init();
  });
</script>
