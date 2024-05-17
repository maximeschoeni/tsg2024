addEventListener("popstate", (event) => {
  const state = history.state || {};
  const agenda = new TSG.Archives(state.agenda, state.saisonIndex || 0);
  agenda.render();
});


TSG.Archives = class extends TSG.Agenda {

  static async open(index) {

    history.pushState({agenda: "archives", saisonIndex: index}, "");

    dispatchEvent(new Event("popstate"));

  }

  constructor(mode, saisonIndex) {

    super(mode || "calendar");

    this.currentSaison = saisonIndex || 0;

  }

  async render() {

    if (this.mode === "archives") {

      // await this.getSaisons();
      await this.queryArchivesSaison(this.currentSaison);

    }

    await abduct(document.getElementById("archives"), this.build());

  }

  async queryArchivesSaison(saisonIndex) {

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda) {

      TSG.agenda = {};

    }

    if (!TSG.agenda.saisons) {

      TSG.agenda.saisons = {};

    }

    // const saisons = await this.getSaisons();
    // const saison = saisons[saisonIndex];
    //
    // if (saison && !TSG.agenda.saisons[saison.id]) {
    //
    //   const response = await fetch(`${TSG.rest_url}tsg/v1/archives-saison/${saison.id}`);
    //
    //   TSG.agenda.saisons[saison.id] = await response.json();
    //
    // }

    // const saisons = await this.getSaisons();
    // const saison = saisons[saisonIndex];

    // const saisonIndex = this.currentSaison || 0;

    if (!TSG.agenda.saisons[saisonIndex]) {

      const saisons = await this.getSaisons();
      const saison = saisons[saisonIndex];
      console.log(saisons, saison, saisonIndex);
      const response = await fetch(`${TSG.rest_url}tsg/v1/archives-saison/${saison.id}`);

      TSG.agenda.saisons[saisonIndex] = await response.json();

    }

  }

  buildHeader() {

    return {
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
                  // this.mode = "calendar";
                  // history.replaceState({agenda: "calendar", year: this.date.getFullYear(), month: this.date.getMonth()}, "");
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
                element.classList.remove("loading");
                element.onchange = event => {
                  element.classList.add("loading");
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

    if (this.mode === "archives") {

      const spectacles = TSG.agenda && TSG.agenda.saisons && TSG.agenda.saisons[this.currentSaison] || []



      yield {
        class: "agenda archives",
        update: element => {
          element.classList.toggle("hidden", this.mode !== "archives");
        },
        children: [
          this.buildHeader(),
          this.buildBody(spectacles)
        ]
      }

    }

  }



}
