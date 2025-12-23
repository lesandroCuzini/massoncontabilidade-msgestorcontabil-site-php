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

class PerfilModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome;
    public $status;

    public $table = 'perfis';
    public $key = "id_perfil";
  
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
        $query = "SELECT * FROM " . $this->table;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
    
    /**
     * Verifica se a permissão já foi concedida ao Perfil
     * 
     * @param int $id_perfil
     * @param int $id_pagina
     * @param String $tipo visualizar, incluir, alterar, excluir
     * 
     * @return boolean
     */
     protected static function verificaPermissao($id_perfil, $id_pagina, $tipo) {
        $query = "SELECT * FROM perfis_paginas 
                  WHERE id_perfil = '".$id_perfil."' 
                  AND id_pagina = '".$id_pagina."' ";
        $resultado = Conexao::getInstance()->executeS($query);
        if ($resultado === false)
            return false;
        
        $autorizado = false;
        switch($tipo) {
            case 'visualizar':
                $autorizado = (isset($resultado[0]) && $resultado[0]->visualizar == 'S' ? true : false);
                break;
            case 'incluir':
                $autorizado = (isset($resultado[0]) && $resultado[0]->incluir == 'S' ? true : false);
                break;
            case 'alterar':
                $autorizado = (isset($resultado[0]) && $resultado[0]->alterar == 'S' ? true : false);
                break;
            case 'excluir':
                $autorizado = (isset($resultado[0]) && $resultado[0]->excluir == 'S' ? true : false);
                break;
            default:
                $autorizado = false;
        }
        return $autorizado;
     }
     
     /**
       * Exclui permissoes das páginas do perfil atual
       *
       * @return boolean
       */
     protected function clearPaginasPerfil() {
        $query = "DELETE FROM perfis_paginas where id_perfil = " . $this->getIdTable();
        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
     }
     
    /**
     * Insere permissao para uma página do perfil atual
     *
     * @param $id_pagina
     * @param $visualizar
     * @param $incluir
     * @param $alterar
     * @param $excluir
     * 
     * @return boolean
     */
    protected function addPaginaPerfil($id_pagina, $visualizar, $incluir, $alterar, $excluir) {
        $query = "INSERT INTO perfis_paginas VALUES (".$this->getIdTable().", ".$id_pagina.", '".$visualizar."', '".$incluir."', '".$alterar."', '".$excluir."')";
        return Conexao::getInstance()->execute($query);
    }
    
    /**
     * Retorna lista com todos perfis exceto Super Admin
     * @author Dheyson Wildny
     *
     * @return array
     */
    protected function getPerfisDisponiveis() {
        $query = "SELECT * FROM " . $this->table . " WHERE id_perfil > 1 AND status = 'A'";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
    
    

  /**
   * Testa a permissão de acesso de telas para o usuário logado
   * @author Eduardo
   * @param $page_id
   * @return boolean
   */
  /*public static function testaPermissoes($page_id) {
    $id_perfil = getSession("id_perfil", "user");
    $query = "SELECT * from perfis_paginas where id_perfil = $id_perfil AND id_pagina IN($page_id)";
    $resultado = Conexao::getInstance()->executeS($query);
    if($resultado < 1)
      return false;
    return true;
  }*/

  /**
   * Testa a permissão de acesso da tela de diferentes perfis
   * @author Eduardo
   * @param $id_perfil
   * @param $id_pagina
   * @return boolean
   */
  public function temPermissao($id_perfil, $id_pagina) {
    $query = "SELECT * from perfis_paginas where id_perfil = $id_perfil AND id_pagina = $id_pagina";
    $resultado = Conexao::getInstance()->executeS($query);
    if($resultado < 1)
      return false;
    return true;
  }

  

  

}
