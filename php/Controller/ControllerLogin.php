<?php

/*
 * Classe responsável pela contrução da classe de login sistema
 */

class ControllerLogin extends Controller {

    public function __construct() {
        $this->carregaClasses('Login');
    }

    /**
     * Retorna a tela de login caso não tenha sessão válida
     * @param type $sDados
     * @return type
     */
    public function mostraTelaLogin($sDados) {
        $sLoginHtml = $this->getOView()->retornaTelaLogin();
        return $sLoginHtml;
    }

    /**
     * Método responsável por validar o login e suas variáveis e redirecionar
     * a tela retornando true se sessão válida ou false caso deva mostrar  a tela de login
     * @return boolean
     */
    public function validaLogin() {

        /**
         * Função responsável por recebe os dados da tela de login
         * Criar a sessão e validar os dados de entrada
         * E criar a pasta de arquivos para o usuário da sessão
         */
        //Pega valor do request POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Obtem o email do formulário
            $sEmail = $_POST["email"];

            // Obtem a senha do formulário
            $sSenha = $_POST["pass"];
            $sPass = password_hash($sSenha, PASSWORD_BCRYPT);

            //Obtém o modo, convidado ou usuário
            $sModo = $_POST["modo"];

            //Valor a ser recebido caso e-mail com senha válido
            $bVal = $this->getOPersistencia()->verificaEmailPass($sEmail, $sSenha);

            //Ignora modo convidado
            if (!$bVal || $sModo == "convidado") {
                //Opta pelo modo sendo os possíveis casos: convidado, cadastro (isso se diferente de usuário)
                switch ($sModo) {
                    case "convidado":
                        $sSenha = $this->gerarNomeConvidado();
                        $sEmail = $sSenha;
                        $sPass = $sSenha;
                        $bVal = true;
                        break;
//                    case "cadastro":
//                        if (trim($sEmail) == '' || $sEmail == null) {
//                            $this->Mensagem('Não é possível cadastrar sem email!', 4);
//                            return false;
//                        }
//                        $bVal = $this->getOPersistencia()->cadastraUsuario($sEmail, $sPass);
//                        if ($bVal) {
//                            $this->Mensagem('Cadastro realizado com sucesso!', 1);
//                        } else {
//                            $this->Mensagem('Não é possível Cadastrar, Email já cadastrado!', 4);
//                            return false;
//                        }
//                        break;
                    default :
                        $this->Mensagem('Verifique email ou senha, ou o modo de entrada!', 4);
                }
            }

            if ($bVal) {
                //Variável para mostrar a tela principal caso seja válido o email
                $bEmailValido = false;

                //Pasta que inicializa em branco caso exista traz o conteúdo dos arquivos
                $pasta = '';

                // Verifica se o email é válido e ignora quando for modo convidado
                if (filter_var($sEmail, FILTER_VALIDATE_EMAIL) || $sModo == "convidado") {

                    // Diretório para criar pasta de arquivos
                    $diretorio = "datausers//";

                    // Crie a pasta com o nome do email
                    $pasta = $diretorio . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $sEmail);

                    //Salva valores iniciais na variável de sessão do usuário
                    $_SESSION['pasta'] = $pasta;
                    $_SESSION['diretorio'] = $pasta;
                    $_SESSION['email'] = $sEmail;
                    $_SESSION['pass'] = $sPass;
                    $_SESSION['modo'] = $sModo;

                    //    $this->oPersistencia->gravaArrayEmCSV($sArquivo, $iTipo, $dadosArray);
                    // Verifique se a pasta já existe
                    if (!file_exists($pasta)) {
                        // Crie a pasta
                        mkdir($pasta, 0777, true);

                        $bEmailValido = true;
                    } else {
                        $bEmailValido = true;
                    }
                } else {
                    return false;
                }

                //Apresenta a tela inicial do sistema
                if ($bEmailValido) {
                    $this->Mensagem('Bem vindo ao sistema!', 1);
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->Mensagem('Email ou senha incorretos!', 4);
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Método responsável por retornar a tela de cadastro de usuário
     * @param type $sDados
     * @return type
     */
    public function mostraTelaCadastraUsuario($sDados) {
        $sLoginHtml = $this->getOView()->retornaTelaCadastro();
        return $sLoginHtml;
    }

    public function realizaCadastroUsuario($sDados) {
        //Pega valor do request POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Obtem o email do formulário
            $sEmail = $_POST["email"];

            // Obtem a senha do formulário
            $sSenha = $_POST["senha"];
            $sPass = password_hash($sSenha, PASSWORD_BCRYPT);

            //Valor a ser recebido caso e-mail com senha válido
            $bVal = $this->getOPersistencia()->verificaEmailPass($sEmail, $sSenha);

            if (!$bVal) {
                if (trim($sEmail) == '' || $sEmail == null) {
                    $this->Mensagem('Não é possível cadastrar sem email!', 4);
                    return false;
                }
                $bVal = $this->getOPersistencia()->cadastraUsuario($sEmail, $sPass);
                if ($bVal) {
                    $this->Mensagem('Cadastro realizado com sucesso!', 1);
                    return true;
                } else {
                    $this->Mensagem('Não é possível Cadastrar, Email já cadastrado!', 4);
                    return false;
                }
            } else {
                $this->Mensagem('Verifique email ou senha, ou o modo de entrada!', 4);
            }
        }
    }

    // Função para gerar um nome de convidado único
    public function gerarNomeConvidado() {
        // Obter timestamp atual
        $timestamp = time();

        // Gerar identificador único
        $identificadorUnico = uniqid();

        // Concatenar timestamp e identificador único para criar um nome único
        $nomeConvidado = "convidado" . $timestamp . "@" . $identificadorUnico;

        return $nomeConvidado;
    }

    public function excluirUsuario() {
        $sEmail = $_SESSION['email'];
        if ($sEmail != null && $sEmail != '') {
            $bVal = $this->getOPersistencia()->excluirUsuario($sEmail);
            return $bVal;
        }else{
            return false;
        }
    }
}
