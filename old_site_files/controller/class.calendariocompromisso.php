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

class CalendarioCompromisso extends CalendarioCompromissoModel {

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        if ($acao == "novo_compromisso") {
            $this->novoCompromisso();
        }


    }

    /**
     * Retornam todos compromissos agendados no mês
     *
     * @param int $mes
     * @param int $ano
     *
     */
    public function getCompromissosAgendados($mes, $ano) {
        $lista_compromissos = parent::getCompromissosAgendados($mes, $ano);

        if ($lista_compromissos === false)
            return array();

        $array_datas = array();
        foreach($lista_compromissos as $datas_bloquedas) {
            $array_datas[$datas_bloquedas->data_agendamento] = $datas_bloquedas->total;
        }
        return $array_datas;
    }

    /**
     * Cria um novo compromisso na agenda selecionada
     *
     */
    public function novoCompromisso() {

        $this->setIdCalendario(1);
        $this->setDataAgendamento(formatarData('bd', getRequest('data')));
        $this->setNome(utf8_decode(getRequest('nome')));
        $this->setEmail(utf8_decode(getRequest('email')));
        $this->setTelefone(utf8_decode(getRequest('telefone')));
        $this->setSexo(utf8_decode(getRequest('sexo')));
        $this->setStatus("P");
        $id = $this->incluir();
        if ($id) {
            $array_sucesso = array();
            $array_sucesso['retorno'] == 'ok';
            $array_sucesso['id'] == $id;
            $array_sucesso['nome'] == getRequest('nome');
            $array_sucesso['email'] == getRequest('email');
            echo json_encode($array_sucesso);

        } else {
            $array_falha = array();
            $array_falha['retorno'] == 'erro';
            $array_falha['msg'] == "Erro ao tentar adicionar evento.";
            echo json_encode($array_falha);
        }
        die;

    }
}
