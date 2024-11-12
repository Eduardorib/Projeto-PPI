async function checaSessao() {
  try {
    let response = await fetch("../controladorPHP.php?acao=verificaLogin");

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();

    if (!data.loggedIn) {
      window.location.href = "../index.html";
    }
  } catch (error) {
    console.error("Erro: ", error);
  }
}

async function logout() {
  try {
    let response = await fetch("../controladorPrivado.php?acao=logout");

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();

    if (data.redirect) {
      window.location.href = data.redirect;
    }
  } catch (error) {
    console.error("Erro: ", error);
  }
}
