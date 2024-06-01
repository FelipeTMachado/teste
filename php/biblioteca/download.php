<?php

//Inicia a sessão para usar as variáveis de sessão
session_start();

// Verifique se o parâmetro 'arquivo' está presente na consulta
if (isset($_GET['arquivo'])) {
    
    //Diretório do usuário para o sistema
    $sDiretorio = $_SESSION['diretorio'];
    
    // Obtenha o nome do arquivo a partir do parâmetro 'arquivo'
    $nomeArquivo = $_GET['arquivo'];

    // Caminho para o arquivo no servidor
    $caminhoArquivo = '../../'.$sDiretorio.'/' . $nomeArquivo;

    // Verifique se o arquivo existe
    if (file_exists($caminhoArquivo)) {
        // Defina os cabeçalhos apropriados para forçar o download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($caminhoArquivo));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminhoArquivo));

        // Leia o arquivo e envie o conteúdo para o navegador
        readfile($caminhoArquivo);
        exit;
    } else {
        // Se o arquivo não existir, emita um erro 404
        header("HTTP/1.0 404 Not Found");
        echo "Arquivo não encontrado.";
    }
} else {
    // Se o parâmetro 'arquivo' não estiver presente, emita um erro 400
    header("HTTP/1.0 400 Bad Request");
    echo "Parâmetro 'arquivo' ausente.";
}
?>
