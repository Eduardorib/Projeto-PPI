<?php

require "./conexaoMysql.php";
require "../php/anunciante.php";

$acao = $_GET['acao'];

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
      header("location: login.html");
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

    
  case "excluirAnunciante":
    //--------------------------------------------------------------------------------------
    $idAnunciante = $_GET["idAnunciante"] ?? "";
    try {
      Anunciante::Remove($pdo, $idAnunciante);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "listarAnunciantes":
    //--------------------------------------------------------------------------------------
    try {
      $arrayAnunciantes = Anunciante::GetFirst30($pdo);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($arrayAnunciantes);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

    //-----------------------------------------------------------------
  default:
    exit("Ação não disponível");
}
