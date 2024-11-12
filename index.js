const marcaSelect = document.querySelector("#marca");
const modeloSelect = document.querySelector("#modelo");
const localidadesSelect = document.querySelector("#localizacao");

const buttonBusca = document.querySelector("#buttonBusca");

const cardsContainer = document.querySelector(".cardsContainer");

window.onload = async function () {
  await getMarcas();

  marcaSelect.onchange = (e) => getModelos(e.target.value);
  modeloSelect.onchange = (e) =>
    getLocalidades(e.target.value, marcaSelect.value);

  buttonBusca.onclick = (e) => getVeiculos(e);
};

async function getMarcas() {
  try {
    let response = await fetch("controladorPHP.php?acao=getMarcas");

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    for (let i = 0; i < data.length; i++) {
      const option = document.createElement("option");
      option.value = data[i].marca;
      option.textContent = data[i].marca;

      marcaSelect.appendChild(option);
    }
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function getModelos(value) {
  if (!value) {
    modeloSelect.innerHTML = "";

    const option = document.createElement("option");
    option.value = "";
    option.textContent = "Selecione o modelo";

    modeloSelect.appendChild(option);
    return;
  }

  const dados = new FormData();

  dados.append("marca", value);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch("controladorPHP.php?acao=getModelos", options);

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    modeloSelect.innerHTML = "";

    const option = document.createElement("option");
    option.value = "";
    option.textContent = "Selecione o modelo";

    modeloSelect.appendChild(option);

    for (let i = 0; i < data.length; i++) {
      const option = document.createElement("option");
      option.value = data[i].modelo;
      option.textContent = data[i].modelo;

      modeloSelect.appendChild(option);
    }
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function getLocalidades(modelo, marca) {
  if (!marca | !modelo) {
    localidadesSelect.innerHTML = "";

    const option = document.createElement("option");
    option.value = "";
    option.textContent = "Selecione a localização";

    localidadesSelect.appendChild(option);
    return;
  }

  const dados = new FormData();

  dados.append("marca", marca);
  dados.append("modelo", modelo);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch(
      "controladorPHP.php?acao=getLocalidades",
      options
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    localidadesSelect.innerHTML = "";

    const option = document.createElement("option");
    option.value = "";
    option.textContent = "Selecione a localização";

    localidadesSelect.appendChild(option);

    for (let i = 0; i < data.length; i++) {
      const option = document.createElement("option");
      option.value = data[i].cidade;
      option.textContent = data[i].cidade;

      localidadesSelect.appendChild(option);
    }
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function getVeiculos(e) {
  e.preventDefault();

  let marca = marcaSelect.value;
  let modelo = modeloSelect.value;
  let localidade = localidadesSelect.value;

  const dados = new FormData();

  dados.append("marca", marca);
  dados.append("modelo", modelo);
  dados.append("localidade", localidade);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch("controladorPHP.php?acao=getVeiculos", options);

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    preencheCards(data);
  } catch (error) {
    console.error("Erro: ", error);
  }
}

function preencheCards(veiculos) {
  cardsContainer.innerHTML = "";

  for (i = 0; i < veiculos.length; i++) {
    const carCard = document.createElement("div");
    carCard.classList.add("carCard");

    carCard.innerHTML = `
          <img src="images/carro.jpeg" alt="carro brabo">

          <div class="cardInfo">
              <div>
                  <span>Descrição:</span>
                  <span>${veiculos[i].descricao}</span>
              </div>

              <div>
                  <span>Ano:</span>
                  <span>${veiculos[i].ano}</span>
              </div>

              <div>
                  <span>Marca:</span>
                  <span>${veiculos[i].marca}</span>
              </div>

              <div>
                  <span>Modelo:</span>
                  <span>${veiculos[i].modelo}</span>
              </div>
              
              <div>
                  <span>Cidade:</span>
                  <span>${veiculos[i].cidade}</span>
              </div>
              <div>
                  <span>Valor:</span>
                  <span>R$${veiculos[i].valor}</span>
              </div>

              
          </div>
          
          <button class="buttonMain" id="buttonInteresse${veiculos[i].idAnunciante}" >Tenho Interesse</button>
          `;

    cardsContainer.appendChild(carCard);

    const idAnunciante = veiculos[i].idAnunciante;

    const buttonInteresse = document.querySelector(
      `#buttonInteresse${idAnunciante}`
    );

    buttonInteresse.onclick = function (idAnunciante) {
      redirecionarInteresse(idAnunciante);
    };
  }
}

async function redirecionarInteresse(idAnunciante) {
  const dados = new FormData();
  dados.append("idAnunciante", idAnunciante);

  const options = {
    method: "POST",
    body: dados,
  };

  try {
    let response = await fetch(
      "controladorPHP.php?acao=redirecionaInteresse",
      options
    );

    console.log(response);

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
