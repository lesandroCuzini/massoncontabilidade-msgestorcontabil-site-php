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

class ServicoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $icon_servico;
    public $nome_servico;
    public $breve_descricao_servico;
    public $descricao_servico;
    public $exibir_home;
    public $posicao;
    public $url_banner;
    public $status;

    public $table = 'servicos';
    public $key = "id_servico";
    
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
     * Retorna os Serviços a serem exibidos na Home
     */
    protected function getServicosHome(){
        $query = "SELECT * FROM ".$this->table." WHERE exibir_home='S' ORDER BY posicao";
        $resultado = Conexao::getInstance()->execute($query);
        return $this->getListaRegistros($resultado);
    }
    /**
     * Atualiza os campos por id_servico
     * @param $id_servico
     * @param $campo
     * @param $valor
     * @return bool
     */
    protected function updateCamposImagem($id_servico,$campo,$valor){
        $query = "UPDATE ".$this->table." SET ".$campo."='".$valor."' WHERE id_servico='".$id_servico."'";
        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }
}
