<?php

/*
 * Classe que analisa as expressões regulares definidas pelo usuário
 * e realiza a criação da tabela de transição (automato) para a análise léxica
 */

class ControllerExpRegulares extends Controller {

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

        $this->getOPersistencia()->gravaArquivo("defReg.txt", $sText);

        $sText2 = $this->analisador($sText);

        if ($sText2 == ' ') {
            $sText2 = $this->validaDefinicoesRegulares($sText);
        }

        $sJson = '{"texto":"' . $sText2 . '"}';

        return json_encode($sJson);
    }

    /**
     * Método que valida as definições regulares para seguirem os padrões do sistema 
     * finalizando por ; , não pré-definido uma expressão simples usada por uma composta.
     * @param type $definition
     * @return string
     */
    function validaDefinicoesRegulares($definition) {

        if (strlen($definition) > 300) {
            return "Atenção ultrapassou do limite maximo de 300 caracteres!";
        }

        // Verifica se a string de definição termina com ';'
        if (substr($definition, -1) !== ';' && $definition != '') {
            return "A string de definição deve terminar com ';'.";
        }

        // Remove o ponto e vírgula final para evitar uma definição vazia
        $definition = rtrim($definition, ';');

        // Divide a string de definições por ';' para obter definições individuais
        $definitions = explode(';', $definition);

        // Array para armazenar as chaves definidas
        $definedKeys = [];

        // Itera sobre cada definição
        foreach ($definitions as $def) {
            // Remove espaços em branco
            $def = trim($def);
            // Pula definições vazias (por exemplo, se houver um ';' extra no final)
            if (empty($def))
                continue;

            // Divide a definição por ':' para separar chave e valor
            $parts = explode(':', $def, 2);

            // Verifica se a divisão resultou em exatamente duas partes (chave e valor)
            if (count($parts) != 2) {
                return "A definição '$def' está mal formada.";
            }

            list($key, $value) = $parts;

            // Remove espaços em branco ao redor das partes
            $key = trim($key);
            $value = trim($value);

            // Verifica se a chave ou o valor estão vazios
            if (empty($key) || empty($value)) {
                return "A definição '$def' está mal formada.";
            }

            // Adiciona a chave à lista de chaves definidas
            $definedKeys[] = $key;

            // Verificação adicional:caso tenha { siga o padrão específico {chave1}{chave2}
            // Encontra todas as chaves dentro de {}
            preg_match_all('/\{([a-zA-Z]+)\}/', $value, $matches);

            foreach ($matches[1] as $match) {
                if (!in_array($match, $definedKeys)) {
                    return "A definição de '" . $key . "' refere-se a uma chave não definida: '{$match}'.";
                }
            }
        }

        return " ";
    }

    /**
     * Método responsável por realizar a indentificação dos caracteres inválidos, resultando só expressões regulares válidas
     * @param type $sTexto
     * @return string
     */
    public function analisador($sTexto) {

        $aAfd = $this->getOPersistencia()->retornaCaracteresInvalidos()[0]; //Retorna todos os caracteres inválidos
        $aChar = preg_split('//u', $sTexto, -1, PREG_SPLIT_NO_EMPTY); //Consegue lidar com caracteres especiais, caracteres multibyte.

        $sRetorno = ' ';
        foreach ($aChar as $sPos) {
            $sPos = trim($sPos);
            //Verifica se o caracter é inválido
            if (in_array($sPos, $aAfd)) {
                $sRetorno = 'Erro Léxico Caractere (' . $sPos . ') inesperado!';
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
        $this->getOModel()->setAArray(preg_split('/;\s*(?=(?:[^"]|"[^"]*")*$)/', $sTexto));

        //Remove a possições em branco depois do ; mais analisar se precisa 
        $key = array_search('', $this->getOModel()->getAArray());
        if ($key !== false) {
            $this->getOModel()->unsetAArray($key);
        }

        // Obtem o cabeçalho do array
        $aCabecalhoTabelaLexica = $this->getOPersistencia()->retornaCabecalhoTabelaLexica()[0];

        //Cria cabeçalho da tabela
        $this->getOModel()->setValorATabelaAutomato(-1, $aCabecalhoTabelaLexica);

        //Inicializa a variável de controle de estado
        $this->getOModel()->setIPos(0);

        //Estado 0 sempe inicia com uma incógnita pois não reconhece nenhum elemento apenas indica para qual estado ir para reconhecer
        $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIPos());
        $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), '?');

        //Busca caracteres válidos usados para analisar e criar estados de transição conforme correspondência.
        $this->getOModel()->setAArrayCaracteres($this->getOPersistencia()->retornaCaracteresValidos()[0]);

        /*
         * Inicio da análise: Percore caracteres possíveis e analisar se eles estão especificados
         * criando um estado de transisão para os mesmos 
         */

        //Cria um array do tipo Token=>Expressão Regular removendo espaços em branco
        $this->retornaArrayTokenExp();

        //Monta o estado inicial 0
        $this->montaEstadoInicial();

        //Monta os demais estados
        $this->montaEstadosTransicao();

        //Parte que salva as palavras reservadas
        $this->getOPersistencia()->gravaPalavrasReservadas($this->getOModel()->getAPalavrasReservadas());

        //Parte que grava a tabela do automato para análise léxica
        $this->getOPersistencia()->gravaTabelaLexica($this->getOModel()->getATabelaAutomato());

        //Parte que grava ArrayEstTransicaoExpToken para desenho do automato
        $this->getOPersistencia()->gravaArrayEstTransicaoExpToken($this->getOModel()->getAArrayEstTransicaoExpToken());

        $sJson = '{"texto":"Sucesso!"}';

        return json_encode($sJson);
    }

    /**
     * Cria um array do tipo array[Token]=>(Expressão Regular) 
     * removendo espaços em branco e escapa o caractere ":" (Dois pontos)
     */
    public function retornaArrayTokenExp() {
        foreach ($this->getOModel()->getAArray() as $sVal1) {
            //Função que aceita o :
            if (strpos($sVal1, '\:') !== false) {
                $aArray2 = explode(':', $sVal1);
                $this->getOModel()->setValorAArrayTokenExpr(trim($aArray2[0]), ":");
            } else {
                $aArray2 = explode(':', $sVal1);
                $this->getOModel()->setValorAArrayTokenExpr(trim($aArray2[0]), trim($aArray2[1]));
            }
        }
    }

    /**
     * Monta estado inicial do automato
     */
    public function montaEstadoInicial() {
        //Percorre caracter por caracter para formar o estado 0 inicial de transição
        foreach ($this->getOModel()->getAArrayCaracteres() as $sChar) {
            //Variável de controle para não entrar nos demais ifs caso caracter já analisado
            $this->getOModel()->setBCont(true);
            foreach ($this->getOModel()->getAArray() as $sVal) {

                //Cria um array do tipo array[0]=>token; array[1]=>exp; 
                $this->retornaArrayPosTokenExp($sVal);

                /*
                 * Retira espaços em branco
                 * Verifica palavras reservadas
                 * Pega posição que contém as definições de cada tokem ex: [a-b] ou &&
                 * E verifica se for igual a 1 inicia a criação da árvore
                 */
                if (trim($this->getOModel()->getValorAArray1(1)) != "") {

//                                            //Opção que analisa expressões com caracteres especiais
//                        $this->getOModel->setValorAArray1(1, str_replace(['(', ')'], ['', ''], $this->getOModel()->getValorAArray1(1)));
//                        // && $sChar!='(' && $sChar!=')' && $sChar!='|'
//                            
                    
                    //Tratamento de expressões em branco
                    $this->analisaExprEmBranco($sChar);

                    $this->analisaPalavrasChaves($sChar);

                    //Todas as expressões exceto palavras token:token
                    if (!($this->verificaPalavraChave($sChar))) {

                        //Escapa simbolo quando contém aspas duplas
                        $bEscapaCol = false;
                        if (strpos($this->getOModel()->getValorAArray1(1), '"') !== false) {
                            $bEscapaCol = true;
                        }
                        if ($sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r" && $sChar != "{" || $bEscapaCol) {

                            //Substitui textos encontrados com " por exemplo "{" por { (sem aspas)
                            if (strpos($this->getOModel()->getValorAArray1(1), '"') !== false) {
                                $this->getOModel()->setValorAArray1(1, str_replace('"', '', $this->getOModel()->getValorAArray1(1)));
                            }

                            //Opção de caracteres simples +, -, *, /
                            if ($this->getOModel()->getBCont() && $this->getOModel()->getValorAArray1(1) == $sChar) {
                                $this->funcaoAtribuicaoVariaveis();
                                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
                            }
                            
                            //Opção que analisa se a expressão regular do tipo [a-b] ou [a-z]* é reconhecida pelo preg_match
                            if ($this->getOModel()->getBCont() && (preg_match("/^(" . $this->getOModel()->getValorAArray1(1) . ")$/", $sChar) == 1)) {
                                $this->funcaoAtribuicaoVariaveis();
                                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
                            }
                            
                            //Opção que verfica duplicidade na definição de uma expressão regular do tipo ++, --, ||, &&
                            if ($this->getOModel()->getBCont() && substr_count($this->getOModel()->getValorAArray1(1), $sChar) == strlen($this->getOModel()->getValorAArray1(1)) && strlen($this->getOModel()->getValorAArray1(1)) > 1) {
                                $this->funcaoAtribuicaoVariaveis2();
                                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$sChar, $this->getOModel()->getValorAArray1(1)]); /////AQUIIIII
                            }
                            //Opção quando existe caracteres diferentes que definem um token tipo <=, >=
                            if ($this->getOModel()->getBCont() && (preg_match("/[" . $this->getOModel()->getValorAArray1(1) . "]/", $sChar) == 1) && strlen($this->getOModel()->getValorAArray1(1)) > 1) {
                                $aCarac = str_split($this->getOModel()->getValorAArray1(1));
                                if ($aCarac[0] == $sChar) {
                                    $this->funcaoAtribuicaoVariaveis2();
                                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$sChar, $this->getOModel()->getValorAArray1(1)]); /////AQUIIIII
                                }
                            }                            
                        }
                    }
                }
            }
            //Coloca -1 em todas as posições que não possuem transição na tabela
            if ($this->getOModel()->getBCont()) {
                $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), -1);
            }
        }
    }

    /**
     * Cria um array do tipo array[0]=>token; array[1]=>exp; 
     * removendo espaços em branco e escapa o caractere ":" (Dois pontos)
     */
    public function retornaArrayPosTokenExp($sVal) {
        $this->getOModel()->setAArray1(array());

        //Função que aceita o :
        if (strpos($sVal, '\:') !== false) {
            $aArray2 = explode(':', $sVal);
            $this->getOModel()->setValorAArray1(0, trim($aArray2[0]));
            $this->getOModel()->setValorAArray1(1, ':');
        } else {
            $this->getOModel()->setAArray1(explode(':', $sVal));
            $this->getOModel()->setValorAArray1(0, trim($this->getOModel()->getValorAArray1(0))); //Remove espaços em branco
            $this->getOModel()->setValorAArray1(1, trim($this->getOModel()->getValorAArray1(1))); //Remove espaços em branco
        }
    }

    /**
     * Realiza a verificação caso exista um caracter digitado como \t, \r, ou \n
     */
    public function analisaExprEmBranco($sChar) {
        if ($sChar == "\\t" && $sChar == $this->getOModel()->getValorAArray1(1)) {
            $this->funcaoAtribuicaoVariaveis();
            $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
        }
        if ($sChar == "\\n" && $sChar == $this->getOModel()->getValorAArray1(1)) {
            $this->funcaoAtribuicaoVariaveis();
            $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
        }
        if ($sChar == "\\r" && $sChar == $this->getOModel()->getValorAArray1(1)) {
            $this->funcaoAtribuicaoVariaveis();
            $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
        }
    }

    /**
     * Função responsável pela atribuição de valores as variáveis 
     * de acordo com os casamentos das expressões
     */
    public function funcaoAtribuicaoVariaveis() {

        if (!$this->getOModel()->issetATokenEstado($this->getOModel()->getValorAArray1(1))) {
            $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
            $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), $this->getOModel()->getAArray1());
            //Parte que retira as expressões que possuem estado (Ficar só compostas)
            $this->getOModel()->unsetIFissetAArrayTokenExpr($this->getOModel()->getValorAArray1(0));
        }
        if (!$this->getOModel()->issetATokenEstado($this->getOModel()->getValorAArray1(1))) {
            $this->getOModel()->setValorATokenEstado($this->getOModel()->getValorAArray1(1), [$this->getOModel()->getValorAArray1(0), $this->getOModel()->getIEst()]);
        }
        if ($this->getOModel()->issetATokenEstado($this->getOModel()->getValorAArray1(1))) {
            $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getValorATokenEstado($this->getOModel()->getValorAArray1(1))[1]); //ESTADO JÁ DEFINIDO
        } else {
            $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
        }
        $this->getOModel()->setBCont(false);
    }

    /**
     * Função responsável pela atribuição de valores as variáveis 
     * de acordo com os casamentos das expressões adiciona token com ?
     */
    public function funcaoAtribuicaoVariaveis2() {
        $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
        $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), $this->getOModel()->getAArray1());
        $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), ["?", $this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); //Adiciona o token
        //Parte que retira as expressões que possuem estado (Ficar só compostas)
        $this->getOModel()->unsetIFissetAArrayTokenExpr($this->getOModel()->getValorAArray1(0));
        $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
        $this->getOModel()->setBCont(false);
    }

    /**
     * Retorna se o caracter pertence ou não a palavra chave
     * @param type $sChar
     * @return boolean
     */
    public function verificaPalavraChave($sChar) {
        $bRetorno = false;
        if ((trim($this->getOModel()->getValorAArray1(0)) == trim($this->getOModel()->getValorAArray1(1))) && preg_match("/[" . $this->getOModel()->getValorAArray1(1) . "]/", $sChar) == 1) {
            $bRetorno = true;
        }
        return $bRetorno;
    }

    /**
     * Função que realiza a transição das palavras chaves conforme caracter
     * @param type $sChar
     */
    public function analisaPalavrasChaves($sChar) {
        if ($this->verificaPalavraChave($sChar)) {
            if ($this->getOModel()->getBCont() && (preg_match("/[" . $this->getOModel()->getValorAArray1(1) . "]/", $sChar) == 1) && strlen($this->getOModel()->getValorAArray1(1)) > 1) {
                $aCarac = str_split($this->getOModel()->getValorAArray1(1));
                if ($aCarac[0] == $sChar) {
                    $this->getOModel()->setValorAutAPalavrasReservadas([trim($this->getOModel()->getValorAArray1(0)), trim($this->getOModel()->getValorAArray1(0))]); //Preenche array com as palavras chaves para posterior salvar em csv
                    $this->getOModel()->setValorAArrayPalavraChave(trim($this->getOModel()->getValorAArray1(0)), trim($this->getOModel()->getValorAArray1(0))); //Armazena palavras chaves para analise posterior
                    $this->funcaoAtribuicaoVariaveis2();
                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$aCarac[0], $this->getOModel()->getValorAArray1(1)]); /////AQUIIIII
                }
            }
        }
    }

    /**
     * Função responsável pela atribuição de valores as variáveis 
     * de acordo com os casamentos das expressões adiciona token com ?
     */
    public function funcaoAtribuicaoVariaveischaves2() {
        $this->getOModel()->setIEst($this->getOModel()->getIEst());
        $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), $this->getOModel()->getAArray1());
        $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), ["?", $this->getOModel()->getValorAArray1(1), $this->getOModel()->getValorAArray1(0)]); //Adiciona o token
        //Parte que retira as expressões que possuem estado (Ficar só compostas)
        $this->getOModel()->unsetIFissetAArrayTokenExpr($this->getOModel()->getValorAArray1(0));
        $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
        $this->getOModel()->setBCont(false);
    }

    /*
     * Monta os estados de transição e final
     */

    public function montaEstadosTransicao() {

        $this->getOModel()->setIPos($this->getOModel()->getIPos() + 1);
        $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIPos());
        ksort($this->getOModel()->getAArrayEstTokenExpr()); //Ordena o array conforme os estados do menor para o maior
        //Monta o índice de tokens retornados e estados de transição de tokens compostos
        $this->getOModel()->setBCont(true);

        //Armazena todas as expressões simples pelo token que são diferentes dos estados de transição e seu respectivo estado
        foreach ($this->getOModel()->getAArrayEstTokenExpr() as $iEstado => $aVal) {
            if ($aVal[0] != "?") {
                $this->getOModel()->setValorAArrayExprEst(trim($aVal[0]), [$iEstado, $aVal[1]]);
            } else {
                if (strpos($this->getOModel()->getValorAArray1(1), $aVal[1]) !== false) {
                    $this->getOModel()->setValorAArrayExprEst(trim($aVal[1]), [$iEstado, $aVal[1]]);
                }
            }
        }

        //Percorre todos estados que possuem transição ou formam um estado de transição e final
        while (count($this->getOModel()->getAArrayEstTokenExpr()) >= $this->getOModel()->getIPos()) {

            $aVal = $this->getOModel()->getValorAArrayEstTokenExpr($this->getOModel()->getIPos()); //Token, expressão

            $aToken = $this->verificaEstadoComposto($aVal); //Verifica se o atual estado é estado final e de transição composto por outro ex: [a-z]* ou [a-z]+

            $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), trim($aToken[0])); //Seta o token retornado de cada estado

            $this->getOModel()->setIki(0); //Contador importante para as expressões compostas
            //Percorre os caracteres colocando -1 quando não tem transição ou o estado de transição
            foreach ($this->getOModel()->getAArrayCaracteres() as $sChar) {

                $this->getOModel()->setBCont(true);

                $this->funcaoAtribuicaoTokenTransicao($aVal, $sChar); //Se for ? é por que é um estado de transição e não de aceitação

                $this->funcaoAtribuicaoComposta($aVal, $sChar); //Expressões compostas por outras expressões

                $this->funçãoAtribuicaoCuringa($sChar, $aToken); //Expressões compostas por + ou * 
                //Coloca -1 em todas as posições que não possuem transição na tabela
                if ($this->getOModel()->getBCont()) {
                    $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), -1);
                }
            }
            $this->getOModel()->setIPos($this->getOModel()->getIPos() + 1);
            //Seta proximo estado a tabela caso tenha mais um estado para acrescentar na tabela
            if (count($this->getOModel()->getAArrayEstTokenExpr()) >= $this->getOModel()->getIPos()) {

                $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIPos());
                //////VERIFICAR SE ISSO TEM LÓGICA

                if (count($this->getOModel()->getAArrayEstTransicaoExpToken()) <= $this->getOModel()->getIPos() - 1) {
                    $aValProx = $this->getOModel()->getValorAArrayEstTokenExpr(count($this->getOModel()->getAArrayEstTransicaoExpToken())); //Token, expressão
                } else {
                    $aValProx = $this->getOModel()->getValorAArrayEstTokenExpr($this->getOModel()->getIPos()); //Token, expressão
                }

                //Parte para armazenar o array para o desenho do automato
                if (is_array($aValProx[1])) {
                    $aValue = array();
                    $aValue = $this->getOModel()->getAArrayExprEst()[trim($aValProx[1][1])];
                    if (isset($aValue[1])) {
                        $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIPos(), [$aValue[1], $aValProx[0]]); ///AQUIIIII
                    } else {
                        $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIPos(), [$aValProx[1], $aValProx[0]]); ///AQUIIIII
                    }
                } else {
                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos() - 1, $this->getOModel()->getIPos() - 1, [$aVal[1], $aVal[0]]); ///AQUIIIII
                }
            } else {
                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos() - 1, $this->getOModel()->getIPos() - 1, [$aVal[1], $aVal[0]]); ///AQUIIIII
            }
        }
    }

    /**
     * Função responsável por retornar nome do token da expressão equivalente caso o estado for composto e se o token deve ser mudado
     * @param type $aVal
     * @return type
     */
    public function verificaEstadoComposto($aVal) {
        if ($aVal[0] == "?") {
            foreach ($this->getOModel()->getAArrayEstTokenExpr() as $iEstado => $aValor) {
                if (($aValor[1] != $aVal[1]) && preg_match("/^" . $aValor[1] . "$/", substr($aVal[1], 0, 1)) == 1 && (strpos($aValor[1], '*') !== false || strpos($aValor[1], '+') !== false)) {
                    return $aValor; //AQUI O TOKEN PARA REPRESENTAR INÍCIO SUBSTITUI O ?
                }
            }
            return $aVal;
        } else {
            return $aVal;
        }
    }

    /**
     * Se for ? é por que é um estado de transição e não de aceitação
     * @param type $aVal
     * @param type $sChar
     */
    public function funcaoAtribuicaoTokenTransicao($aVal, $sChar) {
        if ($aVal[0] == "?" && $this->getOModel()->getBCont()) {
            $this->getOModel()->setAArray1(str_split($aVal[1]));
            //Possibilidade dupla caracteres igual
            if (strlen($aVal[1]) == 2) {
                if ($this->getOModel()->getValorAArray1(1) == $sChar) {
                    $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
                    $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), [$aVal[2], $this->getOModel()->getValorAArray1(1)]);
                    $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
                    $this->getOModel()->setBCont(false);
                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$sChar, $aVal[2]]); /////AQUIIIII
                }
            }
            //Possibilidade n caracteres iguais
            if (count($this->getOModel()->getAArray1()) > 2) {
                if ($this->getOModel()->getValorAArray1(1) == $sChar) {
                    $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
                    $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), ["?", substr($aVal[1], 1), $aVal[2]]);
                    $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
                    $this->getOModel()->setBCont(false);
                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$this->getOModel()->getValorAArray1(1), $aVal[2]]); /////AQUIIIII
                }
            }
        }
    }

    /**
     * Função que realiza o empilhamento de estados de expressões compostas por outras expressões
     * @param type $aVal
     * @param type $sChar
     */
    public function funcaoAtribuicaoComposta($aVal, $sChar) {
        if ($this->getOModel()->getBCont()) {
            if (count($this->getOModel()->getAArrayTokenExpr()) > 0) {
                foreach ($this->getOModel()->getAArrayTokenExpr() as $key => $sExprr) {
                    $sValorExp1 = str_replace("}{", ",", trim($sExprr));
                    $sValorExp2 = str_replace("{", "", $sValorExp1);
                    $sValorExp = str_replace("}", "", $sValorExp2);
                    $aArrayComp = explode(',', $sValorExp);
                    foreach ($aArrayComp as $sKey1 => $sLexic) {
                        if (trim($aVal[0]) == trim($sLexic) && isset($aArrayComp[$sKey1 + 1])) {
                            $sChave = trim($aArrayComp[$sKey1 + 1]);
                            $sExp2 = $this->getOModel()->getValorAArrayExprEst($sChave)[1];
                            if ((preg_match("/^" . $sExp2 . "$/", $sChar) == 1) && $sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r") {
                                if ($this->getOModel()->getIki() == 0) {
                                    $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
                                    $this->getOModel()->setIki($this->getOModel()->getIki() + 1);
                                }
                                $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
                                $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), [$key, $aArrayComp]);
                                $this->getOModel()->setBCont(false);
                                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$sExp2, $key]); /////AQUIIIII
                            }
                            //Parte que analisa se contém multiplas pilhas
//                            else {
//                                if ((preg_match("/^[" . $sExp2 . "]$/", $sChar) == 1) && $sChar != "\\t" && $sChar != "\\n" && $sChar != "\\r") {
//                                    if ($this->getOModel()->getIki() == 0) {
//                                        $this->getOModel()->setIEst($this->getOModel()->getIEst() + 1);
//                                        $this->getOModel()->setIki($this->getOModel()->getIki() + 1);
//                                    }
//                                    $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIEst());
//                                    $this->getOModel()->setValorAArrayEstTokenExpr($this->getOModel()->getIEst(), [$key, $aArrayComp]);
//                                    $this->getOModel()->setBCont(false);
//                                    $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIEst(), [$sExp2, $key]); /////AQUIIIII
//                                }
//                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Função que verifica se necessário alguma transição para o mesmo estado caso * ou +
     */
    public function funçãoAtribuicaoCuringa($sChar, $aToken) {
        if ($this->getOModel()->getBCont()) {
            //Atribui a transição para seu próprio estado caso necessário
            if (preg_match("/^" . $aToken[1] . "$/", $sChar) == 1 && (strpos($aToken[1], '*') !== false || strpos($aToken[1], '+') !== false)) {
                $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getValorATokenEstado($aToken[1])[1]);
                $this->getOModel()->setBCont(false);
                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getValorATokenEstado($aToken[1])[1], [$aToken[1], $this->getOModel()->getValorAArray1(0)]); /////AQUIIIII
            } else {
                //Verifica a atribuição para as palavras reservadas compostas em uma expressão curringa ex: else seguido de uma letra pertence a exp: [a-z]*
                if ($this->getOModel()->issetAArrayPalavraChave($aToken[0])) {
                    foreach ($this->getOModel()->getAArrayExprEst() as $sKey1 => $aLexic) {//Percorre para cada expressão composta uma vez
                        if (preg_match("/^" . $aLexic[1] . "$/", $sChar) == 1 &&
                                preg_match("/^" . $aLexic[1] . "$/", $aToken[0]) == 1 &&
                                (strpos($aLexic[1], '*') !== false || strpos($aLexic[1], '+') !== false)) {
                            $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $aLexic[0]);
                            $this->getOModel()->setBCont(false);
                            $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $aLexic[0], [$aLexic[1], $aToken[0]]); /////AQUIIIII
                        }
                    }
                } else {
                    //Verifica se um estado final de atribuição composta necessita emplimento do mesmo, no ex soma:{mais}{num}; onde num:[0-9]*;
                    if (isset($aToken[1][1])) { //Verifica se existe variáveis
                        if (isset($this->getOModel()->getValorAArrayExprEst($aToken[1][1])[1])) {
                            if (preg_match("/^" . $this->getOModel()->getValorAArrayExprEst($aToken[1][1])[1] . "$/", $sChar) == 1 && (strpos($this->getOModel()->getValorAArrayExprEst($aToken[1][1])[1], '*') !== false || strpos($this->getOModel()->getValorAArrayExprEst($aToken[1][1])[1], '+') !== false)) {
                                $this->getOModel()->setValorAutATabelaAutomato($this->getOModel()->getIPos(), $this->getOModel()->getIPos());
                                $this->getOModel()->setBCont(false);
                                $this->getOModel()->setValorAArrayEstTransicaoExpToken($this->getOModel()->getIPos(), $this->getOModel()->getIPos(), [$this->getOModel()->getValorAArrayExprEst($aToken[1][1])[1], $aToken[1][1]]); /////AQUIIIII
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Método que mostra modal da tabela do automato para a léxica
     * @param type $sDados
     * @return type
     */
    public function mostraModalTabelaLexica($sDados) {

        $aTabela = $this->getOPersistencia()->retornaArrayCSV("tabelaAnaliseLexica.csv", 1);
        $sModal = $this->getOView()->geraModalTabelaLexica($aTabela);
        $this->getOPersistencia()->gravaArquivo("modal.html", $sModal);

        return json_encode($sModal);
    }
}
