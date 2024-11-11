<?php

function mysqlConnect()
{
  $db_host = "localhost";
  $db_username = "mysqllocal";
  $db_password = "localpasswd";
  $db_name = "ObsidianMotors";

  /*$db_host = "sql209.infinityfree.com";
  $db_username = "if0_37061487";
  $db_password = "BnN442uvQSLeZG";
  $db_name = "if0_37061487_ppi";*/

  $options = [
    PDO::ATTR_EMULATE_PREPARES => false, // desativa a execução emulada de prepared statements
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ];

  try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password, $options);
    return $pdo;
  } 
  catch (Exception $e) {
    exit('Ocorreu uma falha na conexão com o MySQL: ' . $e->getMessage());
  }
}
?>