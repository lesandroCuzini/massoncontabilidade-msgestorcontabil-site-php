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
 *  @version  1.0
 *
 */

class PerguntaFrequenteModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $pergunta_frequente;
    public $reposta_pergunta_frequente;
    public $posicao;
    public $status;

    public $table = 'perguntas_frequentes';
    public $key = "id_pergunta_frequente";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author Dheyson Wildny
     *
     * @param Int $rpp
     * @param Int $pag_atual
     *
     * @return array
     */
    protected function getListaAdmin()
    {
        $query = "SELECT * FROM " . $this->table;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
}
