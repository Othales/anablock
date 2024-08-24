<?php
require_once 'vendor/autoload.php'; // Carrega o autoloader do Composer

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'])) {
    $pdfFile = $_FILES['pdf'];

    // Verifica se o upload foi bem-sucedido
    if ($pdfFile['error'] == UPLOAD_ERR_OK) {
        $pdfFilePath = $pdfFile['tmp_name'];

        // Instancia o parser
        $parser = new Parser();

        try {
            $pdf = $parser->parseFile($pdfFilePath);

            $text = preg_replace("/\s+/", ' ', $pdf->getText());

            preg_match_all('/\b(?:[a-z0-9-]+\.)+[a-z]{2,}(\/[^\s]*)?/i', $text, $matches);

            $urls = array_filter($matches[0], function($url) {
                // Ajusta a regex para capturar domínios contendo 'gov.br', 'mp.br', ou 'abta.org'
                return !preg_match('/\b(?:gov\.br|mp\.br|abta\.org)\b/i', $url);
            });

            echo "<h2>URLs Encontradas:</h2>";
            foreach ($urls as $url) {
                echo $url . "<br>";
            }

        } catch (Exception $e) {
            echo 'Ocorreu um erro ao processar o arquivo PDF: ' . $e->getMessage();
        }
    } else {
        echo 'Ocorreu um erro ao fazer o upload do arquivo.';
    }
} else {
    echo 'Por favor, envie um arquivo PDF.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de PDF</title>
</head>
<body>
    <h1>Upload de PDF</h1>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="pdf">Escolha um arquivo PDF:</label>
        <input type="file" name="pdf" id="pdf" accept="application/pdf" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>