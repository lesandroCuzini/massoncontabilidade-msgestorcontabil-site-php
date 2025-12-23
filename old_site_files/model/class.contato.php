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

class ContatoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $data_cadastro;
    public $nome;
    public $email;
    public $telefone;
    public $celular;
    public $mensagem;
    public $status;

    public $table = 'contatos';
    public $key = "id_contato";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author Dheyson Wildny
     *
     * @param Int $rpp
     * @param Int $pag_atual
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM " . $this->table. " ORDER BY id_contato DESC" ;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

}
