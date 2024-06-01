<?php

class ViewAnalisadorLexico {

    /**
     * Recebe como parâmetro o array que veio do csv
     * @return string
     */
    public function geraModalResAnaliseLexica($aCsvArray) {

        $sHtmlModal = '<table class="modal-table">';
        $iK = 0;
        foreach ($aCsvArray as $row) {
            if ($iK == 0) {
                $sHtmlModal .= '<tr style="background-color: #dbcdb9;">';
            } else {
                $sHtmlModal .= '<tr>';
            }
            foreach ($row as $cell) {
                $sHtmlModal .= '<td class="modal-table td">' . htmlspecialchars($cell) . '</td>';
            }
            $iK++;
            $sHtmlModal .= '</tr>';
        }
        $sHtmlModal .= '</table>';

        return $sHtmlModal;
    }

}
