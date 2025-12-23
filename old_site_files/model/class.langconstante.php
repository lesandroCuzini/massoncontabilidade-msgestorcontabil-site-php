<?php

/*
 * 2017 Innovare Company
 *
 * DISCLAIMER
 *
 * Do not edit or add anything to this file
 * If you need to edit this file consult Innovare Company.
 *
 *  @author Innovare Company <contato@innovarecompany.com.br>
 *  @copyright 2017 Innovare Company
 *  @version  1.0
 *
 */

class LangConstanteModel extends Model
{

    /**
     * Atributos privados da classe
     *
     */
    protected $id_table;
    protected $id_lang;
    protected $constante;
    protected $traducao;
    protected $status;

    protected $table = "langs_constantes";
    protected $key = "id_lang_constante";

    /**
     * Retornam todas configurações pré-definidas começam com o parametro
     * @param int $id
     *
     * @return Array
     */
    protected function getConstantesByIdLang($id)
    {
        $sql = "SELECT lc.*, lcl.* FROM " . $this->table . " lc 
                INNER JOIN " . $this->table . "_traducoes lcl ON lc.".$this->key." = lcl.".$this->key."
                WHERE lcl.id_lang = '".$id."' 
                AND lc.status = 'A'";

        $resultado = self::$conexao->executeS($sql);
        $retorno = $this->getListaRegistros($resultado, $this->table."_traducoes");

        return $retorno;
    }
}
