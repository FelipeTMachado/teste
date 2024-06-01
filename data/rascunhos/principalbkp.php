<?php

/*
 * Classe principal que chama as controllers do sistema
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

if ($sClasse != "" && $sMetodo != "") {
    if (isset($_REQUEST['dados'])) {

        $Controller = new $sClasse();

        echo $Controller->$sMetodo($_REQUEST['dados']);
    }
}

//Carrega as classes das pastas inicialmente sem precisar ficando dando require_once
function custom_autoloader($class) {
    // Diretórios a serem pesquisados para as classes
    $directories = ['../php/Controller/', '../php/Model/', '../php/Persistencia/', '../php/View/'];

    // Loop através dos diretórios
    foreach ($directories as $directory) {
        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
}