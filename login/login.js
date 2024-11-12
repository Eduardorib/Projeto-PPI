async function submeteForm(e) {
  e.preventDefault();

  try {
    let form = document.querySelector("form");

    let formData = new FormData(form);

    const options = {
      method: "POST",
      body: formData,
    };

    let response = await fetch(
      "../controladorPHP.php?acao=loginAnunciante",
      options
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    if (data.error) {
      alert(data.message);
      return;
    }

    if (data.redirect) {
      window.location.href = data.redirect;
    }
  } catch (e) {
    console.error(e);
    return;
  }
}

window.onload = function () {
  const botaoSubmit = document.querySelector("#buttonSubmit");
  botaoSubmit.onclick = (e) => submeteForm(e);
};
