<?php

/*
 * Classe que realiza a manipulação de CSVs
 */

class Persistencia {

    /**
     * Grava um array em um arquivo CSV
     * @param string $sArquivo O nome do arquivo CSV a ser criado/gravado
     * @param type $iTipo 0 sistema 1 usuario
     * @param array $dadosArray O array de dados a serem gravados no arquivo CSV
     * @return bool Retorna true se a gravação for bem-sucedida, false em caso de erro
     */
    public function gravaArrayEmCSV($sArquivo, $iTipo, $dadosArray) {

        $nomeArquivo = '';
        if ($iTipo == 0) {
            $nomeArquivo = 'data/' . $sArquivo;
        } else {
            $sDiretorio = $_SESSION['diretorio'];
            $nomeArquivo = $sDiretorio . '//' . $sArquivo;
        }

        $handle = fopen($nomeArquivo, 'w');

        if ($handle !== false) {
            foreach ($dadosArray as $linha) {
                fputcsv($handle, $linha, ';');
            }
            fclose($handle);
            return true; // Retorna true se a gravação for bem-sucedida
        } else {
            echo "Não foi possível criar o arquivo $nomeArquivo.";
            return false; // Retorna false em caso de erro ao criar o arquivo
        }
    }

    /**
     * Método responsável por gravar todos os elementos do array composto array[][] = [valor1, valor2]
     */
    public function gravaArrayCompostoEmCSV($sArquivo, $iTipo, $dadosArray) {
        $nomeArquivo = '';
        if ($iTipo == 0) {
            $nomeArquivo = 'data/' . $sArquivo;
        } else {
            $sDiretorio = $_SESSION['diretorio'];
            $nomeArquivo = $sDiretorio . '/' . $sArquivo;
        }

        $handle = fopen($nomeArquivo, 'w');

        if ($handle !== false) {
            foreach ($dadosArray as $linha) {
                $linhaParaGravar = array();

                // Encontra o valor máximo das chaves
                $maxChave = max(array_keys($linha));

                // Preenche as lacunas com arrays vazios
                for ($i = 1; $i <= $maxChave; $i++) {
                    if (!isset($linha[$i])) {
                        $linha[$i] = [-1, -1]; // Adiciona um array com posições -1, -1
                    }
                }
                // Ordena o array pela chave
                ksort($linha);
                foreach ($linha as $item) {
                    if (is_array($item)) {
                        foreach ($item as $subitem) {
                            $linhaParaGravar[] = $subitem;
                        }
                    } else {
                        $linhaParaGravar[] = '';
                    }
                }
                fputcsv($handle, $linhaParaGravar, ';');
            }
            fclose($handle);
            return true; // Retorna true se a gravação for bem-sucedida
        } else {
            echo "Não foi possível criar o arquivo $nomeArquivo.";
            return false; // Retorna false em caso de erro ao criar o arquivo
        }
    }

    /**
     * Retorna o array do arquivo CSV
     * @param type $sArquivo
     * @param type $iTipo 0 sistema 1 usuario
     * @return type
     */
    public function retornaArrayCSV($sArquivo, $iTipo) {
        $nomeArquivo = '';
        if ($iTipo == 0) {
            $nomeArquivo = 'data/' . $sArquivo;
        } else {
            $sDiretorio = $_SESSION['diretorio'];
            $nomeArquivo = $sDiretorio . '//' . $sArquivo;
        }

        $aCSV = array();

        if (($handle = fopen($nomeArquivo, 'r')) !== false) {
            while (($slinha = fgets($handle)) !== false) {
                $aCSV[] = str_getcsv($slinha, ';');
            }
            fclose($handle);
        } else {
            echo "Não foi possível abrir o arquivo $nomeArquivo.";
        }

        return $aCSV;
    }

    /**
     * Retorna o array composto do arquivo CSV
     * @param type $sArquivo
     * @param type $iTipo 0 sistema 1 usuario
     * @return type
     */
    public function retornaArrayCompostoCSV($sArquivo, $iTipo) {
        $nomeArquivo = '';
        if ($iTipo == 0) {
            $nomeArquivo = 'data/' . $sArquivo;
        } else {
            $sDiretorio = $_SESSION['diretorio'];
            $nomeArquivo = $sDiretorio . '/' . $sArquivo;
        }

        $dadosArray = array();

        if (($handle = fopen($nomeArquivo, 'r')) !== false) {
            while (($linha = fgetcsv($handle, 0, ';')) !== false) {
                $maxChave = max(array_keys($linha));
                $linhaArray = array();
                $iPos = 1;
                for ($i = 0; $i <= $maxChave; $i=$i+2) {
                    if ($linha[$i] != -1) {
                        $linhaArray[$iPos] = [$linha[$i], $linha[$i+1]]; // Adiciona o par de valores ao array da linha
                    }
                    $iPos++;
                }
                $dadosArray[] = $linhaArray; // Adiciona a linha ao array de dados
            }
            fclose($handle);
        } else {
            echo "Não foi possível abrir o arquivo $nomeArquivo.";
        }

        return $dadosArray;
    }

    /**
     * Função para escrever as entradas do usuário para ficar salvo para o próximo logon
     * @param type $sArquivo
     * @param type $sText
     */
    public function gravaArquivo($sArquivo, $sText) {

        $sDiretorio = $_SESSION['diretorio'];

        $arquivo = $sDiretorio . "//" . $sArquivo;

        //Variável $fp armazena a conexão com o arquivo e o tipo de ação.
        $fp = fopen($arquivo, "w");

        //Escreve no arquivo aberto.
        fwrite($fp, $sText);

        //Fecha o arquivo.
        fclose($fp);
    }
}

?>