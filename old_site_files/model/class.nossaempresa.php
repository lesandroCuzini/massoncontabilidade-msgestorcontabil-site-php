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

class NossaEmpresaModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $foto_nossa_empresa;
    public $descricao_nossa_empresa;

    public $table = 'nossa_empresa';
    public $key = "id_nossa_empresa";
    
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
     * Atualiza os campos por id_empresa
     * @param $id_texto
     * @param $campo
     * @param $valor
     * @return bool
     */
    protected function updateCamposImagem($id_empresa,$campo,$valor){
        $query = "UPDATE ".$this->table." SET ".$campo."='".$valor."' WHERE id_nossa_empresa=".$id_empresa;
        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }
}
