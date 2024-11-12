<?php

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros

class Foto
{

  static function Create($pdo, $nomeArq, $idAnuncio)
  {
      try {
        $stmt = $pdo->prepare(
          <<<SQL
          INSERT INTO foto (idAnuncio, nomeArqFoto)
          VALUES (?, ?)
          SQL
        );      

        $stmt->execute([$idAnuncio, $nomeArq]);
    
        return $pdo->lastInsertId();

      } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit;
      }
  }

  static function GetAll($pdo, $idAnuncio)
  {
      try {
        $stmt = $pdo->prepare(
          <<<SQL
          SELECT nomeArqFoto
          FROM foto
          WHERE idAnuncio = ?
          SQL
        );      

        $stmt->execute([$idAnuncio]);

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    
        return $result;
      } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit;
      }
  }
}