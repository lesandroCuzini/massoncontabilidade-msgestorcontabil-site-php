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

class TipoLinkUtelModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome_tipo_link_utel;
    public $icon_tipo_link_utel;
    public $cor_tipo_link_utel;
    public $posicao;
    public $status;

    public $table = 'tipos_link_uteis';
    public $key = "id_tipo_link_utel";
    
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
