<?php

/*
 * Classe reponsável por gerar a estrutura visualização das modais
 */

class ViewModal {

    /**
     * Recebe como parâmetro o array que veio do csv
     * @return string
     */
    public function geraModalTabelaLexica($aCsvArray) {

        $aCores = array(
            '#CCFFCC', '#C2E3C6', '#B3E6CC', '#B2EBF2', '#CCFFFF', '#F0F4C3', '#E6E6FA', '#FFDAB9', '#E0FFFF', '#F0FFF0',
            '#E0EEE0', '#F0FFF0', '#F0FFF0', '#F5FFFA', '#FAFAD2', '#F5F5DC', '#FFFFF0', '#FFF5EE', '#FFFAFA', '#FFFFE0',
            '#FAEBD7', '#FFF0F5', '#FFFACD', '#FFFFF0', '#FFF8DC', '#FFEBCD', '#FFFAF0', '#FFE4E1', '#FFDEAD', '#FFF8E7',
            '#FFF0F5', '#FFFAFA', '#FFFFF0', '#FFF5EE', '#FFFACD', '#FFEFD5', '#FFE4B5', '#FFEFDB', '#FFDAB9', '#FFE4C4',
            '#FFF5EE', '#FFF8DC', '#FFFAF0', '#FFFACD', '#FFEBCD', '#FFDAB9', '#FFE4E1', '#FFDEAD', '#FFEFD5', '#FFE4B5',
            '#FFEFDB', '#FFDAB9'
        );

        $sHtmlModal = '<table class="modal-table">';
        $iK = 0;
        foreach ($aCsvArray as $row) {
            if ($iK == 0) {
                $sHtmlModal .= '<tr style="background-color: #dbcdb9;">';
            } else {
                $sHtmlModal .= '<tr>';
            }
            foreach ($row as $cell) {
                if (is_int((int) htmlspecialchars($cell)) && (int) htmlspecialchars($cell) > 0 && $iK != 0) {
                    $sHtmlModal .= '<td class="modal-table td" style="background-color: ' . $aCores[(int) htmlspecialchars($cell)] . ';">' . htmlspecialchars($cell) . '</td>';
                } else {
                    $sHtmlModal .= '<td class="modal-table td">' . htmlspecialchars($cell) . '</td>';
                }
            }
            $iK++;
            $sHtmlModal .= '</tr>';
        }
        $sHtmlModal .= '</table>';

        $sDiretorio = $_SESSION['diretorio'];

        $arquivo = $sDiretorio."//modal.html";

        //Variável $fp armazena a conexão com o arquivo e o tipo de ação.
        $fp = fopen($arquivo, "w");

        //Escreve no arquivo aberto.
        fwrite($fp, $sHtmlModal);

        //Fecha o arquivo.
        fclose($fp);

        return $sHtmlModal;
    }

}
