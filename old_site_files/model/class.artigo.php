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

class ArtigoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $titulo_artigo;
    public $descricao_artigo;
    public $imagem_capa_artigo;
    public $data_artigo;
    public $status;

    public $table = 'artigos';
    public $key = "id_artigo";
    
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
     * Retorna o Artigo por Id
     * @int $id
     * @return object
     */
    protected function getArtigoById($id){

        $query = "SELECT * FROM ".$this->table." WHERE ".$this->key."=".$id;
        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado);
    }
}
