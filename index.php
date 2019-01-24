<?php
  require "vendor/phpoffice/phpexcel/Classes/PHPExcel.php";
  require "gerarPdf.php";

  // Habilitando os caracteres especiais para serem convertidos
  setlocale(LC_CTYPE, "de_DE.UTF8");

  $file = "teste-producao.xlsx";
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

  // transformando o arquivo .xlsx em .csv
  $objReader = PHPExcel_IOFactory::createReader("Excel2007");
  $objPHPExcel = $objReader->load($file);
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "CSV");
  $csvFileName = str_replace(".xlsx", ".csv", $file);
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
      // echo $nomeCompleto . "<br />";
    }
    else {
      $nomeCompleto = $nome . " " . $sobrenome[0];
      array_push($nomesDefinitivos, $nomeCompleto);
      // echo $nomeCompleto . "<br />";
    }
  }
  $resultado = "";
  foreach ($nomesDefinitivos as $value) {
    $resultado = $resultado . "
      <fieldset>
        $value
      </fieldset>
      <hr>
    ";
  }

  createPDF($resultado, 'teste.pdf', 'A4', 'landscape');

  ?>
