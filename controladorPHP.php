<?php
error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros

require "conexaoSQL.php";
require "php/anunciante.php";
require "php/anuncios.php";


$acaoPost = $_POST['acao'] ?? "";
$acaoGet = $_GET['acao'] ?? "";

if ($acaoPost != "") {
  $acao = $acaoPost;
} else if ($acaoGet != "") {
  $acao = $acaoGet;
} else {
  $acao = "";
}

$pdo = mysqlConnect();

switch ($acao) {
  case "adicionarAnunciante":
    //--------------------------------------------------------------------------------------    
    $nome = $_POST["nome"] ?? "";
    $cpf = $_POST["cpf"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $telefone = $_POST["telefone"] ?? "";

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
      Anunciante::Create($pdo, $nome, $cpf, $email, $senhaHash, $telefone);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['redirect' => '../login/login.html']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "loginAnunciante":
    //--------------------------------------------------------------------------------------   
    $email = $_POST['email'] ?? "";
    $senha = $_POST['senha'] ?? "";

    try {
      $id = Anunciante::Login($pdo, $email, $senha);

      if($id) {
        $cookieParams = session_get_cookie_params();
        $cookieParams['httponly'] = true;
        session_set_cookie_params($cookieParams);

        session_start();
        $_SESSION['loggedIn'] = true;
        $_SESSION['user'] = $email;
        $_SESSION['id'] = $id;
        // $_SESSION['nome'] = Anunciante::GetNomeByEmail($pdo, $email);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['redirect' => '../paginaInterna/paginaInterna.html']);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "getMarcas":
    //--------------------------------------------------------------------------------------   
    try {
      $marcas = Anuncios::GetMarcas($pdo);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($marcas);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;
      
    
  case "getModelos":
    //--------------------------------------------------------------------------------------   
    try {
      $modelos = Anuncios::GetModelos($pdo, $marca);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($modelos);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "getLocalidades":
    //--------------------------------------------------------------------------------------   
    break;

  default:
    exit("Ação não disponível");
}
