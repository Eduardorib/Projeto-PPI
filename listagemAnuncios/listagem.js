const buttonLogout = document.querySelector("#logout");
const cardsContainer = document.querySelector("#cards");
const buttonConfirmaDelete = document.querySelector("#buttonConfirmaDelete");

cardId = "";

window.onload = async function () {
  await checaSessao();

  buttonLogout.onclick = async function () {
    await logout();
  };

  buttonConfirmaDelete.onclick = async function () {
    await confirmaDelete();
  };

  await getAnuncios();
};

async function getAnuncios() {
  try {
    let response = await fetch("../controladorPrivado.php?acao=exibirAnuncios");

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    preencheCards(data);
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function preencheCards(anuncios) {
  cardsContainer.innerHTML = "";

  for (i = 0; i < anuncios.length; i++) {
    const dados = new FormData();

    dados.append("idAnuncio", anuncios[i].id);

    const options = {
      method: "POST",
      body: dados,
    };

    let responseFotos = await fetch(
      "../controladorPrivado.php?acao=fotosAnuncio",
      options
    );

    let dataFotos = await responseFotos.json();

    const carCard = document.createElement("div");
    carCard.classList.add("anuncio");
    carCard.id = `card${anuncios[i].id}`;

    carCard.innerHTML = `
          <img src="../images/${dataFotos[0].nomeArqFoto}" alt="carro brabo">

          <div class="anuncioInfo">
              <div>
                  <span>Descrição:</span>
                  <span>${anuncios[i].descricao}</span>
              </div>

              <div>
                  <span>Ano:</span>
                  <span>${anuncios[i].ano}</span>
              </div>

              <div>
                  <span>Marca:</span>
                  <span>${anuncios[i].marca}</span>
              </div>

              <div>
                  <span>Modelo:</span>
                  <span>${anuncios[i].modelo}</span>
              </div>
              
              <div>
                  <span>Cidade:</span>
                  <span>${anuncios[i].cidade}</span>
              </div>
              <div>
                  <span>Valor:</span>
                  <span>R$${anuncios[i].valor}</span>
              </div>

              <div>
                  <button class="buttonMain" id="buttonInteresse${i}">Interesses no anúncio</button>
                  <button class="buttonMain" id="buttonVisao${i}">Visualizacão detalhada</button>
                  <button type="button" class="btn btn-primary" id="buttonExcluir${i}" data-bs-toggle="modal"
                  data-bs-target="#exampleModal">
                      Excluir anúncio
                  </button>
              </div>       
          </div>
          `;

    cardsContainer.appendChild(carCard);

    const idAnuncio = anuncios[i].id;

    const visaoDetalhada = document.querySelector(`#buttonVisao${i}`);
    const interessesAnuncio = document.querySelector(`#buttonInteresse${i}`);
    const excluirAnuncio = document.querySelector(`#buttonExcluir${i}`);

    excluirAnuncio.onclick = function () {
      cardId = idAnuncio;
    };

    visaoDetalhada.onclick = function () {
      redirecionarVisao(idAnuncio);
    };

    interessesAnuncio.onclick = function () {
      redirecionarInteresses(idAnuncio);
    };
  }
}

async function redirecionarInteresses(idAnuncio) {
  console.log(idAnuncio);

  const dados = new FormData();
  dados.append("idAnuncio", idAnuncio);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch(
      "../controladorPrivado.php?acao=redirecionarInteresses",
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
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function redirecionarVisao(idAnuncio) {
  const dados = new FormData();
  dados.append("idAnuncio", idAnuncio);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch(
      "../controladorPrivado.php?acao=redirecionarVisao",
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
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function confirmaDelete() {
  if (!cardId) {
    return;
  }

  const cardCard = document.querySelector(`#card${cardId}`);

  const dados = new FormData();
  dados.append("idAnuncio", cardId);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch(
      "../controladorPrivado.php?acao=excluirAnuncio",
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

    cardCard.remove();
    alert(data.message);
  } catch (error) {
    console.error("Erro: ", error);
  }
}
