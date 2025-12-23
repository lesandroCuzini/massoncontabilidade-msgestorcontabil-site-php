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
 *  @version  3.2
 *
 */

class TipoServicoCalculadora extends TipoServicoCalculadoraModel {
    
    private $pagina_retorno = 'tipo_servico_calculadora-form.html';

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        // Inclui o registro
        if ($acao == "incluir") {
            $id = $this->insert();
            if ($id) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=erro");
            }
            die;
        }
        // Altera o registro selecionado
        elseif ($acao == "alterar") {
            $id = getRequest("id_registro");
            if ($this->update($id)) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno."?msg=erro&id=" . $id);
            }
            die;
        }
        // Apaga um registro do banco de dados
        elseif ($acao == "excluir") {
            $this->setIdTable(getRequest('id'));
            if ($this->excluir()) {
                //Registra o log da alteração            
               // Log::addNewLog(false, $this, null, null, true);
                exit('ok');
            } else {
                exit('erro');
            }
        }
        // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        }
        elseif($acao == "add_faturamento_tipo_servico"){
            $valor = str_replace(",",".",getRequest("valor"));
            exit($this->addValorTipoServico(getRequest("id_tipo_servico"),getRequest("id_calculadora_faturamento"),$valor));
        }
        elseif ($acao == "faturamentos_tipo_servico"){
            $faturamentos = json_encode($this->getValoresFaturamenteByTipoServico(getRequest('id_tipo_servico_calculadora')));
            exit($faturamentos);
        }
        elseif ($acao == "excluir_faturamento_tipo_servico"){
            exit($this->deleteValoresFaturamento(getRequest('id_tipo_servico_faturamento_calculadora')));
        }
    }

    /**
     * Rertorna a Listagem para o Admin
     */
    public function getListagemAdmin() {
        $lista_resultados = parent::getListaAdmin(0);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach($lista_resultados as $object) {
            $object->status = ($object->status == 'A') ? "<span style='color:#00B285'><i class='fa fa-check' aria-hidden='true'></i></span>" : "<span style='color:#F00'><i class='fa fa-times' aria-hidden='true'></i></span>";
            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/'.$this->pagina_retorno.'?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            $array_results[] = $object;
        }
        return json_encode($array_results);
    }

    /* Insere o registro no banco de dados
    *
    */
    public function insert() {
        $aux_valor_socio = str_replace(".","",getRequest("valor_socio"));
        $valor_socio = str_replace(",",".",$aux_valor_socio);
        $aux_valor_funcionario = str_replace(".","",getRequest("valor_funcionario"));
        $valor_funcionario = str_replace(",",".",$aux_valor_funcionario);

        $this->setNomeTipoServicoCalculadora(getRequest("nome_tipo_servico_calculadora"));
        $this->setValorSocio($valor_socio);
        $this->setValorFuncionario($valor_funcionario);
        $this->setMaximoSocios(getRequest("maximo_socios"));
        $this->setMaximoFuncionarios(getRequest("maximo_funcionarios"));
        $this->setStatus(getRequest("status")=="A"?"A":"I");

        $id = $this->incluir();
        $this->setIdTable($id);

        //Registra o log da inclusão
        Log::addNewLog(true, $this);

        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
     */
    public function update($id) {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $aux_valor_socio = str_replace(".","",getRequest("valor_socio"));
        $valor_socio = str_replace(",",".",$aux_valor_socio);
        $aux_valor_funcionario = str_replace(".","",getRequest("valor_funcionario"));
        $valor_funcionario = str_replace(",",".",$aux_valor_funcionario);

        $this->setNomeTipoServicoCalculadora(getRequest("nome_tipo_servico_calculadora"));
        $this->setValorSocio($valor_socio);
        $this->setValorFuncionario($valor_funcionario);
        $this->setMaximoSocios(getRequest("maximo_socios"));
        $this->setMaximoFuncionarios(getRequest("maximo_funcionarios"));

        $this->setStatus(getRequest("status")=="A"?"A":"I");

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }
    /**
     * Função Utilizada para Adicionar Faturamento ao Tipo Serviço com seus Devidos valores
     * @param $id_tipo_servico_calculadora
     * @param $id_calculadora_faturamento
     * @param $valor
     * @return bool
     */
    public function addValorTipoServico($id_tipo_servico_calculadora, $id_calculadora_faturamento, $valor)
    {
        return parent::addValorTipoServico($id_tipo_servico_calculadora, $id_calculadora_faturamento, $valor);
    }
    /**
     * Função Utilizada para Pegas Os Valores de cada faturamento Por id_tipo_servico
     * @param $id_tipo_servico
     * @return array
     */
    public function getValoresFaturamenteByTipoServico($id_tipo_servico_calculadora)
    {
        return parent::getValoresFaturamenteByTipoServico($id_tipo_servico_calculadora); // TODO: Change the autogenerated stub
    }
    /**
     * Função Utilizada para Apagar os Valores Faturamento
     * @param $id_tipo_servico_faturamento_calculadora
     * @return bool
     */
    public function deleteValoresFaturamento($id_tipo_servico_faturamento_calculadora)
    {
        return parent::deleteValoresFaturamento($id_tipo_servico_faturamento_calculadora); // TODO: Change the autogenerated stub
    }

}
