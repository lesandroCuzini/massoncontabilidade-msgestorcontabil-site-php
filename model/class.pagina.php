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

class PaginaModel extends Model {

  /**
   * Atributos privados da classe
   *
   */
  public $id_table;
  public $id_parent;
  public $nome;
  public $titulo;
  public $ordem;
  public $icone;

  public $table = 'paginas';
  public $key = "id_pagina";

  protected function getGrupos() {
    $query = "SELECT * FROM ".$this->table." 
              WHERE id_parent = 0 
              ORDER BY ordem";
    $resultado = Conexao::getInstance()->executeS($query);
    return $this->getListaRegistros($resultado);
  }

  protected function getIdsByGrupo($grupo) {
    $query = "SELECT ".$this->key." FROM ".$this->table." WHERE grupo = '".removerAcento($grupo)."'";
    $resultado = Conexao::getInstance()->executeS($query);
    if ($resultado !== false)
      return $this->getArrayRegistros($resultado);
    else
      return false;
  }

  protected function getPaginas($id_grupo) {
    $query = "SELECT * FROM ".$this->table." WHERE id_parent = '".$id_grupo."' ORDER BY ordem";
    $resultado = Conexao::getInstance()->executeS($query);
    
    return $this->getListaRegistros($resultado);
  }

  protected function getByName($name) {
    $query = "SELECT * FROM ".$this->table." WHERE `nome` = '".$name."' ORDER BY ordem";
    $resultado = Conexao::getInstance()->executeS($query);
    return $this->getLinhaRegistro($resultado);
  }

}
