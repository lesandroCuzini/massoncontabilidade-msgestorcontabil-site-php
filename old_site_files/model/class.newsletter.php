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

class NewsletterModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $email;
    public $status;

    public $table = 'newsletter';
    public $key = "id_newsletter";
    
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

    protected function verificaEmailCadastrado($email){
        $query = "SELECT COUNT(email) AS quantidade FROM ".$this->table." WHERE email='".$email."'";
        $resultado = Conexao::getInstance()->executeS($query);
        return $resultado[0]->quantidade;
    }
}
