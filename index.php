<?php
  require "vendor/phpoffice/excel/Classes/PHPExcel.php";
  require_once 'vendor/autoload.php';

  // Habilitando os caracteres especiais para serem convertidos
  setlocale(LC_CTYPE, "de_DE.UTF8");

  use Dompdf\Dompdf;
  use Dompdf\Options;

  $nomesInvertidos = [];
  $nomes = [];
  $nomesDefinitivos = [];
  $juncoes = [
    "DE",
    "DAS",
    "DA",
    "DOS",
    "DO"
  ];

  if ($_FILES) {
    $tempName = $_FILES["arquivo"]["tmp_name"];
    $erro = $_FILES["arquivo"]["error"];
    $nomeArquivo = $_FILES["arquivo"]["name"];
    $nomeArquivoSemExtensao = explode(".", $nomeArquivo);
    $nomeArquivoSemExtensao = $nomeArquivoSemExtensao[0];
    $diretorio = dirname(__FILE__);
    $nomePasta = "/files/";
    $caminhoCompleto = $diretorio.$nomePasta.$nomeArquivo;

    if (file_exists($caminhoCompleto)) {
      echo "O arquivo já existe";
    } elseif ($erro === UPLOAD_ERR_OK){
      move_uploaded_file($tempName, $caminhoCompleto);
    }
  }

  // transformando o arquivo .xlsx em .csv
  $objReader = PHPExcel_IOFactory::createReader("Excel2007");
  $objPHPExcel = $objReader->load($caminhoCompleto);
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "CSV");
  $csvFileName = str_replace(".xlsx", ".csv", $caminhoCompleto);
  $objWriter->save($csvFileName);

  // Pegando o conteúdo do arquivo .csv e colocando em um array
  if (($handle = fopen($csvFileName, "r")) !== false) {
      while (($data = fgetcsv($handle, 1000, ",")) !== false) {
          $num = count($data);
          $row++;
          array_push($nomesInvertidos, mb_strtoupper($data[0]));
      }
      fclose($handle);
  }

  // Organizando os nome invertidos do array para que fiquem na ordem certa
  foreach ($nomesInvertidos as $value) {
    array_push($nomes, explode(", ", $value));
  }


  // Imprimindo os nomes e resoolvendo o problema dos sobrenomes
  foreach ($nomes as $value) {
    $nome = $value[1];
    $sobrenome = explode(" ", $value[0]);
    if (in_array($sobrenome[0], $juncoes)) {
      $nomeCompleto = $nome . " " . $sobrenome[0] . " " . $sobrenome[1];
      array_push($nomesDefinitivos, $nomeCompleto);
    }
    else {
      $nomeCompleto = $nome . " " . $sobrenome[0];
      array_push($nomesDefinitivos, $nomeCompleto);
    }
  }

  foreach ($nomesDefinitivos as $value) {
    $resultado .= "
      <div style='height: 21cm; width:29.7cm;'>
        <div style='width: 90%;'>
          <img style='position: absolute; top: -5%; width:400px' src='layouts/img/ri_3.png'>
          <img style='float: right; width: 800px;' src='layouts/img/ri_2.png'>
        </div>
        <hr style='width: 120%; position: absolute; top: 18%; display:block; float:left'>
        <div style='width:100%; text-align: center; position: absolute; top: 35%;'>
          <p style='font-family: Helvetica; font-size: 400%; margin-right:5%'> $value </p>
        </div>
        <hr style='width: 120%; position: absolute; bottom: 15%; display:block; float:left'>
        <img style='position: absolute; bottom: 10%; width: 900px;' src='layouts/img/ri_9.png'>
        <img style='position: absolute; bottom: 14%; width: 400px; margin-left:60%' src='layouts/img/ri_8.png'>
        <span></span>
      </div>
    ";
  }

  $options = new Options();
  $options->set('isRemoteEnabled', TRUE);
  $dompdf = new Dompdf($options);
  $contxt = stream_context_create([
      'ssl' => [
          'verify_peer' => FALSE,
          'verify_peer_name' => FALSE,
          'allow_self_signed'=> TRUE
      ]
  ]);
  $dompdf->setHttpContext($contxt);
  $dompdf->loadHtml($resultado);
  $dompdf->set_option('defaultFont', 'times');
  $dompdf->setPaper('A4', 'landscape');
  $dompdf->render();
  $dompdf->stream($nomeArquivoSemExtensao.'.pdf');

  ?>
