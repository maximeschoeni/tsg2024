
window.Accordeon = window.AccordeonItem = class {

  static register(element, itemSelector, handleSelector, bodySelector, closeSelector) {

    const items = element.querySelectorAll(itemSelector);

    for (let item of items) {

      new this(item, handleSelector, bodySelector, closeSelector);

    }

  }

  constructor(element, handle, body, close, isOpen, duration) {

    this.element = element || document.body;
    this.duration = duration || 300;
    this.isOpen = isOpen || false;

    if (typeof handle === "string") {

      handle = element.querySelector(handle);

    }

    if (typeof body === "string") {

      body = element.querySelector(body);

    }

    if (typeof close === "string") {

      close = element.querySelector(close);

    }

    if (handle) {

      this.addHandle(handle);

    }

    if (body) {

      this.addBody(body);

    }

    if (close) {

      this.addClose(close);

    }

  }

  addHandle(element) {

    this.handle = element;

    element.onclick = event => {

      event.preventDefault();

      this.toggle();

    }

  }

  addBody(element, content) {

    this.body = element;
    this.content = content || element.children[0];

    if (this.isOpen) {

      element.style.height = `auto`;

    } else {

      element.style.height = `0`;
      element.style.overflow = `hidden`;

    }

    element.style.transition = `height ${this.duration}ms`;

    element.ontransitionend = event => {

      if (this.isOpen) {

        element.style.height = "auto";
        element.style.overflow = "visible";

      }

    }

  }

  addClose(element) {

    element.onclick = event => {

      event.preventDefault();
      this.close();

    }

  }

  init() {

    if (this.body) {

      if (!this.isOpen) {

        this.body.style.overflow = "hidden";
        this.body.style.height = "0";

      }

    }

    if (this.element) {

      this.element.classList.toggle("open", this.isOpen);

    }

    if (this.handle) {

      this.handle.classList.toggle("active", this.isOpen);

    }

  }

  update() {

    if (this.body) {

      this.body.style.height = `${this.content.clientHeight}px`;

      if (!this.isOpen) {

        this.body.style.overflow = "hidden";
        this.body.clientHeight; // -> force reflow
        this.body.style.height = "0";

      }

    }

    if (this.element) {

      this.element.classList.toggle("open", this.isOpen);

    }

    if (this.handle) {

      this.handle.classList.toggle("active", this.isOpen);

    }

    if (this.onOpen && this.isOpen) {

      this.onOpen();

    }

    if (this.onClose && !this.isOpen) {

      this.onClose();

    }

  }

  close() {

    this.isOpen = false;
    this.update();

  }

  open() {

    this.isOpen = true;
    this.update();

  }

  toggle() {

    this.isOpen = !this.isOpen;
    this.update();

  }


}

addEventListener("DOMContentLoaded", event => {

  const elements = document.querySelectorAll(".accordeon");

  for (let element of elements) {

    Accordeon.register(element, ".accordeon-item", ".accordeon-handle", ".accordeon-body", ".accordeon-close");

  }

});
