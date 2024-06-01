<?php

/*
 * Classe responsável pela 
 */

class ControllerGramatica extends Controller {

    public function __construct() {
        $this->carregaClasses('Gramatica');
    }
    
    /**
     * Grava os dados da gramática digitados pelo usuário e verifica se os tokens são válidos
     * @param type $sDados
     * @return type
     */
    public function analisaGramatica($sDados){
        
        $sCampos = json_decode($sDados);
        $sTexto = $sCampos->{'texto'};
        $sText = trim($sTexto);

        $this->getOPersistencia()->gravaArquivo("defGram.txt", $sText); ///TERMINAR A PARTE DA ANÁLISE

        $sJson = '{"texto":"' . $sText . '"}';

        return json_encode($sJson);
        
    }
    
    /**
     * Método responsável por gerar a tabela de firsts e follows
     * @param type $sDados
     */
    public function geradorFirstFollow($sDados){
        
        
        
        
    }
    
    
}