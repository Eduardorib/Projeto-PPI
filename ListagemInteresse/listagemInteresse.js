const buttonLogout = document.querySelector("#logout");
const containerInteressados = document.querySelector(
  ".anunciosInteresseContainer"
);

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
      "../controladorPrivado.php?acao=getInteressados"
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    let data = await response.json();

    console.log(data);

    preencheContainer(data);
  } catch (error) {
    console.error("Erro: ", error);
  }
}

function preencheContainer(interessados) {
  for (i = 0; i < interessados.length; i++) {
    const interessadoCard = document.createElement("div");
    interessadoCard.classList.add("cardInteresse");

    interessadoCard.innerHTML = `

                <h2 class="anuncioInteresseTitle">Interessado ${i + 1}:</h2>
                <div class="campos">
                    <span>Nome do interessado:</span>
                    <span>${interessados[i].nome}</span>
                </div>


                <div class="campos">
                    <span>Telefone de contato:</span>
                    <span>${interessados[i].telefone}</span>
                </div>


                <div class="campos">
                    <span>Mensagem de interesse:</span>

                    <span>${interessados[i].mensagem}</span>
                </div>
        </div>`;

    containerInteressados.appendChild(interessadoCard);
  }
}
