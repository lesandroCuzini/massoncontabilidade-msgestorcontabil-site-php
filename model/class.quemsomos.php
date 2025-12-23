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

class QuemSomosModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $datahora_cadastro;
    public $datahora_atualizacao;
    public $nome_pagina;
    public $descricao_home;
    public $titulo_sobre;
    public $titulo_nosso_diferencial;
    public $titulo_como_funciona;
    public $descricao_sobre;
    public $descricao_nosso_diferencial;
    public $descricao_como_funciona;
    public $url_imagem_sobre;
    public $url_imagem_nosso_diferencial;
    public $url_imagem_como_funciona;
    public $url_rewrite;

    public $table = 'quem_somos';
    public $key = "id_quem_somos";
    
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
}
