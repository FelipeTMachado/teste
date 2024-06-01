<?php

class ModelAutomato{
    
    //Armazena os estados já criados para não redesenhar os mesmos
    private $aEstadosJaCriados = array();
    
    public function getAEstadosJaCriados() {
        return $this->aEstadosJaCriados;
    }

    public function setAEstadosJaCriados($aEstadosJaCriados){
        $this->aEstadosJaCriados = $aEstadosJaCriados;
    }
        
    //Armazena os estados já criados com suas respectivas transições
    private $aEstadoTransicao = array();
    
    public function getAEstadoTransicao() {
        return $this->aEstadoTransicao;
    }

    public function setAEstadoTransicao($aEstadoTransicao): void {
        $this->aEstadoTransicao = $aEstadoTransicao;
    }
    
    //Armazena os estados e seus respectivos tokens
    private $aEstadoToken = array();
    
    public function getAEstadoToken() {
        return $this->aEstadoToken;
    }

    public function setAEstadoToken($aEstadoToken) {
        $this->aEstadoToken = $aEstadoToken;
    }
        
    //Armazena a tabela do automato completa 
    private $aTabelaAutomato = array();
    
    public function getATabelaAutomato() {
        return $this->aTabelaAutomato;
    }

    public function setATabelaAutomato($aTabelaAutomato): void {
        $this->aTabelaAutomato = $aTabelaAutomato;
    }    
    
}