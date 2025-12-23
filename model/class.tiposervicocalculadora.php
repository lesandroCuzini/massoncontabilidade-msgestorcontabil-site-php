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

class TipoServicoCalculadoraModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $nome_tipo_servico_calculadora;
    public $valor_socio;
    public $valor_funcionario;
    public $maximo_socios;
    public $maximo_funcionarios;
    public $status;

    public $table = 'tipo_servico_calculadora';
    public $key = "id_tipo_servico_calculadora";
    
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

    /**
     * Função Utilizada para Adicionar Faturamento ao Tipo Serviço com seus Devidos valores
     * @param $id_tipo_servico_calculadora
     * @param $id_calculadora_faturamento
     * @param $valor
     * @return bool
     */
    protected function addValorTipoServico($id_tipo_servico_calculadora,$id_calculadora_faturamento,$valor){
        $query = "INSERT INTO tipo_servico_faturamento_calculadora (id_tipo_servico_calculadora,id_calculadora_faturamento,valor)
                 VALUES (".$id_tipo_servico_calculadora.",".$id_calculadora_faturamento.",'".$valor."')";

        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }

    /**
     * Função Utilizada para Pegas Os Valores de cada faturamento Por id_tipo_servico_calculadora
     * @param $id_tipo_servico
     * @return array
     */
    protected function getValoresFaturamenteByTipoServico($id_tipo_servico_calculadora){
        $query = "SELECT * FROM tipo_servico_faturamento_calculadora AS  TSFC
                  INNER JOIN calculadora_faturamento as CF 
                  ON TSFC.id_calculadora_faturamento = CF.id_calculadora_faturamento
                  WHERE TSFC.id_tipo_servico_calculadora=".$id_tipo_servico_calculadora." ORDER BY CF.posicao";

        $resultado = Conexao::getInstance()->executeS($query);
        return $resultado;
    }

    /**
     * Função Utilizada para Apagar os Valores Faturamento
     * @param $id_tipo_servico_faturamento_calculadora
     * @return bool
     */
    protected function deleteValoresFaturamento($id_tipo_servico_faturamento_calculadora){
        $query = "DELETE FROM tipo_servico_faturamento_calculadora WHERE id_tipo_servico_faturamento_calculadora=".$id_tipo_servico_faturamento_calculadora;

        return Conexao::getInstance()->execute($query);
    }
}
