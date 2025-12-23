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

class LogModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    protected $tabela;
    protected $id_usuario;
    protected $datahora_alteracao;
    protected $mensagem;
    protected $status;

    protected $table = 'logs';
    protected $key = "id_log";

    /**
     * Adiciona o registro de Log
     * 
     * @param int $id_tipo
     *
     * @return Boolean
     */
    protected function newLog() {
        $query = "SELECT * FROM ".$this->table." WHERE status = 'A' ";
        return Conexao::getInstance()->execute($query);
    }

}
