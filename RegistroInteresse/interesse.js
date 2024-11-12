const anuncioContainer = document.querySelector("#anuncioContainer");
const botaoSubmit = document.querySelector("#buttonSubmeter");

const inputNome = document.querySelector("#nome");
const inputMensagem = document.querySelector("#mensagem");
const inputTel = document.querySelector("#telefone");

window.onload = async function () {
  const { id, idAnunciante } = await getAnuncio();

  botaoSubmit.onclick = function (e) {
    registrarInteresse(e, id, idAnunciante);
  };
};

async function getAnuncio() {
  try {
    let response = await fetch("../controladorPHP.php?acao=getAnuncioRedirect");

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    preencheCard(data);

    return {
      id: data.id,
      idAnunciante: data.idAnunciante,
    };
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function preencheCard(veiculo) {
  const carCard = document.querySelector("#anuncioContainer");

  const dados = new FormData();

  dados.append("idAnuncio", veiculo.id);

  const options = {
    method: "POST",
    body: dados,
  };

  let responseFotos = await fetch(
    "../controladorPHP.php?acao=fotosAnuncio",
    options
  );

  let dataFotos = await responseFotos.json();

  carCard.innerHTML = `
          <img src="../images/${dataFotos[0].nomeArqFoto}" alt="carro">

          <div class="cardInfo">
              <div>
                  <span>Descrição:</span>
                  <span>${veiculo.descricao}</span>
              </div>

              <div>
                  <span>Ano:</span>
                  <span>${veiculo.ano}</span>
              </div>

              <div>
                  <span>Marca:</span>
                  <span>${veiculo.marca}</span>
              </div>

              <div>
                  <span>Modelo:</span>
                  <span>${veiculo.modelo}</span>
              </div>
              
              <div>
                  <span>Cidade:</span>
                  <span>${veiculo.cidade}</span>
              </div>
              <div>
                  <span>Valor:</span>
                  <span>R$${veiculo.valor}</span>
              </div>

              
          </div>
          `;
}

async function registrarInteresse(e, id, idAnunciante) {
  e.preventDefault();

  if (inputNome.value.trim() === "") {
    alert("Insira seu nome");
    return;
  }

  if (inputTel.value.trim() === "") {
    alert("Insira o seu telefone");
    return;
  }

  if (inputMensagem.value.trim() === "") {
    alert("Insira uma mensagem");
    return;
  }

  try {
    let form = document.querySelector("form");

    let formData = new FormData(form);

    const hoje = new Date();
    const dia =
      hoje.getDate() + "/" + hoje.getMonth() + "/" + hoje.getFullYear();
    const hora = hoje.getHours();
    const minutos = hoje.getMinutes();

    const dataHora = dia + " - " + hora + ":" + minutos;

    formData.append("dataHora", dataHora);
    formData.append("idAnuncio", id);
    formData.append("idAnunciante", idAnunciante);

    const options = {
      method: "POST",
      body: formData,
    };

    let response = await fetch(
      "../controladorPHP.php?acao=registrarInteresse",
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
      alert("Interesse cadastrado com sucesso!");
      window.location.href = data.redirect;
    }
  } catch (e) {
    console.error(e);
    return;
  }
}
