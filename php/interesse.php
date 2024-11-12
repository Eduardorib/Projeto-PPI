<?php

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros

class Interesse
{

  static function PostInteresse($pdo, $idAnuncio, $telefone, $mensagem, $dataHora, $nome)
  {
      try {
        $stmt = $pdo->prepare(
          <<<SQL
          INSERT INTO interesse (nome, telefone, mensagem, dataHora, idAnuncio)
          VALUES (?, ?, ?, ?, ?)
          SQL
        );      

        $stmt->execute([$nome, $telefone, $mensagem, $dataHora, $idAnuncio]);
    
        return $pdo->lastInsertId();

      } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit;
      }
  }

  static function GetInteressados($pdo, $idAnuncio)
  {
    try {
      $stmt = $pdo->prepare(
        <<<SQL
        SELECT nome, telefone, mensagem
        FROM interesse
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