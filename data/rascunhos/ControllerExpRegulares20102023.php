<?php

/*
 * Classe que analisa as expressões regulares definidas pelo usuário
 * e realiza a criação da tabela de transição (automato) para a análise léxica
 */
//require_once '../php/Persistencia/PersistenciaExpRegulares.php';

class ControllerExpRegulares extends Controller{

    public function __construct() {
        $this->carregaClasses('ExpRegulares');
    }
    
    /**
     * Método responsável por chamar o analisador de expressões regulares
     * @param type $sDados
     * @return type
     */
    public function analisaExpressoes($sDados) {

        $sCampos = json_decode($sDados);
        $sTexto = $sCampos->{'texto'};
        $sText = trim($sTexto);

        $sText2 = $this->analisador($sText);

        $sDiretorio = $_SESSION['diretorio'];
        
        $arquivo = $sDiretorio."//defReg.txt";

        //Variável $fp armazena a conexão com o arquivo e o tipo de ação.
        $fp = fopen($arquivo, "w");

        //Escreve no arquivo aberto.
        fwrite($fp, $sText);

        //Fecha o arquivo.
        fclose($fp);

        $sJson = '{"texto":"' . $sText2 . '"}';

        return json_encode($sJson);
    }

    /**
     * Método responsável por realizar a indentificação dos caracteres válidos nas expressões regulares
     * @param type $sTexto
     * @return string
     */
    public function analisador($sTexto) {

        $sAfd = array();
        $sAfd["a"] = 0;
        $sAfd["b"] = 0;
        $sAfd["c"] = 0;
        $sAfd["d"] = 0;
        $sAfd["e"] = 0;
        $sAfd["f"] = 0;
        $sAfd["g"] = 0;
        $sAfd["h"] = 0;
        $sAfd["i"] = 0;
        $sAfd["j"] = 0;
        $sAfd["k"] = 0;
        $sAfd["l"] = 0;
        $sAfd["m"] = 0;
        $sAfd["n"] = 0;
        $sAfd["o"] = 0;
        $sAfd["p"] = 0;
        $sAfd["q"] = 0;
        $sAfd["r"] = 0;
        $sAfd["s"] = 0;
        $sAfd["t"] = 0;
        $sAfd["u"] = 0;
        $sAfd["v"] = 0;
        $sAfd["x"] = 0;
        $sAfd["y"] = 0;
        $sAfd["z"] = 0;
        $sAfd["ç"] = 0;
        $sAfd["A"] = 0;
        $sAfd["B"] = 0;
        $sAfd["C"] = 0;
        $sAfd["D"] = 0;
        $sAfd["E"] = 0;
        $sAfd["F"] = 0;
        $sAfd["G"] = 0;
        $sAfd["I"] = 0;
        $sAfd["J"] = 0;
        $sAfd["K"] = 0;
        $sAfd["L"] = 0;
        $sAfd["M"] = 0;
        $sAfd["N"] = 0;
        $sAfd["O"] = 0;
        $sAfd["P"] = 0;
        $sAfd["Q"] = 0;
        $sAfd["R"] = 0;
        $sAfd["S"] = 0;
        $sAfd["T"] = 0;
        $sAfd["U"] = 0;
        $sAfd["V"] = 0;
        $sAfd["X"] = 0;
        $sAfd["Y"] = 0;
        $sAfd["Z"] = 0;
        $sAfd["Ç"] = 0;
        $sAfd["1"] = 0;
        $sAfd["2"] = 0;
        $sAfd["3"] = 0;
        $sAfd["4"] = 0;
        $sAfd["5"] = 0;
        $sAfd["6"] = 0;
        $sAfd["7"] = 0;
        $sAfd["8"] = 0;
        $sAfd["9"] = 0;
        $sAfd["0"] = 0;
        $sAfd[" "] = 0;
        $sAfd[":"] = 0;
        $sAfd[","] = 0;
        $sAfd["."] = 0;
        $sAfd["\'"] = -1;
        $sAfd["@"] = -1;
        $sAfd["#"] = -1;
        $sAfd["$"] = -1;
        $sAfd["%"] = 0;
        $sAfd["¨"] = -1;
        $sAfd["&"] = 0;
        $sAfd["*"] = 0;
        $sAfd["("] = 0;
        $sAfd[")"] = 0;
        $sAfd["_"] = -1;
        $sAfd["+"] = 0;
        $sAfd["-"] = 0;
        $sAfd["´"] = -1;
        $sAfd["`"] = -1;
        $sAfd["{"] = 0;
        $sAfd["}"] = 0;
        $sAfd["ª"] = -1;
        $sAfd["º"] = -1;
        $sAfd["~"] = 0;
        $sAfd["^"] = 0;
        $sAfd["<"] = 0;
        $sAfd[">"] = 0;
        $sAfd[";"] = 0;
        $sAfd["?"] = -1;
        $sAfd["/"] = -1;
        $sAfd["\\"] = -1;
        $sAfd["\t"] = 0;
        $sAfd["\n"] = 0;
        $sAfd["\r"] = 0;
        $sAfd["]"] = 0;
        $sAfd["["] = 0;

        $aChar = str_split($sTexto);
        $sRetorno = ' ';
        foreach ($aChar as $sPos) {
            if ($sAfd[$sPos] != 0) {
                $sRetorno = 'Erro Léxico Caractere ( ' . $sPos . ' ) inesperado!';
            }
        }
        return $sRetorno;
    }

    /**
     * Método responsável por gerar a tabela do automato finito para análise léxica
     * @param type $sDados
     * @return type
     */
    public function geradorTabelaAutomatoFinito($sDados) {
        /*
         * @Observações iniciais
         * São caracteres especiais : e ; pois são usados no controle inicial de separação dos tokens
         */

        //Recebe o json das expressões regulares e transforma em texto.
        $sCampos = json_decode($sDados);
        $sTexto = $sCampos->{'texto'};

        //Separa a string pelo ponto e vírgula
        $aArray = explode(';', trim($sTexto));

        //Remove a possições em branco depois do ; mais analisar se precisa 
        $key = array_search('', $aArray);
        if ($key !== false) {
            unset($aArray[$key]);
        }

        /*
         * Armazena as palavras reservadas para posterior análise léxica e salva as palavras reservadas
         */
        $aPalavrasReservadas = array();

        /*
         * Armazena a tabela de transição do automato usada depois na análise léxica
         */
        $aTabelaAutomato = array();
        
        //Instancia a classe de persistência
        $oPersistencia = new PersistenciaExpRegulares();

        // Obtem o cabeçalho do array
        $aCabecalhoTabelaLexica = $oPersistencia->retornaCabecalhoTabelaLexica()[0];
        
        //Cria cabeçalho da tabela
        $aTabelaAutomato[-1] = $aCabecalhoTabelaLexica;
        
        //Inicializa a variável de controle de estado
        $iPos = 0;
        
        //Estado 0
        $aTabelaAutomato[$iPos][] = $iPos;
        $aTabelaAutomato[$iPos][] = '?';
        
        //Busca caracteres válidos usados para analisar e criar estados de transição conforme correspondência.
        $aArrayCaracteres = $oPersistencia->retornaCaracteresValidos()[0];
               
        /*
         * Inicio da análise: Percore caracteres possíveis e analisar se eles estão especificados
         * criando um estado de transisão para os mesmos 
         */
        
        //Variáveis utilizadas para análise
        $sArrayEstTokenExpr = array(); //Usado para armazenar informações dos estados de transição posteriores ao 0
        $sArrayTokenExpr1 = array(); //(Fica somente palavras compostas)Armazena inicialmente todos os tokens porém retira os que são estados simples ou palavras reservadas definidas a partir de uma expressão 
        $sArrayTokenExpr2 = array(); //Palavras reservadas quando não sozinhas
        $iEst = 0; //Inicia contador de estado em 0
        $iEst2 = 0; //Contador composto caso de palavras reservadas
        $sExp = ''; //Responsável por armazenar a expressão já verificada no estado 0 para não precisar repetir a análise

        //Cria um array do tipo Token=>Expressão Regular removendo espaços em branco
        foreach ($aArray as $sVal1) {
            //Função que aceita o :
            if (strpos($sVal1, '\:') !== false) {
                $aArray2 = explode(':', $sVal1);
                $sArrayTokenExpr1[$aArray2[0]] = ":";
            } else {
                $aArray2 = explode(':', $sVal1);
                $sArrayTokenExpr1[trim($aArray2[0])] = trim($aArray2[1]);
            }
        }
        
        /*
         * Expressões regulares
         * a      reconhece a
         * ab     reconhece a seguido de b
         * a|b    reconhece a ou b
         * [abc]  reconhece qualquer caractere, exceto a, b, c
         * [a-z]  reconhece a, b, c, ... ou z
         * a*     reconhece zero ou mais a's
         * a+     reconhece um ou mais a's
         * a?     reconhece um ou nenhum a
         * (a|b)* reconhece qualquer número de a's ou b's
         * .      reconhece qualquer caractere, exceto quebra de linha
         * \123   reconhece o caractere ASCCII 123 (decimal)
         * 
         * Os operadores posfixos (*, + e ?) tem a maior prioridade. Em seguida está a concatenação e por fim a união ( | ). Parênteses podem ser utilizador para agrupar símbolos.
         * Os caracteres " \ | * + ? ( ) [ ] { } . ^ - possuem significado especial. Para utilizá-los como caracteres normais deve-se precedê-los por \, ou colocá-los entre aspas. Qualquer seqüência de caracteres entre aspas é tratada como caracteres ordinários.
         * 
         * 
         * \+ reconhece + 
         * "+*" reconhece + seguido de * 
         * "a""b" reconhece a, seguido de ", seguido de b
         * \" reconhece "
         * 
         * 
         * Existem ainda os caracteres não imprimíveis, representados por seqüências de escape
         * \n Line Feed
         * \r Carriage Return
         * \s Espaço
         * \t Tabulação
         * \b Backspace
         * \e Esc
         * \XXX O caractere ASCII XXX (XXX é um número decimal)
         * 
         * 
         * falta:a|b;
           falta:*;
           falta:operadores"+";
         * 
         * \caracter reconhece o caracter por exemplo (
         * 
         */
        
        //Percorre caracter por caracter para formar o estado 0 inicial de transição
        foreach ($aArrayCaracteres as $sChar) {
            //Variável de controle para não entrar nos demais ifs caso caracter já analisado
            $bCont = true;
            foreach ($aArray as $sVal) {
                
                $aArray1 = array();
                
                //Função que aceita o :
                if (strpos($sVal, '\:') !== false) {
                    $aArray2 = explode(':', $sVal);
                    $aArray1[0] = $aArray2[0];
                    $aArray1[1] = ":";
                } else {
                    $aArray1 = explode(':', $sVal);
                    $aArray1[0] = trim($aArray1[0]); //Remove espaços em branco
                    $aArray1[1] = trim($aArray1[1]); //Remove espaços em branco
                }
                /*
                 * Retira espaços em branco
                 * Pega posição que contém as definições de cada tokem ex: [a-b] ou &&
                 * E verifica se for igual a 1 inicia a criação da árvore
                 */
                if (trim($aArray1[1]) != "") {
                    //Todas as expressões exceto palavras token:token
                    if (!(trim($aArray1[0]) == trim($aArray1[1]))) {
                        //Tratamento de expressões em branco
                        if ($sChar == "\\t" && $sChar == $aArray1[1]) {
                            if ($sExp != $aArray1[1]) {
                                $iEst++;
                                $sArrayEstTokenExpr[$iEst] = $aArray1;
                                //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                    unset($sArrayTokenExpr1[$aArray1[0]]);
                                }
                            }
                            $aTabelaAutomato[$iPos][] = $iEst; 
                            $bCont = false;
                            $sExp = $aArray1[1];
                            //echo 'aqui entra se precisa fazer alguma projeção para frente';
                        }
                        if ($sChar == "\\n" && $sChar == $aArray1[1]) {
                            if ($sExp != $aArray1[1]) {
                                $iEst++;
                                $sArrayEstTokenExpr[$iEst] = $aArray1;
                                //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                    unset($sArrayTokenExpr1[$aArray1[0]]);
                                }
                            }
                            $aTabelaAutomato[$iPos][] = $iEst; 
                            $bCont = false;
                            $sExp = $aArray1[1];
                            //echo 'aqui entra se precisa fazer alguma projeção para frente';
                        }
                        if ($sChar == "\\r" && $sChar == $aArray1[1]) {
                            if ($sExp != $aArray1[1]) {
                                $iEst++;
                                $sArrayEstTokenExpr[$iEst] = $aArray1;
                                //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                    unset($sArrayTokenExpr1[$aArray1[0]]);
                                }
                            }
                            $aTabelaAutomato[$iPos][] = $iEst;
                            $bCont = false;
                            $sExp = $aArray1[1];
                            //echo 'aqui entra se precisa fazer alguma projeção para frente';
                        }
                        //Escapa simbolo quando contém aspas duplas
                        $sEscapaCol = false;
                        if (strpos($aArray1[1], '"') !== false) {
                            $sEscapaCol = true;
                        }
                        if ($sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r" && $sChar != "{" || $sEscapaCol) {

                            //Substitui textos encontrados com " por exemplo "{" por {
                            if (strpos($aArray1[1], '"') !== false) {
                                $aArray1[1] = str_replace('"', '', $aArray1[1]);
                            }

                            //Opção que analisa se a expressão regular é reconhecida pelo preg_match
                            if ($bCont && (preg_match("/" . $aArray1[1] . "/", $sChar) == 1)) {
                                if ($sExp != $aArray1[1]) {
                                    $iEst++;
                                    $sArrayEstTokenExpr[$iEst] = $aArray1;
                                    //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                    if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                        unset($sArrayTokenExpr1[$aArray1[0]]);
                                    }
                                }
                                $aTabelaAutomato[$iPos][] = $iEst;
                                $bCont = false;
                                $sExp = $aArray1[1];
                            }
                            //Opção que verfica duplicidade na definição de uma expressão regular do tipo ++, --, ||, &&
                            if ($bCont && substr_count($aArray1[1], $sChar) == strlen($aArray1[1]) && strlen($aArray1[1]) > 1) {
                                if ($sExp != $aArray1[1]) {
                                    $iEst++;
                                    $sArrayEstTokenExpr[$iEst] = ["?", $aArray1[1], $aArray1[0]]; //Adiciona o token
                                    //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                    if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                        unset($sArrayTokenExpr1[$aArray1[0]]);
                                    }
                                }
                                $aTabelaAutomato[$iPos][] = $iEst;
                                $bCont = false;
                                $sExp = $aArray1[1];
                            }
                            //Opção quando existe caracteres diferentes que definem um token tipo <=, >=
                            if ($bCont && (preg_match("/[" . $aArray1[1] . "]/", $sChar) == 1) && strlen($aArray1[1]) > 1) {
                                $aCarac = str_split($aArray1[1]);
                                if ($aCarac[0] == $sChar) {
                                    if ($sExp != $aArray1[1]) {
                                        $iEst++;
                                        $sArrayEstTokenExpr[$iEst] = ["?", $aArray1[1], $aArray1[0]]; //Adiciona o token
                                        //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                        if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                            unset($sArrayTokenExpr1[$aArray1[0]]);
                                        }
                                    }
                                    $aTabelaAutomato[$iPos][] = $iEst; //paralelo
                                    if ($aArray1[1] == $aArray1[0]) {
                                        $iEst--;
                                        $iEst2++;
                                    }
                                    $sExp = $aArray1[1];
                                    $bCont = false;
                                }
                            }
                        }
                    }
                    /**
                     * Opção que armazena as palavras reservadas em um array extra caso tenha composições antes ou depois [a-z]
                     * se não for composto realiza as transições das palavras chaves
                     * ex: else, if, while 
                     */ else {
                        $sContr = false;
                        //Percorre todas as entradas verificando se as palavras chaves 'else, if ...' não esteja em uma composição do tipo letras:[a-z]
                        foreach ($aArray as $sVal3) {
                            $aArrayAux = explode(':', $sVal3);
                            $aArrayAux[0] = trim($aArrayAux[0]);//Remove espaços em branco
                            $aArrayAux[1] = trim($aArrayAux[1]);//Remove espaços em branco
                            
                            if ((preg_match("/" . $aArrayAux[1] . "/", $aArray1[1]) == 1) && $aArrayAux[1] != $aArray1[1]) {
                                $sContr = true;
                            }
                        }
                        //Só entra caso as palavras chaves sejam compostas em uma expressão
                        if ($sContr) {
                            $sArrayTokenExpr2[$aArray1[0]] = $aArray1[1];
                        }
                        //Coloca no estado 0  as transições quando 
                        //as palavras chaves forem apenas composições simples sem ser composta em [a-z]
                        if (!$sContr) {
                            if ($bCont && (preg_match("/[" . $aArray1[1] . "]/", $sChar) == 1) && strlen($aArray1[1]) > 1) {
                                $aCarac = str_split($aArray1[1]);
                                if ($aCarac[0] == $sChar) {
                                    if ($sExp != $aArray1[1]) {
                                        $iEst++;
                                        $sArrayEstTokenExpr[$iEst] = ["?", $aArray1[1], $aArray1[0]]; //Adiciona o token
                                        //Parte que retira as expressões que possuem estado (Ficar só compostas)
                                        if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                                            unset($sArrayTokenExpr1[$aArray1[0]]);
                                        }
                                    }
                                    $aTabelaAutomato[$iPos][] = $iEst; //paralelo
                                    $sExp = $aArray1[1];
                                    $bCont = false;
                                }
                            }
                        }
                        //Parte que retira as expressões que possuem estado (Ficar só compostas)
                        if (isset($sArrayTokenExpr1[$aArray1[0]])) {
                            unset($sArrayTokenExpr1[$aArray1[0]]);
                        }
                    }
                }
            }
            //Coloca -1 em todas as posições que não possuem transição na tabela
            if ($bCont) {
                $aTabelaAutomato[$iPos][] = -1; //paralelo
            }
        }
        $iPos++;
        $aTabelaAutomato[$iPos][] = $iPos;
        ksort($sArrayEstTokenExpr); //Ordena o array conforme os estados do menor para o maior
        //Monta o índice de tokens retornados e estados de transição de tokens compostos
        $bCont = true;
        //Array que armazena todos as expressões simples pelo token que são diferentes dos estados de transição e seu respectivo estado
        $aArrayExprEst = array();
        foreach ($sArrayEstTokenExpr as $iEstado => $aVal) {
            if ($aVal[0] != "?") {
                $aArrayExprEst[trim($aVal[0])] = [$iEstado, $aVal[1]];
            }
        }
        //Armazena as palavras reservadas para persistir para análise léxica.
        $aPalavrasReservadas = $sArrayTokenExpr2;
        $iEst = $iEst + $iEst2; //Adiciona os estados que são transições das palavras reservadas
        while (count($sArrayEstTokenExpr) >= $iPos) {
            $sVal = $sArrayEstTokenExpr[$iPos];//Token, expressão
            $aTabelaAutomato[$iPos][] = trim($sVal[0]); //Seta o estado de cada expressão
            $iki = 0; //Contador importante para as expressões compostas
            //Percorre os caracteres colocando -1 quando não tem transição ou o estado de transição
            foreach ($aArrayCaracteres as $sChar) {
                $bCont = true;
                if ($sVal[0] == "?") { //Se for ? é por que é um token composto, estado de transição e não de aceitação
                    $aArray1 = str_split($sVal[1]);
                    //Possibilidade dupla caracteres igual
                    if (strlen($sVal[1]) == 2) {
                        if ($aArray1[1] == $sChar) {
                            $iEst++;
                            $sArrayEstTokenExpr[$iEst] = [$sVal[2], $aArray1[1]];
                            $aTabelaAutomato[$iPos][] = $iEst;
                            $bCont = false;
                        }
                    }
                    //Possibilidade n caracteres iguais
                    if (count($aArray1) > 2) {
                        if ($aArray1[1] == $sChar) {
                            $iEst++;
                            $sArrayEstTokenExpr[$iEst] = ["?", substr($sVal[1], 1), $sVal[2]];
                            $aTabelaAutomato[$iPos][] = $iEst; 
                            $bCont = false;
                        }
                    }
                }
                //Palavras reservadas
                if (count($sArrayTokenExpr2) > 0) {
                    foreach ($sArrayTokenExpr2 as $key => $sExprr) {
                        $aArray1 = str_split($sExprr);
                        if ((preg_match("/" . $sVal[1] . "/", $aArray1[1]) == 1) && $aArray1[1] == $sChar) {
                            $iEst++;
                            //Mais que dois caracteres
                            if (strlen(substr($sExprr, 1)) > 2) {
                                $sArrayEstTokenExpr[$iEst] = ['?', substr($sExprr, 1), $key, $iPos, $sVal[1]];//$sVal[0]
                            } else {
                                $sArrayEstTokenExpr[$iEst] = [$key, substr($sExprr, 1), $key, $iPos, $sVal[1]];
                            }
                            $aTabelaAutomato[$iPos][] = $iEst;
                            unset($sArrayTokenExpr2[$key]);
                            $bCont = false;
                        }
                    }
                }
                //Tendo 3 é palavra reservada e precisa colocar o estado de transição composta por ex:id
                //E tem que ser diferente do estado de transição
                //Ou indicar mais um estado dependendo dos caracteres
                if (isset($sVal[3]) && $sVal[0] != "?") {
                    if (preg_match("/" . $sVal[4] . "/", $sChar) == 1 && $sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r") {
                        if ($sVal[0] == $sVal[2]) {
                        //    $aTabelaAutomato[$iPos][] = $sVal[3]; 
                        //    $bCont = false;
                        } else {
                            $aArray1 = str_split($sVal[1]);
                            if ((preg_match("/" . $sVal[4] . "/", $aArray1[1]) == 1) && $aArray1[1] == $sChar) {
                                $iEst++;
                                //Mais que dois caracteres
                                if (strlen(substr($sVal[1], 1)) > 1) {
                                    $sArrayEstTokenExpr[$iEst] = ['?', substr($sVal[1], 1), $sVal[2], $sVal[3], $sVal[4]];//$sVal[0]
                                } else {
                                    $sArrayEstTokenExpr[$iEst] = [$sVal[2], substr($sVal[2], 1), $sVal[2], $sVal[3], $sVal[4]];
                                }
                                $aTabelaAutomato[$iPos][] = $iEst;
                                $bCont = false;
                            } else {
                                //$aTabelaAutomato[$iPos][] = $sVal[3]; 
                                //$bCont = false;
                            }
                        }
                    }
                }
                //Tokens compostos por outros tokens
                if (count($sArrayTokenExpr1) > 0) {
                    foreach ($sArrayTokenExpr1 as $key => $sExprr) {
                        $sValorExp1 = str_replace("}{", ",", trim($sExprr));
                        $sValorExp2 = str_replace("{", "", $sValorExp1);
                        $sValorExp = str_replace("}", "", $sValorExp2);
                        $aArrayComp = explode(',', $sValorExp);
                        foreach ($aArrayComp as $sKey1 => $sLexic) {
                            if (trim($sVal[0]) == trim($sLexic) && isset($aArrayComp[$sKey1 + 1])) {
                                $sChave = trim($aArrayComp[$sKey1 + 1]);
                                $sExp2 = $aArrayExprEst[$sChave][1];
                                if ((preg_match("/" . $sExp2 . "/", $sChar) == 1) && $sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r") {
                                    if ($iki == 0) {
                                        $iEst++;
                                        $iki++;
                                    }
                                    $aTabelaAutomato[$iPos][] = $iEst; //paralelo
                                    $sArrayEstTokenExpr[$iEst] = [$key, $aArrayComp];
                                    $bCont = false;
                                }
                            }
                        }
                    }
                }
                //Coloca -1 em todas as posições que não possuem transição na tabela
                if ($bCont) {
                    $aTabelaAutomato[$iPos][] = -1;
                }
            }
            $iPos++;
            if (count($sArrayEstTokenExpr) >= $iPos) {
                $aTabelaAutomato[$iPos][] = $iPos;
            }
        }
        
        //Parte que salva as palavras reservadas
        
        $aCsvPalavrasRes = array();
        //Parte que salva as palavras reservadas
        foreach ($aPalavrasReservadas as $linha) {
            $aCsvPalavrasRes[] = [$linha, $linha];
        }

        $oPersistencia->gravaPalavrasReservadas($aCsvPalavrasRes);

        //Parte que grava a tabela do automato para análise léxica
        $oPersistencia->gravaTabelaLexica($aTabelaAutomato); 

        $sJson = '{"texto":"Sucesso!"}';

        return json_encode($sJson);
    }

    /**
     * Método que mostra modal da tabela do automato para a léxica
     * @param type $sDados
     * @return type
     */
    public function mostraModalTabelaLexica($sDados){
        
        $aTabela = $this->oPersistencia->retornaArrayCSV("tabelaAnaliseLexica.csv", 1); 
        $sModal = $this->oView->geraModalTabelaLexica($aTabela);

        return json_encode($sModal);
    }
   
}
