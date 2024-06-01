<?php

/**
 * Classe responsável por realizar a persistencia dos dados do automato
 */
class PersistenciaAutomato extends Persistencia {

    /**
     * Método responsável por retornar os estados e suas transições presentes na tabela de análise léxica
     * @return type
     */
    public function retornaArrayEstadosTransicoes() {

        $aCSV = $this->retornaArrayCompostoCSV("estTransicaoExpToken.csv", 1);
        return $aCSV;
    }

    /**
     * Função que retorna um array de tokens com a seguinte estrutura [estado]=token
     * @return type
     */
    public function retornaTabelaDeTokens() {

        $aCSV = $this->retornaArrayCSV("tabelaAnaliseLexica.csv", 1);
        $aTokens = array();
        //array_pop($aCSV);
        foreach ($aCSV as $aVal) {
            if ($aCSV[0] != $aVal) {
                $aTokens[trim($aVal[0])] = trim($aVal[1]);
            }
        }
        return $aTokens;
    }

    /**
     * Função que retorna os estados que possuem transições para eles mesmos
     * @return type
     */
    public function retornaTransicoesProprias() {

        $aCSV = $this->retornaArrayCSV("tabelaAnaliseLexica.csv", 1);
        $aTrans = array();
        array_shift($aCSV);
        array_shift($aCSV);
        foreach ($aCSV as $aVal) {
            $iEst = $aVal[0];
            $aTrans[$iEst] = false;
            array_shift($aVal);
            array_shift($aVal);
            foreach ($aVal as $iVal) {
                if ($iEst == $iVal) {
                    $aTrans[$iEst] = true;
                }
            }
        }
        return $aTrans;
    }
}
