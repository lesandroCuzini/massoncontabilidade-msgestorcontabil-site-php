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

class BannerModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $link_banner;
    public $posicao;
    public $nome;
    public $alt_imagem;
    public $url_imagem;
    public $target;
    public $status;

    public $table = 'banners';
    public $key = "id_banner";
    
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
        $query = "SELECT * FROM " . $this->table ;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Retornam os banners pelo tipo
     * 
     * @param int $id_tipo
     * @param int $quantidade
     * @param String $order
     * 
     * @return Array
     */
    protected function getBannersByTipo($id_tipo, $quantidade, $order) {
        $query = "SELECT b.*, bl.* FROM ".$this->table." b INNER JOIN ".$this->table."_langs bl ON b.".$this->key." = bl.".$this->key." AND bl.id_lang = ".$_COOKIE["id_lang"]." WHERE b.status = 'A' AND b.id_tipo = '".$id_tipo."' ";
        if ($order != "") {
            $query .= " ORDER BY ".$order;
        } else {
            if ($quantidade == 1)
                $query .= " ORDER BY RAND() ";
            else
                $query .= " ORDER BY b.posicao, b.id_banner DESC ";
        }
        if ($quantidade > 0)
            $query .= " LIMIT ".$quantidade;
        $resultado = Conexao::getInstance()->executeS($query);
        
        return $this->getListaRegistros($resultado);
    }

}
