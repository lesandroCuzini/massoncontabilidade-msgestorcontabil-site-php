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

class BannerModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $id_banner_tipo;
    public $nome_banner;
    public $link_banner;
    public $posicao;
    public $alt_imagem;
    public $url_imagem;
    public $target;
    public $status;

    public $table = 'banners';
    public $key = "id_banner";
    
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
     * Retorna os banners pelo tipo
     *
     * @param $statys
     * @param $id_banner_tipo
     * @param $order_by
     */
    public function getBannersByTipo($status, $id_banner_tipo, $order_by) {
        $query = "SELECT * FROM ".$this->table." 
                    WHERE `status` = '".$status."' 
                    AND `id_banner_tipo` = '".$id_banner_tipo."' 
                    ORDER BY ".$order_by;

        return $this->getListaRegistros(Conexao::getInstance()->executeS($query));
    }

    /**
     * Limpam os campos de imagens
     *
     * @param $id_registro
     * @param $campo
     *
     * @return bool
     */
    protected function updateCamposImagem($id_registro, $campo){
        $query = "UPDATE ".$this->table." SET ".$campo." = '' 
                  WHERE ".$this->key." = '".$id_registro."'";
        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }

}
