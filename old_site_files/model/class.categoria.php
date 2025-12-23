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

class CategoriaModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome;
    public $status;

    public $table = 'categorias';
    public $key = "id_categoria";
    
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
        $query = "SELECT * FROM " . $this->table ;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    protected function verificaProdutoCategoria($id_produto,$id_categoria){
        $query = "SELECT COUNT(*) as quantidade FROM produtos_categorias WHERE 1=1";

        if($id_produto != ""){
            $query .= " AND id_produto='".$id_produto."' ";
        }

        if($id_categoria != ""){
            $query .= " AND id_categoria='".$id_categoria."' ";
        }
        
        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado)->quantidade;
    }

    protected function deletaCategoriaProduto($id_produto){
        $query = "DELETE FROM produtos_categorias WHERE id_produto='".$id_produto."'";
        return Conexao::getInstance()->execute($query);
    }

    protected function insereCategoriaProduto($id_produto,$id_categoria){
        $query = "INSERT INTO produtos_categorias (id_produto,id_categoria) VALUES ('".$id_produto."','".$id_categoria."')";
        return Conexao::getInstance()->execute($query);
    }

    protected function getCategoriaByName($string){
        $query = "SELECT id_categoria FROM categorias WHERE url_amigavel='".$string."'";
        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

}
