<?php

/*
 * 2011 Innovare Company
 *
 * DISCLAIMER
 *
 * Do not edit or add anything to this file
 * If you need to edit this file consult Innovare Company.
 *
 *  @author Innovare Company <contato@innovarecompany.com.br>
 *  @copyright 2011 Innovare Company
 *  @version  3.2
 *
 */

class CalendarioDataExcessao extends CalendarioDataExcessaoModel {

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {


    }

    /**
     * Retornam todas datas que não haverá possibilidade de agendamento
     *
     * @param int $mes
     * @param int $ano
     *
     * @return Array
     */
    public function getDatasBloqueadas($mes, $ano) {
        $lista_excessoes = parent::getDatasBloqueadas($mes, $ano);

        if ($lista_excessoes === false)
            return array();

        $array_datas = array();
        $i = 1;
        foreach($lista_excessoes as $datas_bloquedas) {
            $array_datas[$i] = $datas_bloquedas->data;
            $i++;
        }
        return $array_datas;
    }
}
