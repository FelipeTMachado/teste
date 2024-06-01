<?php

/*
 * Classe index do sistema que carrega as bibliotecas iniciais e inicializa o sistema
 * E carrega os autoloader das classes
 * Parâmetros pelo $_REQUEST classe, metodo, dados
 */

//Inicia a sessão para usar as variáveis de sessão
session_start();

// Registre a função de autoload personalizada
spl_autoload_register('custom_autoloader');

//Variáveis Classe e Método
$sClasse = "";
$sMetodo = "";

if (isset($_REQUEST['classe'])) {
    $sClasse = $_REQUEST['classe'];
}

if (isset($_REQUEST['classe'])) {
    $sMetodo = $_REQUEST['metodo'];
}

/* * Chama a classe login para verificar o usuário logado e fazer as validações
 * caso não tenha classe e método definido
 */
if ($sClasse == "" && $sMetodo == "") {
    if (isset($_REQUEST['modo'])) {
        if ($_REQUEST['modo'] == 'cadastro') {
            $sClasse = 'ControllerSistema';
            $sMetodo = 'mostraTelaCadastroUsuario';
            $_REQUEST['dados'] = 'cadastro';
        } else {
            $sClasse = 'ControllerSistema';
            $sMetodo = 'mostraSistema';
            $_REQUEST['dados'] = 'login';
        }
    } else {
        $sClasse = 'ControllerSistema';
        $sMetodo = 'mostraSistema';
        $_REQUEST['dados'] = 'login';
    }
}

if ($sClasse != "" && $sMetodo != "") {
    if (isset($_REQUEST['dados'])) {

        $Controller = new $sClasse();

        echo $Controller->$sMetodo($_REQUEST['dados']);
    }
}

//Carrega as classes das pastas inicialmente sem precisar ficando dando require_once
function custom_autoloader($class) {
    // Diretórios a serem pesquisados para as classes
    $directories = ['config/',
        'php/biblioteca/',
        'php/Controller/',
        'php/Model/',
        'php/Persistencia/',
        'php/View/'];

    // Loop através dos diretórios
    foreach ($directories as $directory) {
        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
}
