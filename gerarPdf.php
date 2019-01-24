<?php
    require_once 'vendor/autoload.php';

    use Dompdf\Dompdf;

    function createPDF($html,$filename,$paper,$orientation) {
      $dompdf = new Dompdf();
      $dompdf->loadHtml($html);
      $dompdf->set_option('defaultFont', 'Times New Roman’');
      $dompdf->setPaper($paper, $orientation);
      $dompdf->render();
      $dompdf->stream($filename);
    }
