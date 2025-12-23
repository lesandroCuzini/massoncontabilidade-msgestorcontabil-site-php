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
 *  @version  3.2
 *
 */

class Pagina extends PaginaModel {

  public function __construct($submit = false) {
    parent::__construct($submit);
  }

  public function getGrupos() {
    return parent::getGrupos();
  }

  public function getIdsByGrupo($grupo) {
    $ids = parent::getIdsByGrupo($grupo);
    $return = "";
    if ($ids !== false) {
      foreach($ids as $id) {
        $return .= $id->id_pagina . ",";
      }
    }
    $return = substr($return, 0, -1);
    return $return;
  }

  public function getByName($name) {
    return parent::getByName($name);
  }

  public function getPaginas($id_grupo) {
    return parent::getPaginas($id_grupo);
  }

}
