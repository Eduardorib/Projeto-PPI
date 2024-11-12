<?php

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros


class Anuncios
{
  
   
  static function Create($pdo, $modelo, $ano, $cor, $quilometragem, $descricao,$valor,$estado,$cidade)
  {
    $id = $_SESSION['id'];
    $fotos = $_FILES['fileinput'];

try{
    $pdo->beginTransaction();

    $dataHora = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare(
      <<<SQL
      INSERT INTO anuncio (marca, modelo, ano, cor, quilometragem, descricao, valor, dataHora, estado, cidade, idAnunciante)
      VALUES (?, ?, ?, ?, ?,?,?,?,?,?,?)
      SQL
    );

    $stmt->execute([$modelo, $ano, $cor, $quilometragem, $descricao,$valor,$dataHora,$estado,$cidade,$id]);

    $idAnuncio = $pdo->lastInsertId();
    $pastaFotos = '../fotos/';

    if (!is_dir($pastaFotos)) {
        mkdir($pastaFotos, 0777, true);
    }
    $stmtFoto = $pdo->prepare("INSERT INTO foto (nomeArqFoto, idAnuncio) VALUES (:nomeArqFoto, :idAnuncio)");

    
    foreach ($fotos['tmp_name'] as $index => $tmpName) {
       
        if ($fotos['error'][$index] == UPLOAD_ERR_OK) {
           
            $extensao = pathinfo($fotos['name'][$index], PATHINFO_EXTENSION);

            
            $nomeArqFoto = uniqid() . '.' . $extensao;

           
            if (move_uploaded_file($tmpName, $pastaFotos . $nomeArqFoto)) {
                
                $stmtFoto->execute([
                    ':nomeArqFoto' => $nomeArqFoto,
                    ':idAnuncio' => $idAnuncio
                ]);
            }
          }
        }

    $pdo->commit();
    return $pdo->lastInsertId();

    }
     catch (Exception $e) {
      $pdo->rollBack();
      echo json_encode(['error' => true, 'message' => $e->getMessage()]);
      exit;
    }
  }

  
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