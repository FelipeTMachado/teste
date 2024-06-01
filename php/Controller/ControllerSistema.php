<?php

/*
 * Classe responsável por realizar o controle do sistema
 * validando o login e mostrando a tela do sistema
 */

class ControllerSistema extends Controller {

    public function __construct() {
        $this->carregaClasses('Sistema');
    }

    /**
     * Método responsável de mostrar a tela de sistema correspondente
     */
    public function mostraSistema($sDados) {

        $oControllerLogin = new ControllerLogin();

        $bLogin = $oControllerLogin->validaLogin();

        if (!$bLogin) {
            return $oControllerLogin->mostraTelaLogin($sDados);
        } else {
            return $this->getOView()->retornaTelaSistema();
        }
    }

    /*
     * Método responsável pelo cadastro de usuário
     */

    public function mostraTelaCadastroUsuario($sDados) {

        $oControllerLogin = new ControllerLogin();

        return $oControllerLogin->mostraTelaCadastraUsuario($sDados);
    }

    /*
     * Método responsável pelo cadastro de usuário
     */

    public function cadastraUsuario($sDados) {

        $oControllerLogin = new ControllerLogin();

        $bCadastro = $oControllerLogin->realizaCadastroUsuario($sDados);

        if ($bCadastro) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Método responsável por realizar o logout do sistema
     */
    public function realizaLogout($sDados) {

        if (is_dir($_SESSION['diretorio']) && $_SESSION['modo'] == 'convidado') {
            // Tenta remover a pasta
            $this->removerConteudoDaPasta(realpath(($_SESSION['diretorio'])));
        }
        session_start();
        session_destroy();
        $_SESSION = array();
        return true;
    }

    /**
     * Remove o conteúdo das pastas temporárias na hora de fazer o logout do sistema
     * @param type $caminho_da_pasta
     */
    public function removerConteudoDaPasta($caminho_da_pasta) {
        $diretorio = new RecursiveDirectoryIterator($caminho_da_pasta, FilesystemIterator::SKIP_DOTS);
        $arquivos = new RecursiveIteratorIterator($diretorio, RecursiveIteratorIterator::CHILD_FIRST);
        $iK = 0;
        foreach ($arquivos as $arquivo) {
            if ($arquivo->isDir()) {
                rmdir($arquivo->getRealPath());
            } else {
                unlink($arquivo->getRealPath());
            }
            rmdir($caminho_da_pasta);
            $iK++;
        }
        //Verifica se não contém arquivos remove apenas a pasta
        if ($iK == 0) {
            rmdir($caminho_da_pasta);
        }
    }

    /**
     * Exclui o usuário e seus respectivos dados
     * @param type $sDados
     * @return bool
     */
    public function excluirUsuario($sDados) {
        $oControllerLogin = new ControllerLogin();
        $bRetorno = $oControllerLogin->excluirUsuario();
        if ($bRetorno) {
            if (is_dir($_SESSION['diretorio'])) {
                // Tenta remover a pasta
                $this->removerConteudoDaPasta(realpath(($_SESSION['diretorio'])));
            }
            session_start();
            session_destroy();
            $_SESSION = array();

            // Cria um array associativo com o resultado
            $response = array("resultado" => true);

            // Retorna a resposta JSON
            return json_encode($response);
        } else {
            // Cria um array associativo com o resultado
            $response = array("resultado" => false);

            // Retorna a resposta JSON
            return json_encode($response);
        }
    }
}
