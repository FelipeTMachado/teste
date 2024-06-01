<?php

/**
 * Classe responsável por recebe os dados da tela de login
 * Criar a sessão e validar os dados de entrada
 * E criar a pasta de arquivos para o usuário da sessão
 */
//Pega valor do request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Inicializa a variável de sessão
    session_start();

    // Obtenha o email do formulário
    $sEmail = $_POST["email"];
    
    // Obtenha o email do formulário
    $sPass = $_POST["pass"];
    
    // Obtenha o email do formulário
    $sModo = $_POST["modo"];
    
    if($sModo=='convidado'){
        $sEmail = "teste@gmail.com";
    }

    //Variável para mostrar a tela principal caso seja válido o email
    $bEmailValido = false;

    //Pasta que inicializa em branco caso exista traz o conteúdo dos arquivos
    $pasta = '';

    // Verifica se o email é válido
    if (filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {

        // Diretório para criar pasta de arquivos
        $diretorio = "datausers//";

        // Crie a pasta com o nome do email
        $pasta = $diretorio . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $sEmail);

        //Salva valores iniciais na variável de sessão do usuário
        $_SESSION['diretorio'] = "..//" . $pasta;
        $_SESSION['email'] = $sEmail;

        // Verifique se a pasta já existe
        if (!file_exists($pasta)) {
            // Crie a pasta
            mkdir($pasta, 0777, true);

            $bEmailValido = true;
        } else {
            $bEmailValido = true;
        }
    } else {
        header("Location: login.php?erro=email_invalido");
    }

    //Apresenta a tela inicial do sistema
    if ($bEmailValido) {

        $defReg = retornaTexto($pasta . '//defReg.txt');//Definições regulares do usuário no sistema
        $codigoParaAnalise = retornaTexto($pasta . '//codigoParaAnalise.txt');//Definições regulares do usuário no sistema
        $defGram = retornaTexto($pasta . '//defGram.txt');//Definições gramatica do usuário no sistema
        
        echo '<!DOCTYPE html>
                <html lang="pt">    
                    <head>
                        <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
                              integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
                        <link rel="stylesheet" href="css/app.css">
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
                        <script src="js/app.js" type="text/javascript"></script> 

                    </head>
                    <body> 
                        <nav class="navbar p-0" style="background:#e3f2fd; display:flex;">
                            <div class="dropdown p-1">
                                <button class="dropbtn" type="button" id="dropdownMenu1" data-bs-toggle="dropdown" style="background:cornflowerblue;">
                                    <span class="icon-asset material-icons ng-star-inserted" style="font-size: calc(1.7vw); width: calc(1.7vw); height: calc(1vh)">menu</span>
                                </button>
                                <div class="dropdown-content">
                                    <a href="#">Link 1</a>
                                    <a href="#">Link 2</a>
                                    <a href="#">Documentação</a>
                                </div>
                            </div>
                            <h4 class="mx-auto" style="font-size:calc(5px + 1vw)">SELSS - SOFTWARE EDUCACIONAL LÉXICO, SINTÁTICO E
                                SEMÂNTICO
                            </h4>
                        </nav>
                        <nav class="navbar p-1" style="background:#e3f2fd;">
                            <div style="width: calc(27vw); height: calc(65vh); background-color: rgba(0,0,255,0.1); border:1px solid black;">
                                <nav class="navbar p-1">
                                    <form class="container-fluid justify-content-start p-0">
                                        <button class="btn btn-sm btn-outline-secondary me-1 " onclick="loadTabLexica()"
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">TABELA DE ANÁLISE
                                            LÉXICA</button>
                                        <div class="div text-center"
                                             style="width: calc(27vw); height: calc(57vh); background-color: rgba(0,0,255,0.1);">
                                            <div class="div text-center p-1">
                                                <h6 class="justify"
                                                    style="border:1px solid black; font-size:calc(1vw);">DEFINIÇÕES REGULARES/TOKENS</h6>
                                            </div>
                                            <div class="div text-justify">
                                                <textarea id="defReg" name="defReg" style="width: calc(26vw); height: calc(50vh);" placeholder=\'Escreva as definições regulares, tokens\'>' . $defReg . '</textarea>
                                            </div>
                                        </div>
                                    </form>
                                </nav>
                            </div>
                            <div style="width: calc(27vw); height: calc(65vh); background-color: rgba(0,0,255,0.1); border:1px solid black;">
                                <nav class="navbar p-1">
                                    <form class="container-fluid justify-content-start p-0">
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">FIRST E
                                            FOLLOWS</button>
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">ITENS</button>
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">TABELA
                                            SINTÁTICA</button>
                                    </form>

                                    <div style="width: calc(27vw); height: calc(57vh); background-color: rgba(0,0,255,0.1);">
                                        <div class="div p-1 text-center">
                                            <h6 class="justify" style="border:1px solid black; font-size:calc(1vw); ">
                                                GRAMÁTICA
                                            </h6>
                                        </div>
                                        <div class="div text-justify">
                                            <textarea id="defGram" name="defGram" style="width: calc(26vw); height: calc(50vh);" placeholder=\'Escreva as definições da gramática\'>'.$defGram.'</textarea> 
                                        </div>
                                    </div>
                                </nav>
                            </div>
                            <div style="width: calc(42vw); height: calc(65vh); background-color: rgba(0,0,255,0.1); border:1px solid black;">

                                <nav class="navbar p-1">
                                    <form class="container-fluid justify-content-start p-0">
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button" onclick="analiseLexica()">LÉXICO</button>
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">SINTÁTICO</button>
                                        <button class="btn btn-sm btn-outline-secondary me-1 "
                                                style="width: calc(vw); height: calc(vh); font-size:calc(1vw)" type="button">SEMÂNTICO</button>
                                        <button class="btn btn-sm btn-secondary" style="width: calc(vw); height: calc(vh); font-size:calc(1vw)"
                                                type="button">?</button>
                                    </form>
                                    <div style="width: calc(42vw); height: calc(30vh); background-color: rgba(0,0,255,0.1);">
                                        <div class="div p-1 text-center">
                                            <h6 class="container-fluid justify-content-start" style="border:1px solid black; font-size:calc(1vw);">
                                                ÁREA PARA INSERIR CÓDIGO DE TESTE PARA AS DEFINIÇÕES CRIADAS
                                            </h6>
                                        </div>
                                        <div class="div p-1 text-justify">
                                            <textarea id="codTest" name="codTest" style="width: calc(41vw); height: calc(22vh);" placeholder=\'Escreva o código a ser analisado\' >'.$codigoParaAnalise.'</textarea> 
                                        </div>
                                    </div>
                                    <div style="width: calc(42vw); height: calc(27vh); background-color: rgba(0,0,255,0.1);">
                                        <div class="div p-1 text-center">
                                            <h6 class="container-fluid justify-content-start" style="border:1px solid black; font-size:calc(1vw);">
                                                SAÍDA DO TESTE
                                            </h6>
                                        </div>
                                        <div class="div p-1 text-justify">
                                            <textarea id="saidaAnalise" name="saidaAnalise" style="resize:none; overflow:auto; width: calc(41vw); height: calc(19vh);"></textarea> 
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </nav>
                        <nav class="navbar p-1" style="background:#e3f2fd">
                            <div style="width: calc(100vw); height: calc(20vh); background-color: rgba(0,0,255,0.1); border:1px solid black;">
                                <div class="div p-1 text-center">
                                    <h6 class="container-fluid justify-content-start" style="border:1px solid black; font-size:calc(1vw);">
                                        CONSOLE DE ERROS E INFORMAÇÕES DAS DEFINIÇÕES REGULARES E GRAMÁTICAS
                                    </h6>
                                </div>
                                <div class="div p-1 text-justify">
                                    <textarea id=\'saidaDefErros\' name=\'saidaDefErros\' style="resize:none; overflow:auto; width: calc(98vw); height: calc(12vh); background-color: #fcfaff;"></textarea>
                                </div>
                            </div>
                        </nav>	
                        <div id="myModal" class="modal">
                            <div class="modal-content">
                                <div class="modal-header-wrapper">
                                    <div class="modal-header">
                                        <h2 class="mx-auto">Tabela de Analise Léxica</h2>
                                        <span class="close-button" onclick="closeModal()">&times;</span>
                                    </div>
                                </div>
                                <div id="csvData" class="modal-table">
                                    <!-- Conteúdo da tabela aqui -->
                                </div>
                                <button id="downloadTabelaAnaliseLexica">Baixar Tabela</button>
                            </div>
                        </div>
                        <div id="myModal2" class="modal">
                            <div class="modal-content">
                                <div class="modal-header-wrapper">
                                    <div class="modal-header">
                                        <h2 class="mx-auto">Resultado da Análise Léxica</h2>
                                        <span class="close-button" onclick="closeModal2()">&times;</span>
                                    </div>
                                </div>
                                <div id="csvData2" class="modal-table">
                                    <!-- Conteúdo da tabela aqui -->
                                </div>
                                <button id="downloadResultadoAnaliseLexica">Baixar Tabela</button>
                            </div>
                        </div>
                    </body>

                </html>
                ';
    }
} else {
    header("Location: login.php?erro=login_invalido");
}

/**
 * Função que realiza a leitura para retornar caso já exista os arquivos pré-carregados no sistema
 * @param type $sNome
 * @return string
 */
function retornaTexto($sNome) {
    // Verifica se o arquivo existe
    if (file_exists($sNome)) {
        // Lê o conteúdo do arquivo e retorna
        return file_get_contents($sNome);
    } else {
        return '';
    }
}

?>