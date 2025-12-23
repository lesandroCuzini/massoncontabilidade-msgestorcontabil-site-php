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

class ArtigoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $datahora_cadastro;
    public $data_artigo;
    public $titulo;
    public $descricao;
    public $conteudo;
    public $url_imagem;
    public $url_imagem_capa;
    public $meta_titulo;
    public $meta_descricao;
    public $url_rewrite;
    public $status;

    public $table = 'artigos';
    public $key = "id_artigo";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author 3dots
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM ". $this->table ." ORDER BY ". $this->key ." DESC";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Limpa os campos de imagens
     *
     * @param $id_registro
     * @param $campo
     *
     * @return bool
     */
    protected function updateCamposImagem($id_registro, $campo) {
        $query = "UPDATE ".$this->table." SET ".$campo." = '' 
                  WHERE ".$this->key." = '".$id_registro."'";

        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }

    /**
     * Lista os artigos conforme a data de publicaçao
     *
     * @param $data_atual
     * @param $status
     * @return array|false
     */
    protected function getArtigosByDataArtigo($data_atual, $status) {
        $query = "SELECT * FROM `". $this->table ."`
                    WHERE `status` = '".$status."'
                    AND `data_artigo` <= '".$data_atual."'
                    ORDER BY `data_artigo` DESC";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Retorna o ID através da URL amigável
     *
     * @param $url_rewrite
     *
     * @return array|false
     */
    protected function getIdByUrlRewrite($url_rewrite) {
        $query = "SELECT * FROM `". $this->table ."` 
                    WHERE `url_rewrite` = '". $url_rewrite ."' 
                    LIMIT 1 ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado);
    }
}
