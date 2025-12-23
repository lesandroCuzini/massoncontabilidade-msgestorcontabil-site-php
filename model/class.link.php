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

class LinkModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $id_link_tipo;
    public $nome;
    public $url;
    public $posicao;
    public $status;

    public $table = 'links';
    public $key = "id_link";
    
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

    /**
     * Retorna os Links pelo tipo
     *
     * @param $id_link_tipo
     * @param $status
     * @param $order_by
     */
    public function getLinksByTipo($id_link_tipo, $status, $order_by) {
        $query = "SELECT * FROM ".$this->table." 
                    WHERE `id_link_tipo` = '".$id_link_tipo."' 
                    AND `status` = '".$status."' 
                    ORDER BY ".$order_by;

        return $this->getListaRegistros(Conexao::getInstance()->executeS($query));
    }
}