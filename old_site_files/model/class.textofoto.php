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

class TextoFotoModel extends Model
{

    /**
     *
     * Atributos privados da classe
     */
    protected $id_table;
    protected $id_texto;
    protected $url_imagem;

    protected $table = 'textos_fotos';
    protected $key = "id_texto_foto";

    public function getFotosEnviadas($id_fk)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id_texto = '" . $id_fk . "'";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    public function deletaImagem($id_imagem_foto, $id_objeto)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id_texto_foto = '" . $id_imagem_foto . "' AND id_texto = '" . $id_objeto . "'";
        $resultado = Conexao::getInstance()->execute($query);
        return ($resultado) ? $resultado : false;
    }
}
