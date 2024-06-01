<?php

/**
 * Classe responsável por exibir as mensagens no sistema
 */
class Mensagem {
    
    const SUCCESS = 1;
    const INFO = 2;
    const WARNING = 3;
    const ERROR = 4;

    /**
     * Método chamado para exibir as mensagens no sistema
     * @param string $sMensagem
     * @param int $iTipo SUCCESS = 1; INFO = 2; WARNING = 3; ERROR = 4;
     */
    public function exibirToast($sMensagem, $iTipo = self::SUCCESS) {
        $tiposValidos = [self::SUCCESS, self::INFO, self::WARNING, self::ERROR];

        // Verifica se o tipo fornecido é válido
        if (!in_array($iTipo, $tiposValidos)) {
            $iTipo = self::SUCCESS; // Define como padrão se for inválido
        }

        $tipoNomes = [
            self::SUCCESS => 'successMsg',
            self::INFO => 'infoMsg',
            self::WARNING => 'warningMsg',
            self::ERROR => 'erroMsg'
        ];

        echo "<script>var ".$tipoNomes[$iTipo]." = '".$sMensagem."';</script>";
        
    }
}

?>
