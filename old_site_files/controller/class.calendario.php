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

class Calendario extends CalendarioModel {

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        // Retorna a listagem de informações de cabeçalhos de títulos
        if ($acao == "getCabecalhos") {
            $this->getCabecalhos();
            die;
        }
    }

    /**
     * Retorna a listagem de informações de cabeçalhos de títulos
     *
     * @param String $type [array / json / object]
     *
     */
    public function getCabecalhos($type = "json") {
        //Configuração do calendário para N línguas
        $headers = array(
            'pt-br' => array(
                'prev' => 'Mês Anterior',
                'next' => 'Próximo Mês',
                'mes' => array(
                    1 => 'Janeiro',
                    2 => 'Fevereiro',
                    3 => 'Março',
                    4 => 'Abril',
                    5 => 'Maio',
                    6 => 'Junho',
                    7 => 'Julho',
                    8 => 'Agosto',
                    9 => 'Setembro',
                    10 => 'Outubro',
                    11 => 'Novembro',
                    12 => 'Dezembro'
                ),
                'dias' => array(
                    1 => array(
                        'abreviacao' => 'Dom',
                        'descricao' => 'Domingo'
                    ),
                    2 => array(
                        'abreviacao' => 'Seg',
                        'descricao' => 'Segunda-Feira'
                    ),
                    3 => array(
                        'abreviacao' => 'Ter',
                        'descricao' => 'Terça-Feira'
                    ),
                    4 => array(
                        'abreviacao' => 'Qua',
                        'descricao' => 'Quarta-Feira'
                    ),
                    5 => array(
                        'abreviacao' => 'Qui',
                        'descricao' => 'Quinta-Feira'
                    ),
                    6 => array(
                        'abreviacao' => 'Sex',
                        'descricao' => 'Sexta-Feira'
                    ),
                    7 => array(
                        'abreviacao' => 'Sáb',
                        'descricao' => 'Sábado'
                    )
                )
            ),
            'en-us' => array(
                'prev' => 'Monthy Previows',
                'next' => 'Next Monthy',
                'mes' => array(
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December'
                ),
                'dias' => array(
                    1 => array(
                        'abreviacao' => 'Sun',
                        'descricao' => 'Sunday'
                    ),
                    2 => array(
                        'abreviacao' => 'Mon',
                        'descricao' => 'Monday'
                    ),
                    3 => array(
                        'abreviacao' => 'Tue',
                        'descricao' => 'Tuesday'
                    ),
                    4 => array(
                        'abreviacao' => 'Wen',
                        'descricao' => 'Wednesday'
                    ),
                    5 => array(
                        'abreviacao' => 'Thu',
                        'descricao' => 'Thursday'
                    ),
                    6 => array(
                        'abreviacao' => 'Fri',
                        'descricao' => 'Friday'
                    ),
                    7 => array(
                        'abreviacao' => 'Sat',
                        'descricao' => 'Saturday'
                    )
                )
            ),
            'nld' => array(
                'prev' => 'Vorige maand',
                'next' => 'Volgende Maand',
                'mes' => array(
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maart',
                    4 => 'April',
                    5 => 'May',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'August',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'December'
                ),
                'dias' => array(
                    1 => array(
                        'abreviacao' => 'Zon',
                        'descricao' => 'Zondag'
                    ),
                    2 => array(
                        'abreviacao' => 'Maa',
                        'descricao' => 'Maandag'
                    ),
                    3 => array(
                        'abreviacao' => 'Din',
                        'descricao' => 'Dinsdag'
                    ),
                    4 => array(
                        'abreviacao' => 'Woe',
                        'descricao' => 'Woensdag'
                    ),
                    5 => array(
                        'abreviacao' => 'Don',
                        'descricao' => 'Donderdag'
                    ),
                    6 => array(
                        'abreviacao' => 'Vri',
                        'descricao' => 'Vrijdag'
                    ),
                    7 => array(
                        'abreviacao' => 'Zat',
                        'descricao' => 'Zaterdag'
                    )
                )
            )
        );

        if ($type == "json")
            return json_encode($headers[$_COOKIE['iso_code']]);
        elseif ($type == "object")
            return (object)$headers[$_COOKIE['iso_code']];
        else
            return $headers[$_COOKIE['iso_code']];
    }

    /**
     * Retorna a quantidade de dias de um determinado mês
     *
     * @param int $mes
     * @param int $ano
     *
     * @return int $dias_mes
     */
    public function getTotalDaysMonth($mes, $ano) {
        $dias_mes = cal_days_in_month ( CAL_GREGORIAN, $mes, $ano );
        return $dias_mes;
    }

    /**
     * Retorna o dia da semana do primeiro dia do mês
     *
     * @param int $mes
     * @param int $ano
     *
     * @return int $primeiro_dia_semana
     */
    public function getDayWeekOfFirstDay($mes, $ano) {
        $primeiro_dia_semana = date("w", mktime(0, 0, 0, $mes, 1, $ano));
        switch ($primeiro_dia_semana) {
            case "0": return 1; break; //Domingo
            case "1": return 2; break; //Segunda-Feira
            case "2": return 3; break; //Terça-Feira
            case "3": return 4; break; //Quarta-Feira
            case "4": return 5; break; //Quinta-Feira
            case "5": return 6; break; //Sexta-Feira
            case "6": return 7; break; //Sábado
            default: return false;
        }
    }

    /**
     * Retorna a quantidade de semanas do mês
     *
     * @param int $mes
     * @param int $ano
     *
     * @return int $quantidade_semanas
     */
    public function getTotalWeeksOfMonth($mes, $ano) {
        $total_dias_mes = $this->getTotalDaysMonth($mes, $ano);
        $primeiro_dia_semana = $this->getDayWeekOfFirstDay($mes, $ano);
        $total_semanas = 1;
        $dias_restantes = $total_dias_mes - (7-$primeiro_dia_semana)-1;

        $aux = $dias_restantes % 7;
        if ($aux > 0) {
            $semanas = (($dias_restantes-$aux) / 7) + 1;
        } else {
            $semanas = $dias_restantes / 7;
        }
        $total_semanas = $total_semanas + $semanas;

        return $total_semanas;
    }
}
