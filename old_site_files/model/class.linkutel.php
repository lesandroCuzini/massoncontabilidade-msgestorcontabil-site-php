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

class LinkUtelModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome_link_utel;
    public $link_utel;
    public $id_tipo_link_utel;
    public $status;

    public $table = 'links_uteis';
    public $key = "id_link_utel";
    
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

    /**
     * Retorna Links Úteis Filtrando por Tipo Link Utel
     * @param $id_tipo_link_utel
     * @return array
     */
    protected function getLinksUteisByTipoLinkUtel($id_tipo_link_utel){

        $query = "SELECT * FROM ".$this->table." WHERE id_tipo_link_utel=".$id_tipo_link_utel;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
}
