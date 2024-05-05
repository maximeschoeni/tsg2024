class KarmaPlayer {

  constructor() {
    this.slides = [];
    this.events = {};
    this.index = 0;
    this.offset = 0;
    this.loop = true;

    this.duration = 4500;

  }

  add(element) {

    this.slides.push(element);

  }

  on(name, callback) {

    if (!this.events[name]) {

      this.events[name] = [];

    }

    this.events[name].push(callback);
  }

  trigger(name, ...args) {

    if (this.events[name]) {

      for (let callback of this.events[name]) {

        callback(...args);

      }

    }

  }

  prev() {

    let index = this.index - 1;

    if (this.loop && index < 0) {

      index += this.slides.length;

    }

    this.change(index, -1);

  }

  next() {

    let index = this.index + 1;

    if (this.loop && index >= this.slides.length) {

      index -= this.slides.length;

    }

    this.change(index, 1);

  }

  change(nextIndex, direction) {

    if (this.timer) {

      this.play();

    }

    if (!direction) {

      direction = nextIndex < this.index ? 1 : -1;

    }

    if (nextIndex < 0) {

      nextIndex = 0;

    } else if (nextIndex >= this.slides.length) {

      nextIndex = this.slides.length - 1;

    }

    for (let i = 0; i < this.slides.length; i++) {

      if (i - nextIndex === 0) {

        this.trigger("enter", this.slides[i], direction, i + this.offset - this.index);

        if (this.onEnter) {

          this.onEnter(this.slides[i], direction, i + this.offset - this.index);

        }

      } else if (i - this.index === 0) {

        this.trigger("leave", this.slides[i], direction, i + this.offset - nextIndex);

        if (this.onLeave) {

          this.onLeave(this.slides[i], direction, i + this.offset - nextIndex);

        }

      }

      if (this.onSlide) {

        this.onSlide(this.slides[i], direction, i - nextIndex, i);

      }

    }

    this.trigger("change", nextIndex, this.index, direction);

    if (this.onChange) {

      this.onChange(nextIndex, this.index, direction);

    }

    this.index = nextIndex;
    this.offset = 0;

  }

  shift(offset) {

    this.offset = offset;

    if (this.timer) {

      this.play();

    }

    for (let i = 0; i < this.slides.length; i++) {

      let slideIndex = i + this.offset - this.index;

      if (this.loop) {

        slideIndex = ((slideIndex + this.slides.length + 1) % this.slides.length) - 1;

      }


      // if (slideIndex >= -1 && slideIndex <= 1) {

        this.trigger("shift", this.slides[i], slideIndex);

        if (this.onShift) {

          this.onShift(this.slides[i], slideIndex);

        }

      // }

    }

  }

  cancel() {

    this.change(this.index, this.offset > 0 ? -1 : 1);

  }

  init() {

    for (let i = 0; i < this.slides.length; i++) {

      if (this.onInit) {

        const isCurrent = i === this.index;
        const relativeIndex = i - this.index;

        this.onInit(this.slides[i], isCurrent, relativeIndex);

      }

    }

  }

  getIndex() {

    return this.index;

  }

  getCurrent() {

    return this.slides[this.index];

  }


  isPlaying() {
    return Boolean(this.timer);
  }

  play() {

    if (this.timer) {

      clearTimeout(this.timer);

    }



    this.timer = setTimeout(() => {

      this.play();

      this.trigger("play");

      if (this.onPlay) {

        this.onPlay();

      }

    }, this.duration || 4500);

  }

  pause() {

    if (this.timer) {

      clearTimeout(this.timer);

    }

  }

}
