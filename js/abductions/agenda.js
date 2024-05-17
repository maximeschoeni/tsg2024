addEventListener("popstate", async event => {
  const state = history.state || {};
  const agenda = new TSG.Agenda(state.agenda || "calendar");
  await agenda.render();

  // scrollTo({top: document.getElementById("agenda").offsetTop});

});


TSG.Agenda = class extends TSG.Calendar {

  static async open() {

    history.pushState({agenda: "agenda"}, "");

    dispatchEvent(new Event("popstate"));

  }

  static async preload() {

    const agenda = new TSG.Agenda();

    await agenda.queryAgendaFuture();

  }

  constructor(mode) {

    super(mode || "calendar");

  }

  async render() {

    if (this.mode === "agenda") {

      await this.queryAgendaFuture();

    }

    await abduct(document.getElementById("agenda"), this.build());

  }

  async queryAgendaFuture() {

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda.future) {

      const response = await fetch(`${TSG.rest_url}tsg/v1/future-spectacles`);

      TSG.agenda.future = await response.json();

    }

  }

  buildHeader() {

    return {
      class: "agenda-header",
      children: [
        {
          class: "agenda-title",
          update: element => {
            element.innerHTML = this.date.toLocaleDateString("fr-CH", {
              year: "numeric",
              month: "long"
            })
          }
        },
        {
          class: "agenda-nav",
          children: [
            {
              tag: "button",
              init: element => {
                element.innerHTML = "Aujourd’hui";
              },
              update: element => {
                element.classList.toggle("active", Boolean(this.todayFlag));
                element.onclick = async event => {
                  this.todayFlag = !this.todayFlag;
                  await this.render();
                  if (this.todayFlag) {
                    const activeRow = document.querySelector(".spectacle-row.active");
                    if (activeRow) {
                      const box = activeRow.getBoundingClientRect();
                      scrollTo({left: 0, top: box.top + scrollY, behavior: "smooth"});
                    }
                    setTimeout(() => {
                      this.todayFlag = false;
                      this.render();
                    }, 5000);
                  }
                }
              }
            },
            {
              tag: "button",
              init: element => {
                element.innerHTML = "Agenda";
              },
              update: element => {
                element.classList.remove("loading");
                element.onclick = async event => {
                  element.classList.add("loading");
                  // this.mode = "calendar";
                  // history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
                  // // await this.requestCalendarShows();
                  // await this.render();
                  TSG.Calendar.open();
                }
              }
            },
            {
              tag: "button",
              init: element => {
                element.innerHTML = "🔎";
              },
              update: element => {
                // search
              }
            }
          ]
        },
        {
          class: "placeholder",
        }
      ]
    };

  }

  buildBody(spectacles) {

    const now = new Date().toLocaleDateString("lt-LT").slice(0, 10);

    return {
      class: "list-body",
      children: [
        {
          class: "not-today",
          update: element => {
            const isActive = this.todayFlag && !spectacles.some(spectacle => spectacle.start <= now && spectacle.end >= now);
            element.classList.toggle("hidden", !isActive);
            if (isActive) {
              const next = spectacles.find(spectacle => spectacle.start <= now && spectacle.end >= now);
              const weekday = new Date(next.start).toLocaleDateString("fr-CH", {weekday: "long"});
              element.innerHTML = `Prochain spectacle ${weekday}`;
            }
          }
        },
        {
          class: "spectacles-container",
          children: spectacles.map(spectacle => {
            return {
              class: "spectacle-row",
              update: element => {
                const isActive = Boolean(this.todayFlag && spectacle.start <= now && spectacle.end >= now);
                element.classList.toggle("active", isActive);
              },
              children: [
                {
                  tag: "a",
                  class: "media",
                  update: element => {
                    element.href = spectacle.permalink;
                    // const isActive = Boolean(this.todayFlag && spectacle.start <= now && spectacle.end >= now);
                    // element.classList.toggle("active", isActive);
                  },
                  children: [
                    {
                      class: "overlay",
                      update: element => {
                        if (spectacle.overlay) {
                          element.style.backgroundImage = `url(${spectacle.overlay})`;
                        } else {
                          element.style.backgroundImage = "none";
                        }
                      }
                    },
                    ...this.buildMedia(spectacle.image)
                  ]
                },
                {
                  class: "text",
                  children: [
                    {
                      class: "spectacle-main",
                      children: [
                        {
                          class: "title",
                          update: element => {
                            element.innerHTML = spectacle.title;
                          }
                        },
                        {
                          class: "subtitle",
                          update: element => {
                            element.innerHTML = spectacle.subtitle;
                          }
                        },
                        {
                          class: "description",
                          update: element => {
                            element.innerHTML = spectacle.description;
                          }
                        },
                        {
                          class: "dates",
                          update: element => {
                            element.innerHTML = spectacle.date;
                          }
                        }
                      ]
                    },
                    {
                      class: "spectacle-nav",
                      children: [
                        {
                          tag: "a",
                          class: "button primary",
                          init: element => {
                            element.innerHTML = "Infos";
                          },
                          update: element => {
                            element.href = spectacle.permalink;
                          }
                        },
                        {
                          tag: "button",
                          init: element => {
                            element.innerHTML = "Billets";
                          }
                        },
                        {
                          tag: "a",
                          class: "button",
                          init: element => {
                            element.innerHTML = "Écoles";
                          },
                          update: element => {
                            element.classList.toggle("hidden", !spectacle.mediations || spectacle.mediations.length === 0);
                            if (spectacle.mediations && spectacle.mediations.length) {
                              element.href = spectacle.mediations[0].url;
                            }
                          }
                        }
                      ]
                    }
                  ]
                }
              ]
            };
          })
        }
      ]

    };

  }

  *build() {

    if (this.mode === "agenda") {

      const spectacles = TSG.agenda && TSG.agenda.future || [];

      yield {
        class: "agenda list",
        update: element => {
          element.classList.toggle("active", this.mode === "agenda");
          element.classList.toggle("hidden", this.mode !== "agenda");
        },
        children: [
          this.buildHeader(),
          this.buildBody(spectacles)
        ]
      }

    }

  }


}
