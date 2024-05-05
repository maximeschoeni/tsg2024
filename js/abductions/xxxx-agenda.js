addEventListener("popstate", (event) => {
  const state = history.state || {};
  const agenda = new TSG.Agenda();
  agenda.mode = state.mode || "calendar";
  agenda.date = new Date(state.year || new Date().getFullYear(), state.month || new Date().getMonth());
  agenda.render();

});


TSG.Agenda = class {

  static async open(mode, year, month, permalink) {

    history.pushState({agenda: mode, year: year, month: month}, "", permalink);

    const agenda = new TSG.Agenda();
    agenda.mode = mode;
    agenda.date = new Date(year, month);
    await agenda.render();

    // dispatchEvent(new Event("popstate"));

  }

  constructor() {

    // const state = history.state || {};
    //
    // this.mode = state.mode || "calendar";
    //
    // this.date = new Date(state.year || new Date().getFullYear(), state.month || new Date().getMonth());


  }

  // *render2() {
  //
  //   let requestedShows = this.getRequestedCalendarShows();
  //
  //   while (requestedShows) {
  //
  //     await abduct(document.getElementById("agenda"), this.build());
  //
  //
  //     await requestedShows();
  //
  //   }
  //
  //
  // }



  async render() {

    if (!this.rendering) {

      this.rendering = true;

      await this.getSaisons();

      await this.requestCalendarShows();
      await this.requestAgendaSpectacles();

      await abduct(document.getElementById("agenda"), this.build());

      this.rendering = false;
    }

  }

  async requestCalendarShows() {

    if (this.mode === "calendar") {

      const nextDate = new Date(this.date);

      nextDate.setMonth(nextDate.getMonth() + 1);

      const saisons = await this.getSaisons();

      const requestedSaisons = saisons.filter(saison => new Date(saison.start).getTime() < nextDate.getTime() && new Date(saison.end).getTime() > this.date.getTime());

      for (let saison of requestedSaisons) {

        await this.queryCalendarSaison(saison.id);

      }

    }

  }

  async requestAgendaSpectacles() {

    // const nextMonth = new Date(this.date);
    //
    // nextMonth.setMonth(nextDate.getMonth() + 1);
    //
    // const saisons = await this.getSaisons();
    //
    // const requestedSaisons = saisons.filter(saison => new Date(saison.start).getTime() < nextMonth.getTime() && new Date(saison.end).getTime() > this.date.getTime());
    //
    // for (let saison of requestedSaisons) {
    //
    //   await this.queryAgendaSaison(saison.id);
    //
    // }

    // await this.queryAgendaFuture();

    if (this.mode === "list") {

      if (this.currentSaison) {

        await this.queryAgendaSaison(this.currentSaison);

      } else {

        await this.queryAgendaFuture();

      }

    }

  }

  requestSaisons() {

    this.requests.saisons = true;

  }



  async getSaisons() {

    if (!TSG.saisons) {

      const response = await fetch(`${TSG.rest_url}tsg/v1/saisons`);

      TSG.saisons = await response.json();

    }

    return TSG.saisons;
  }

  async queryCalendarSaison(saisonId) {

    if (!TSG.calendar) {

      TSG.calendar = {};

    }

    if (!TSG.calendar.shows) {

      TSG.calendar.shows = {};

    }

    if (!TSG.calendar.saisons) {

      TSG.calendar.saisons = {};

    }

    if (!TSG.calendar.saisons[saisonId]) {

      TSG.calendar.saisons[saisonId] = true;

      const response = await fetch(`${TSG.rest_url}tsg/v1/calendar-shows/${saisonId}`);

      TSG.calendar.shows = {...TSG.calendar.shows, ...await response.json()};

    }

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

  async queryAgendaSaison(saisonId) {

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda.saisons) {

      TSG.agenda.saisons = {};

    }

    if (!TSG.agenda.saisons[saisonId]) {

      const response = await fetch(`${TSG.rest_url}tsg/v1/agenda-saison/${saisonId}`);

      TSG.agenda.saisons[saisonId] = await response.json();

    }

  }

  *getMonthDays() {

		const lastDayPrevMonth = new Date(this.date.getFullYear(), this.date.getMonth(), 0);
		const firstDayNextMonth = new Date(this.date.getFullYear(), this.date.getMonth() + 1, 1);
		const date = new Date(this.date.getFullYear(), this.date.getMonth(), 1 - lastDayPrevMonth.getDay());
		const today = (new Date()).setHours(0, 0, 0, 0);

		while((date.getTime() < firstDayNextMonth.getTime()) || date.getDay() !== 1) {
			const day = new Date(date.getFullYear(), date.getMonth(), date.getDate());
      const time = day.getTime();

			yield {
				date: day,
        time: time,
        isFirstDay: time === this.date.getTime(),
				isDayBefore: time == lastDayPrevMonth.getTime(),
				isDayAfter: time == firstDayNextMonth.getTime(),
				isOffset: time <= lastDayPrevMonth.getTime() || time >= firstDayNextMonth.getTime(),
				isToday: time === today,
				isWeekend: time === 0 || time === 6
			};

			date.setDate(date.getDate() + 1);
		}

	}

  *buildMedia(media) {

    if (media && media.src && media.mimetype.startsWith("image")) {

      yield {
        tag: "figure",
        update: element => {
          element.classList.remove("hidden");
        },
        child: {
          tag: "img",
          update: element => {
            if (element.getAttribute("data-src") !== media.src) {
              element.setAttribute("data-src", media.src);
              element.src = media.src;
              element.width = media.width;
              element.height = media.height;
              if (media.sizes) {
                element.srcset = media.sizes.map(size => `${size.src} ${size.width}w`).join(",");
                element.sizes = "14vw";
              }
              element.draggable = false;
            }
          }
        }
      };

    }  else {

      yield {
        tag: "figure",
        children: [],
        update: element => {
          element.classList.add("hidden");
        }
      };

    }

    if (media && media.src && media.mimetype.startsWith("video")) {

      yield {
        tag: "figure",
        update: element => {
          element.classList.remove("hidden");
        },
        child: {
          tag: "video",
          update: element => {
            if (element.getAttribute("data-src") !== media.src) {
              element.setAttribute("data-src", media.src);
              element.src = media.src;
              element.width = media.width;
              element.height = media.height;
              element.draggable = false;
            }
          }
        }
      };

    } else {

      yield {
        tag: "figure",
        update: element => {
          element.classList.add("hidden");
        },
        children: []
      };

    }

  }

  *buildCalendar() {

    if (this.mode === "calendar") {

      yield {
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
                  element.innerHTML = "<";
                },
                update: element => {
                  element.classList.remove("loading");
                  element.onclick = async event => {
                    element.classList.add("loading");
                    this.date.setMonth(this.date.getMonth()-1);
                    history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
                    // await this.requestCalendarShows();
                    await this.render();
                  }
                }
              },
              {
                tag: "button",
                init: element => {
                  element.innerHTML = "Aujourd’hui";
                },
                update: element => {
                  element.onclick = event => {
                  }
                }
              },
              {
                tag: "button",
                init: element => {
                  element.innerHTML = ">";
                },
                update: element => {
                  element.classList.remove("loading");
                  element.onclick = async event => {
                    element.classList.add("loading");
                    this.date.setMonth(this.date.getMonth()+1);
                    history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
                    // await this.requestCalendarShows();
                    await this.render();
                  }
                }
              },
              {
                tag: "button",
                init: element => {
                  element.innerHTML = "Liste";
                },
                update: element => {
                  element.classList.remove("loading");
                  element.onclick = async event => {
                    element.classList.add("loading");
                    this.mode = "list";
                    this.currentSaison = undefined;
                    history.replaceState({agenda: "list"}, "");
                    // await this.requestAgendaSpectacles();
                    await this.render();
                  }
                }
              },
              {
                tag: "button",
                init: element => {
                  element.innerHTML = "Q";
                },
                update: element => {
                  // search
                }
              }
            ]
          },
          {
            class: "placeholder"
          }
        ]
      };

      yield {
        class: "calendar-body",
        children: [
          {
            class: "calendar-cells-header",
            children: ["lun.", "mar.", "mer.", "jeu.", "ven.", "sam.", "dim."].map(day => {
              return {
                class: "calendar-header-cell",
                init: element => {
                  element.innerHTML = day;
                }
              }
            })
          },
          {
            class: "calendar-cells-body",
            children: [...this.getMonthDays()].map(day => {

              const dateString = day.date.toLocaleString("lt-LT").slice(0, 10); // -> yyyy-mm-dd
              const items = TSG.calendar.shows[dateString] || [];

              return {
                class: "calendar-cell",
                update: element => {
                  element.classList.toggle("today", day.isToday);
                  element.classList.toggle("offset", day.isOffset);
                  element.classList.toggle("count-1", items.length === 1);
                  element.classList.toggle("count-2", items.length === 2);
                  element.classList.toggle("count-4", items.length > 2);
                },
                children: [
                  {
                    class: "day",
                    update: element => {
                      element.innerHTML = day.isFirstDay ? day.date.toLocaleString("fr-CH", {
                        month: "short",
                        day: "numeric"
                      }) : day.date.toLocaleString("fr-CH", {
                        day: "numeric"
                      });
                    }
                  },
                  {
                    class: "shows",
                    children: items.map(item => {
                      return {
                        tag: "a",
                        class: "show",
                        update: element => {
                          element.href = item.permalink || "";
                        },
                        children: [
                          {
                            class: "media",
                            children: this.buildMedia(item.image)
                          },
                          {
                            tag: "h3",
                            class: "title",
                            update: element => {
                              element.innerHTML = item.title;
                            }
                          }
                        ]
                      }
                    })
                  }
                ]
              }
            })
          }
        ]
      };

    }

  }

  *buildAgendaHeader() {

    yield {
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
                  this.mode = "calendar";
                  history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
                  // await this.requestCalendarShows();
                  await this.render();
                }
              }
            },
            {
              tag: "button",
              init: element => {
                element.innerHTML = "Q";
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

  *buildAgendaBody(spectacles) {

    const now = new Date().toLocaleDateString("lt-LT").slice(0, 10);

    yield {
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
                  },
                  children: this.buildMedia(spectacle.image)
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
                          class: "button",
                          init: element => {
                            element.innerHTML = "Infos";
                          },
                          update: element => {
                            const isActive = Boolean(this.todayFlag && spectacle.start <= now && spectacle.end >= now);
                            element.classList.toggle("active", isActive);
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
                          tag: "button",
                          init: element => {
                            element.innerHTML = "Écoles";
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


  *buildAgenda() {

    if (this.mode === "list") {

      yield* this.buildAgendaHeader();

      // if (this.currentSaison) {
      //
      //   yield* this.buildAgendaBody(TSG.agenda.saisons[this.currentSaison] || []);
      //
      // } else {

        yield* this.buildAgendaBody(TSG.agenda.future || []);

      // }

    }

  }

  *buildArchives() {

    if (this.mode === "archives") {

      yield* this.buildArchivesHeader();

      if (this.currentSaison) {

        yield* this.buildAgendaBody(TSG.agenda.saisons[this.currentSaison] || []);

      }
      // else {
      //
      //   yield* this.buildAgendaBody(TSG.agenda.future || []);
      //
      // }

    }

  }

  *buildArchivesHeader() {

    yield {
      class: "agenda-header",
      children: [
        {
          class: "agenda-title",
          update: element => {
            if (this.currentSaison) {
              const saison = TSG.saisons[this.currentSaison];
              if (saison) {
                element.innerHTML = `${saison.start.slice(0, 4)}-${saison.end.slice(0, 4)}`;
              }
            }
          }
        },
        {
          class: "agenda-nav",
          children: [
            {
              tag: "button",
              init: element => {
                element.innerHTML = "Agenda";
              },
              update: element => {
                element.classList.remove("loading");
                element.onclick = async event => {
                  element.classList.add("loading");
                  this.mode = "calendar";
                  history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
                  await this.render();
                }
              }
            },
            {
              tag: "button",
              init: element => {
                element.innerHTML = "Q";
              },
              update: element => {
                // search
              }
            },
            {
              tag: "select",
              children: TSG.saisons.map((saison, index) => {
                return {
                  tag: "option",
                  init: element => {
                    element.value = index;
                    element.innerHTML = `${saison.start.slice(0, 4)}-${saison.end.slice(0, 4)}`;
                  }
                }
              }),
              update: element => {
                element.value = this.currentSaison || 0;
                element.onchange = event => {
                  this.currentSaison = parseInt(element.value);
                  this.render();
                }
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

  *build() {

    yield {
      class: "agenda calendar",
      update: element => {
        element.classList.toggle("hidden", this.mode !== "calendar");
      },
      children: this.buildCalendar()
    }

    yield {
      class: "agenda list",
      update: element => {
        element.classList.toggle("hidden", this.mode !== "list");
      },
      children: this.buildAgenda()
    }

    yield {
      class: "agenda archives",
      update: element => {
        element.classList.toggle("hidden", this.mode !== "list");
      },
      children: this.buildArchives()
    }

  }



}
