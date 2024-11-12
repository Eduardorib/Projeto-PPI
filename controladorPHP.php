<?php
error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros

require "conexaoSQL.php";
require "php/anunciante.php";
require "php/anuncios.php";
require "php/interesse.php";
require "php/fotos.php";


$acao = $_GET['acao'] ?? "";

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
        $marca = $_POST['marca'] ?? '';
  
        $modelos = Anuncios::GetModelos($pdo, $marca);
  
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($modelos);
      } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        throw new Exception($e->getMessage());
      }
      break;

    case "getLocalidades":
      //--------------------------------------------------------------------------------------   
      try {
        $marca = $_POST['marca'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
  
        $localidades = Anuncios::GetLocalidades($pdo, $marca, $modelo);
  
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($localidades);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
      break;

    case "getVeiculos":
      //--------------------------------------------------------------------------------------   
      try {
        $marca = $_POST['marca'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
        $localidade = $_POST['localidade'] ?? '';
  
        $veiculos = Anuncios::GetVeiculos($pdo, $marca, $modelo, $localidade);
  
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($veiculos);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
      break;

    case "redirecionarInteresse":
      //--------------------------------------------------------------------------------------   
      try {
        $idAnunciante = $_POST['idAnunciante'] ?? '';
        $idAnuncio = $_POST['idAnuncio'] ?? '';

        session_start();
        $_SESSION['idAnunciante'] = $idAnunciante;
        $_SESSION['idAnuncio'] = $idAnuncio;

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['redirect' => 'RegistroInteresse/interesse.html']);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
      break;

    case "getAnuncioRedirect":
      //--------------------------------------------------------------------------------------   
      try {
        session_start();
        $idAnunciante = $_SESSION['idAnunciante'];
        $idAnuncio = $_SESSION['idAnuncio'];

        $veiculo = Anuncios::GetVeiculo($pdo, $idAnunciante, $idAnuncio);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($veiculo);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
      break;

    case "registrarInteresse":
      //--------------------------------------------------------------------------------------
      try {
        $nome = $_POST["nome"] ?? "";
        $telefone = $_POST["telefone"] ?? "";
        $mensagem = $_POST["mensagem"] ?? "";
        $dataHora = $_POST["dataHora"] ?? "";
        $idAnuncio = $_POST["idAnuncio"] ?? "";

        Interesse::PostInteresse($pdo, $idAnuncio, $telefone, $mensagem, $dataHora, $nome);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['redirect' => '../index.html']);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }  
    break;
    case "fotosAnuncio":
      //--------------------------------------------------------------------------------------    
      $idAnuncio = $_POST['idAnuncio'];
  
      try {
          $fotos = Foto::GetAll($pdo, $idAnuncio);
  
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($fotos);
        } catch (Exception $e) {
          throw new Exception($e->getMessage());
        }
    break;
    case "verificaLogin":
      //--------------------------------------------------------------------------------------
      try {
        session_start();

        header('Content-Type: application/json; charset=utf-8');

        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
          echo json_encode(['loggedIn' => true]);
        } else {
          echo json_encode(['loggedIn' => false]);
        }
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }  
    break;
    
    default:
    break;
  }



