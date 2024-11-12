<?php

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros


class Anuncios
{
  static function GetMarcas($pdo)
  {
    try {
    $stmt = $pdo->query(
      <<<SQL
      SELECT DISTINCT marca
      FROM anuncio
      LIMIT 30
      SQL
    );

    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $result;

    } catch (Exception $e) {
      echo json_encode(['error' => true, 'message' => $e->getMessage()]);
      exit;
    }
  }

  static function GetModelos($pdo, $marca)
  {
    try {
      $stmt = $pdo->prepare(
        <<<SQL
        SELECT DISTINCT modelo
        FROM anuncio
        WHERE marca = ?
        LIMIT 30
        SQL
      );

      $stmt->execute([$marca]);
  
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
  
      return $result;

      } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit;
      }
  }

  static function GetLocalidades($pdo, $marca, $modelo)
  {

  }
}
