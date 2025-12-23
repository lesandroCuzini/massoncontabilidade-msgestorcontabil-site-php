<?php

/*
 * 2021 3dots
 *
 * DISCLAIMER
 *
 * Do not edit or add anything to this file
 * If you need to edit this file consult 3dots.
 *
 *  @author 3dots <contato@3dots.com.br>
 *  @copyright 2021 3dots
 *  @version  1.0
 *
 */

class LinkTipoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $descricao;
    public $status;

    public $table = 'links_tipos';
    public $key = "id_link_tipo";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author 3dots
     *
     * @param Int $rpp
     * @param Int $pag_atual
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM " . $this->table ;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

}
