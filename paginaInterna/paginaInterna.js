const buttonLogout = document.querySelector("#logout");

window.onload = async function () {
  await checaSessao();

  buttonLogout.onclick = async function () {
    await logout();
  };
};
