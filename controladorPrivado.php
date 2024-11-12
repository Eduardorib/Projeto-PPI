
<?php

session_start();

if(!isset($_SESSION['loggedIn'])) {
  header('Location: index.html');
  exit();
}

error_reporting(E_ALL); // Relatar todos os tipos de erro
ini_set('display_errors', 1); // Exibir os erros

require "conexaoSQL.php";
require "php/anunciante.php";
require "php/anuncios.php";
require "php/interesse.php";
require "php/fotos.php";

$acao = $_GET['acao'] ?? "";

$pdo = mysqlConnect();

function validaFoto($arquivoImagem)
{
   if (!is_uploaded_file($arquivoImagem))
      throw new InvalidArgumentException("Falha ao carregar o arquivo de imagem");

   // Resgata e verifica o tamanho da imagem
   list($width, $height, $type) = getimagesize($arquivoImagem);
   if (empty($width) || empty($height))
      throw new InvalidArgumentException("O arquivo informado não corresponde a uma imagem válida");

   // Verifica o formato do arquivo de imagem
   $imageType = image_type_to_mime_type($type);
   if ($imageType != "image/jpeg" && $imageType != "image/png")
      throw new InvalidArgumentException("A foto deve estar no formato JPEG ou PNG");

   // Verifica o tamanho do arquivo de imagem
   if (filesize($arquivoImagem) > 5*1024*1024)
      throw new InvalidArgumentException("A foto não deve ultrapassar 5MB");

   return $imageType;
}

switch ($acao) {
  case "adicionarAnuncio":
    //--------------------------------------------------------------------------------------    
    $marca = $_POST["marca"] ?? "";
    $modelo = $_POST["modelo"] ?? "";
    $ano = $_POST["ano"] ?? "";
    $cor = $_POST["cor"] ?? "";
    $quilometragem = $_POST["quilometragem"] ?? "";
    $descricao = $_POST["descricao"] ?? "";
    $valor = $_POST["valor"] ?? "";
    $estado = $_POST["estado"] ?? "";
    $cidade = $_POST['cidade']?? "";

    $id = $_SESSION['id'] ?? "";
    $dataHora = date('Ymd_His', time());

    try {
        $idAnuncio = Anuncios::Create($pdo, $marca, $modelo, $ano, $cor, $quilometragem, $descricao,$valor, $dataHora, $estado, $cidade, $id);

        $pasta = 'images';
        $fotos = $_FILES['fileInput'];
        
        for ($i = 0; $i < count($fotos['tmp_name']); $i++) {
          $tipoArquivoImagem = validaFoto($fotos['tmp_name'][$i]);
          $microtime = microtime(true);
          $extensao = substr($tipoArquivoImagem, 6);
          $destinoArquivo = "$pasta/{$dataHora}-{$microtime}.{$extensao}";
          $nomeNovo = "{$dataHora}-{$microtime}.{$extensao}";

          if (move_uploaded_file($fotos['tmp_name'][$i], $destinoArquivo)) {
              Foto::Create($pdo, $nomeNovo, $idAnuncio);
          } else {
              throw new Exception("Falha no upload da imagem {$fotos['tmp_name'][$i]}");
          }
        }
  
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['redirect' => '../listagemAnuncios/listagem.html']);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
  break;

  case "exibirAnuncios":
    //--------------------------------------------------------------------------------------    
    $idAnunciante = $_SESSION['id']; 

    try {
        $anuncios = Anuncios::GetVeiculosById($pdo, $idAnunciante);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($anuncios);
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

  case "excluirAnuncio":
    //--------------------------------------------------------------------------------------
    $idAnuncio = $_POST['idAnuncio']; 
    $pasta = 'images';

    try {
      $fotos = Foto::GetAll($pdo, $idAnuncio);

      for ($i = 0; $i < count($fotos); $i++) {
        $diretorioFoto = $pasta . $fotos[$i]->nomeArqFoto;
        if (file_exists($diretorioFoto)) {
          unlink($diretorioFoto);
        }
      }

      Anuncios::DeleteAnuncio($pdo, $idAnuncio);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['message' => 'Anúncio excluído com sucesso']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  break;

  case "redirecionarInteresses": 
    $idAnuncio = $_POST['idAnuncio']; 

    try {
      $_SESSION['idAnuncio'] = $idAnuncio;

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['redirect' => '../ListagemInteresse/listagemInteresse.html']);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
  break;

  case "redirecionarVisao": 
    $idAnuncio = $_POST['idAnuncio']; 

    try {
      $_SESSION['idAnuncio'] = $idAnuncio;

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['redirect' => '../detalhamentoAnuncio/detalhamento.html']);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
  break;

  case "getAnuncioDetalhado":
    //--------------------------------------------------------------------------------------   
    try {
      $idAnunciante = $_SESSION['id'];
      $idAnuncio = $_SESSION['idAnuncio'];

      $veiculo = Anuncios::GetVeiculoDetalhado($pdo, $idAnunciante, $idAnuncio);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($veiculo);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  break;

  case "getInteressados":
    //--------------------------------------------------------------------------------------   
    try {
      $idAnuncio = $_SESSION['idAnuncio'];

      $interessados = Interesse::GetInteressados($pdo, $idAnuncio);

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($interessados);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  break;

  case "logout": 
    try {
      session_destroy();

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['redirect' => '../index.html']);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  break;

    //-----------------------------------------------------------------
  default:
    exit("Ação não disponível");
}


