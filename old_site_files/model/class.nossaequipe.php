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

class NossaEquipeModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome;
    public $foto;
    public $funcao;
    public $link_facebook;
    public $link_twitter;
    public $link_linkedin;
    public $link_google_plus;
    public $exibir_home;
    public $posicao;
    public $status;

    public $table = 'nossa_equipe';
    public $key = "id_nossa_equipe";
    
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
    /*
     * Retorna os Integrantes da Nossa Equipe Filtrando Por Exibir Home e Status , organizando por posicao
     *
     */
    protected function getNossaEquipeHome(){
     $query = "SELECT * FROM ".$this->table." WHERE exibir_home='S' and status='A' ORDER BY posicao LIMIT 4";
     $resultado = Conexao::getInstance()->executeS($query);
     return $this->getListaRegistros($resultado);
    }
}
