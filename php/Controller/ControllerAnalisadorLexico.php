<?php

/*
 * Classe responsável pela analise léxica e retorno dos dados de análise léxica do código digitado pelo usuário
 */

class ControllerAnalisadorLexico extends Controller {

    public function __construct() {
        $this->carregaClasses('AnalisadorLexico');
    }

    //Variáveis e instancias iniciais carregegadas no construtor
    private array $aPalavrasReservadas;
    private array $aTabelaDeTransicao;
    private array $aTabelaTokens;
    private array $aCaracteresSeparados;
    private int $iCount;
    private int $q;
    private int $qntTokens;
    private string $sBuild;
    private array $aListadeTokensLex;

    /**
     * Funcão que inicializa as variáveis utilizadas na análise léxica
     * @param type $sTexto
     * @return string
     */
    public function InicializaAnalisadorLexico($sTexto) {

        $this->aPalavrasReservadas = $this->getOPersistencia()->retornaPalavrasReservadas();
        $this->aTabelaDeTransicao = $this->getOPersistencia()->retornaTabelaDeTransicao();
        $this->aTabelaTokens = $this->getOPersistencia()->retornaTabelaDeTokens();
        $this->aCaracteresSeparados = str_split($sTexto);
        $this->iCount = count($this->aCaracteresSeparados);
        $this->q = 0;
        $this->qntTokens = 0;
        $this->sBuild = "";
        $this->aListadeTokensLex = array();
    }

    /*
     * Método responsável por realizar a análise léxica do código
     */

    public function analiseLexica($sDados) {

        $sCampos = json_decode($sDados);
        $sTexto = $sCampos->{'texto'} . " ";
        $sText = str_replace("\n", " ", $sTexto);

        $this->getOPersistencia()->gravaArquivo("codigoParaAnalise.txt", trim($sText));

        $this->InicializaAnalisadorLexico($sText);

        //Inicia a análise léxica
        $iK = 0;
        while ($this->iCount > 0) {
            try {
                //Aceita o caractere e avança uma posição na entrada tanto normal como com espaços
                if (!((($this->aTabelaDeTransicao[$this->q][$this->aCaracteresSeparados[$iK]]) == '-1') 
                        || (($this->aTabelaDeTransicao[$this->q]["'" . $this->aCaracteresSeparados[$iK] . "'"]) == '-1')) 
                        && isset($this->aCaracteresSeparados[$iK]) && isset($this->aTabelaDeTransicao[$this->q][$this->aCaracteresSeparados[$iK]])) {
                    //Estado com espaços
                    if ($this->aCaracteresSeparados[$iK] == " ") {
                        //Concatena até formar um token
                        $this->sBuild .= "'" . $this->aCaracteresSeparados[$iK] . "'";
                        //Seta o estado presente na tabela
                        $this->q = (int) $this->aTabelaDeTransicao[$this->q]["'" . $this->aCaracteresSeparados[$iK] . "'"];
                    } else {
                        //Concatena até formar um token
                        $this->sBuild .= $this->aCaracteresSeparados[$iK];
                        //Seta o estado presente na tabela
                        $this->q = (int) $this->aTabelaDeTransicao[$this->q][$this->aCaracteresSeparados[$iK]];
                    }
                    $this->iCount--;
                    $iK++;
                    //Aceita o token
                } else if (!($this->aTabelaTokens[$this->q] == '?')) {
//                    if (isset($this->aPalavrasReservadas[$this->sBuild])) {
//                        $this->aListadeTokensLex[] = [$this->sBuild, $this->sBuild, $this->qntTokens];
//                    } else {
                        $this->aListadeTokensLex[] = [$this->aTabelaTokens[$this->q], $this->sBuild, $this->qntTokens];
 //                   }
                    $this->qntTokens++;
                    $this->sBuild = "";
                    $this->q = 0;
                } else {
                    //Deixa passar caracter em branco caso não tenha sido definido
                    if ($this->aCaracteresSeparados[$iK] == ' ') {
                        $iK++;
                        $this->iCount--;
                    } else {
                        $this->aListadeTokensLex[] = ['?', 'Caractére '.$this->aCaracteresSeparados[$iK].' não identificado', $this->qntTokens];
                        break;
                    }
                }
                //Regeita caractere não identificado
            } catch (Exception $ex) {
                $this->aListadeTokensLex[] = ['?', 'Caractére não identificado', $this->qntTokens];
                $sJson = '{"texto":"Estado não encontrado!"}';
                return json_encode($sJson);
            }
        }
        $aListaTokenLexPer = array();
        $aListaTokenLexPer[0] = ['Token', 'Lexema', 'Posição'];
        $sTeste = "Token    Lex    Pos \\n ";
        $sTextoRetorno = '{"texto":';
        foreach ($this->aListadeTokensLex as $aLex) {
            $sTeste .= "" . $aLex[0] . "       " . $aLex[1] . "            " . $aLex[2] . " \\n ";
            $aListaTokenLexPer[] = [$aLex[0], $aLex[1], $aLex[2]];
        }
        $this->getOPersistencia()->gravaResultadoAnaliseLexica($aListaTokenLexPer);

        $sTextoRetorno .= '"' . $sTeste . '"}';
        return json_encode($sTextoRetorno);
    }

    /**
     * Método que mostra modal da tabela de saida do resultado da análise léxica
     * @param type $sDados
     * @return type
     */
    public function mostraModalResultadoAnaliseLexica($sDados) {

        $aTabela = $this->getOPersistencia()->retornaArrayCSV("resultadoAnaliseLexica.csv", 1);
        $sModal = $this->getOView()->geraModalResAnaliseLexica($aTabela);

        return json_encode($sModal);
    }

}
