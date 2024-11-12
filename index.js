window.onload = async function () {
  await getMarcas();

  const marcaSelect = document.querySelector("#marca");

  marcaSelect.onchange = (e) => getModelos(e.target.value);
};

async function getMarcas() {
  let response = await fetch("controladorPHP.php?acao=getMarcas");

  if (!response.ok) {
    throw new Error(`Erro HTTP: ${response.status}`);
  }

  let data = await response.json();

  const marcaSelect = document.querySelector("#marca");

  for (let i = 0; i < data.length; i++) {
    const option = document.createElement("option");
    option.value = data[i].marca;
    option.textContent = data[i].marca;

    marcaSelect.appendChild(option);
  }
}

async function getModelos(value) {
  if (!value) {
    return;
  }

  const options = {
    method: "POST",
    body: {
      marca: "value",
      acao: "getModelos",
    },
  };

  let response = await fetch("controladorPHP.php", options);

  if (!response.ok) {
    throw new Error(`Erro HTTP: ${response.status}`);
  }

  let data = await response.json();

  const modeloSelect = document.querySelector("#modelo");

  for (let i = 0; i < data.length; i++) {
    const option = document.createElement("option");
    option.value = data[i].modelo;
    option.textContent = data[i].modelo;

    modeloSelect.appendChild(option);
  }
}
