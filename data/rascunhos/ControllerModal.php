<?php

/*
 * Classe responsável pela manipulação das modais e sua reenderização
 */
        
class ControllerModal {
    
    public function mostraModalTabelaLexica($sDados){
        
        $oPersistenciaCSV = new PersistenciaCSV();
        $aTabela = $oPersistenciaCSV->retornaArrayCSV("tabelaAnaliseLexica.csv", 1); 
        $oViewModal = new ViewModal();
        $sModal = $oViewModal->geraModalTabelaLexica($aTabela);

        return json_encode($sModal);
    }
    
    public function mostraModalResultadoAnaliseLexica($sDados){
        $oPersistenciaCSV = new PersistenciaCSV();
        $aTabela = $oPersistenciaCSV->retornaArrayCSV("resultadoAnaliseLexica.csv", 1); 
        $oViewModal = new ViewModal();
        $sModal = $oViewModal->geraModalTabelaLexica($aTabela);

        return json_encode($sModal);
    }
    
}
