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
    try {
      $stmt = $pdo->prepare(
        <<<SQL
        SELECT DISTINCT cidade
        FROM anuncio
        WHERE marca = ? AND modelo = ?
        SQL
      );

      $stmt->execute([$marca, $modelo]);
  
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
  
      return $result;

    } catch (Exception $e) {
      echo json_encode(['error' => true, 'message' => $e->getMessage()]);
      exit;
    }
  }

  static function GetVeiculos($pdo, $marca, $modelo, $localidade)
  {
    try {
      $sql = "SELECT marca, ano, modelo, descricao, cidade, valor, idAnunciante FROM anuncio";
        
      $params = [];
      $whereConditions = [];

      if (!empty($marca)) {
          $whereConditions[] = "marca = ?";
          $params[] = $marca;
      }

      if (!empty($modelo)) {
          $whereConditions[] = "modelo = ?";
          $params[] = $modelo;
      }

      if (!empty($localidade)) {
          $whereConditions[] = "cidade = ?";
          $params[] = $localidade;
      }

      if (!empty($whereConditions)) {
          $sql .= " WHERE ";
        
          for($i = 0; $i < count($whereConditions); $i++) {
            if ($i > 0) {
              $sql .= " AND ";
            }

            $sql .= $whereConditions[$i];
          } 
      }

      $sql .= " LIMIT 20";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
  
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
  
      return $result;

    } catch (Exception $e) {
      echo json_encode(['error' => true, 'message' => $e->getMessage()]);
      exit;
    }
  }
}
