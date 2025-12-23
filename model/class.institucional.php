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

class InstitucionalModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $datahora_cadastro;
    public $datahora_atualizacao;
    public $titulo;
    public $subtitulo;
    public $conteudo;
    public $url_banner_topo;
    public $meta_titulo;
    public $meta_descricao;
    public $url_rewrite;
    public $status;

    public $table = 'institucionais';
    public $key = "id_institucional";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author 3dots
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY `id_institucional`";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Adiciona um Complemento
     *
     * @param $infoComplemento
     * @return bool|string
     */
    protected function addInfoComplemento($infoComplemento) {
        $query = "INSERT INTO `institucionais_complementos` (
                      `id_institucional`, `titulo`, `conteudo`
                  ) VALUES (
                      '".$infoComplemento['id_institucional']."', 
                      '".$infoComplemento['titulo']."', 
                      '".$infoComplemento['conteudo']."' 
                  )";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * Remove um Complemento
     * @param $id_registro
     * @param $id_complemento
     * @return bool|string
     */
    public function removerInfoComplemento($id_registro, $id_complemento) {
        $query = "DELETE FROM `institucionais_complementos` 
                    WHERE `id_institucional_complemento` = '".$id_complemento."' 
                    AND `id_institucional` = '".$id_registro."' ";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * Lista os Complementos pelo ID
     * 
     * @param $id_institucional
     * @return array|false
     */
    protected function getComplementosByIdInstitucional($id_institucional) {
        $query = "SELECT * FROM ". $this->table ."_complementos 
                    WHERE `id_institucional` = '". $id_institucional ."' ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
}
