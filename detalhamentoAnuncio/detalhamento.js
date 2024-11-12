const buttonLogout = document.querySelector("#logout");
const carCard = document.querySelector(".anuncio");

window.onload = async function () {
  await checaSessao();

  buttonLogout.onclick = async function () {
    await logout();
  };

  await getAnuncioDetalhado();
};

async function getAnuncioDetalhado() {
  try {
    let response = await fetch(
      "../controladorPrivado.php?acao=getAnuncioDetalhado"
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    preencheCard(data);
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function preencheCard(anuncio) {
  const dados = new FormData();

  dados.append("idAnuncio", anuncio.id);

  const options = {
    method: "POST",
    body: dados,
  };

  let responseFotos = await fetch(
    "../controladorPrivado.php?acao=fotosAnuncio",
    options
  );

  let dataFotos = await responseFotos.json();

  carCard.innerHTML = `
      <div class="imagesContainer">
        <img src="../images/${dataFotos[0].nomeArqFoto}" alt="foto1">
        <img src="../images/${dataFotos[1].nomeArqFoto}" alt="foto2">
        <img src="../images/${dataFotos[2].nomeArqFoto}" alt="foto3">
      </div>

      <div class="infoContainer">
        <div>
          <span>Descrição:</span>
          <span>${anuncio.descricao}</span>
        </div>

        <div>
          <span>Marca:</span>
          <span>${anuncio.marca}</span>
        </div>

        <div>
          <span>Modelo:</span>
          <span>${anuncio.modelo}</span>
        </div>
        <div>
          <span>Ano:</span>
          <span>${anuncio.ano}</span>
        </div>

        <div>
          <span>Cor:</span>
          <span>
          ${anuncio.cor}
          </span>
        </div>

        <div>
          <span>Quilometragem:</span>
          <span>
          ${anuncio.quilometragem}
          </span>
        </div>

        <div>
          <span>Valor:</span>
          <span>
            R$ ${anuncio.valor}
          </span>
        </div>

        <div>
          <span>Estado:</span>
          <span>
            ${anuncio.estado}
          </span>
        </div>

        <div>
          <span>Cidade:</span>
          <span>
            ${anuncio.cidade}
          </span>
        </div>
      </div>`;
}
