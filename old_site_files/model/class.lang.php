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

class LangModel extends Model
{

    /**
     * Atributos privados da classe
     *
     */
    protected $id_table;
    protected $nome;
    protected $sigla;
    protected $url_icone;
    protected $lang_default;
    protected $status;

    protected $table = "langs";
    protected $key = "id_lang";

    /**
     * Retornam todas Linguagens Ativas
     *
     * @return Array
     */
    protected function getLangs()
    {
        $sql = "SELECT l.* FROM " . $this->table . " l 
                WHERE l.status = 'A'";

        $resultado = self::$conexao->executeS($sql);
        $retorno = $this->getListaRegistros($resultado);

        return $retorno;
    }

    /**
     * Retorna o ID da linguagem default
     *
     * @return Array
     */
    protected function getIdDefault()
    {
        $sql = "SELECT l.* FROM " . $this->table . " l 
                WHERE l.status = 'A' AND l.lang_default = 'S'";

        $resultado = self::$conexao->executeS($sql);
        $retorno = $this->getLinhaRegistro($resultado);

        return $retorno;
    }

    /**
     * Retorna o ID da linguagem através da sigla
     * @param String $sigla
     *
     * @return Array
     */
    protected function getLangBySigla($sigla)
    {
        $sql = "SELECT l.* FROM " . $this->table . " l 
                WHERE l.status = 'A' AND l.sigla = '".$sigla."'";

        $resultado = self::$conexao->executeS($sql);
        $retorno = $this->getLinhaRegistro($resultado);

        return $retorno;
    }
}
