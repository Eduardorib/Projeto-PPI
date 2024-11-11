<?php

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros


class Anunciante
{
  static function Create($pdo, $nome, $cpf, $email, $senhaHash, $telefone)
  {
    try {
    $stmtCpf = $pdo->prepare("SELECT COUNT(*) FROM anunciante WHERE cpf = ?");
    $stmtCpf->execute([$cpf]);
    if ($stmtCpf->fetchColumn() > 0) {
        throw new Exception("O CPF já está em uso.");
    }

    $stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM anunciante WHERE email = ?");
    $stmtEmail->execute([$email]);
    if ($stmtEmail->fetchColumn() > 0) {
        throw new Exception("O email já está em uso.");
    }

    $stmt = $pdo->prepare(
      <<<SQL
      INSERT INTO anunciante (nome, cpf, email, senhaHash, telefone)
      VALUES (?, ?, ?, ?, ?)
      SQL
    );

    $stmt->execute([$nome, $cpf, $email, $senhaHash, $telefone]);

    return $pdo->lastInsertId();

    } catch (Exception $e) {
      echo json_encode(['error' => true, 'message' => $e->getMessage()]);
      exit;
    }
  }

  static function Login($pdo, $email, $senha)
  {
    try {
        $stmt = $pdo->prepare(
          <<<SQL
          SELECT senhaHash, id
          FROM anunciante
          WHERE email = ?
          SQL
        );

        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$result) {
          throw new Exception("E-mail não encontrado.");
        }

        if (!password_verify($senha, $result->senhaHash)) {
          throw new Exception("Senha incorreta inserida.");
        }
          
        return $result->id;
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        exit;
    }
  }
}
