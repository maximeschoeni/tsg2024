Laribot.Slideshow = class {

  constructor(container) {

    const player = new KarmaPlayer();

    const transitionDuration = 400;

    const viewer = container.querySelector(".viewer");
    // const left = container.querySelector(".left-navigation");
    // const right = container.querySelector(".right-navigation");

    const items = [...viewer.children];

    for (let item of items) {
      player.add(item);
    }

    player.onShift = (element, index) => {
      element.style.transform = `translateX(${(index)*100}%)`;
      element.style.transition = "none";
    };

    player.onEnter = (element, direction, currentIndex) => {
      if (currentIndex <= -1 || currentIndex >= 1) {
        element.style.transform = `translateX(${direction*100}%)`;
        element.style.transition = "none";
        element.offsetTop; // force reflow
      }
      element.style.transform = "translateX(0)";
      element.style.transition = `transform ${transitionDuration}ms`;
    };

    player.onLeave = (element, direction) => {
      element.style.transform = `translateX(${-direction*100}%)`;
      element.style.transition = `transform ${transitionDuration}ms`;
    };

    player.onInit = (element, isCurrent) => {
      element.style.transform = `translateX(${isCurrent ? 0 : -100}%)`;
      element.style.transition = "none";
    };

    // left.onclick = event => {
    //   player.prev();
    // }
    //
    // right.onclick = event => {
    //   player.next();
    // }

  }




}
