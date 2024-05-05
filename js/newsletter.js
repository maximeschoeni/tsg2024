function registerNewsletterForm(form) {

  var inputEmail = form.querySelector("input");
  var button = form.querySelector("button");
  var thanks = form.querySelector(".newsletter-thanx");
  var error = form.querySelector(".newsletter-error");
  var content = form.querySelector(".newsletter-columns");

  button.addEventListener("click", function(event) {
    event.preventDefault();
    if (inputEmail.value) {
      error.textContent = "";
      error.style.display = "none";
      button.disabled = true;

      fetch(`${Laribot.rest_url}mailchimp/v1/register`, {
        method: "post",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
          email: inputEmail.value
        }),
      }).then(function(result) {
        return result.json();
      }).then(function(results) {
        if (results.success) {
          thanks.style.display = "block";
          content.style.display = "none";
        } else {
          button.disabled = false;
          error.textContent = results.error || "Une error s’est produite. L’inscription n’a pas pu être enregistrée";
          error.style.display = "block";
        }
      });
    } else {
      error.textContent = "Veuillez remplir tous les champs SVP";
      error.style.display = "block";
    }
  });


}



window.addEventListener("DOMContentLoaded", () => {

  const forms = document.querySelectorAll("form.newsletter-form");

  for (let form of forms) {

    registerNewsletterForm(form);

  }
});
