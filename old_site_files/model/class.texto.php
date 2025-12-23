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

class TextoModel extends Model
{

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $url_imagem;
    public $url_imagem_home;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $nome;
    public $titulo;
    public $subtitulo;
    public $descricao;
    public $missao;
    public $visao;
    public $valores;
    public $status;

    public $table = 'textos';
    public $key = "id_texto";

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
     * Atualiza os campos por id_texto
     * @param $id_texto
     * @param $campo
     * @param $valor
     * @return bool
     */
    protected function updateCamposImagem($id_texto,$campo,$valor){
        $query = "UPDATE ".$this->table." SET ".$campo."='".$valor."' WHERE id_texto='".$id_texto."'";
        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }
}
