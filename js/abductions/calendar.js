addEventListener("popstate", async (event) => {
  const state = history.state || {};
  const calendrier = new TSG.Calendar(state.agenda, state.year, state.month);
  await calendrier.render();
});


TSG.Calendar = class {

  static async open(year, month) {

    history.pushState({agenda: "calendar", year: year, month: month}, "");

    dispatchEvent(new Event("popstate"));

  }

  constructor(mode, year, month) {

    this.mode = mode;

    if (!year) {

      year = new Date().getFullYear();

    }

    if (month === undefined) {

      month = new Date().getMonth();

    }

    this.date = new Date(year, month);

  }

  async render() {

    if (this.mode === "calendar") {

      const nextDate = new Date(this.date);

      nextDate.setMonth(nextDate.getMonth() + 1);

      const saisons = await this.getSaisons();

      const requestedSaisons = saisons.filter(saison => new Date(saison.start).getTime() < nextDate.getTime() && new Date(saison.end).getTime() > this.date.getTime());

      for (let saison of requestedSaisons) {

        await this.queryCalendarSaison(saison.id);

      }

    }

    await abduct(document.getElementById("calendar"), this.build());

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
                  // this.mode = "list";
                  // this.currentSaison = undefined;
                  // history.replaceState({agenda: "list"}, "");
                  // // await this.requestAgendaSpectacles();
                  // await this.render();

                  TSG.Agenda.open();
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

  }

  buildBody() {

    return {
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

  *build() {

    if (this.mode === "calendar") {

      yield {
        class: "agenda calendar",
        update: element => {
          element.classList.toggle("active", this.mode === "calendar");
          element.classList.toggle("hidden", this.mode !== "calendar");
        },
        children: [
          this.buildHeader(),
          this.buildBody()
        ]
      };

    }

  }



}
