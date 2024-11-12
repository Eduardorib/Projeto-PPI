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
      "../controladorPrivado.php?acao=adicionarAnuncio",
      options
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    console.log(data);

    if (data.error) {
      alert(data.message);
      return;
    }

    if (data.redirect) {
      alert("Anúncio criado com sucesso!");
      window.location.href = data.redirect;
    }
  } catch (e) {
    console.error(e);
    return;
  }
}

window.onload = function () {
  const botaoSubmit = document.querySelector("#botaoCadastro");
  botaoSubmit.onclick = (e) => submeteForm(e);

  const uploader = document.querySelector("#fileInput");
  const error = document.querySelector("#error");

  uploader.addEventListener("change", function () {
    if (uploader.files.length < 3) {
      error.style.display = "block";
    }
  });
};
