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

class Orcamento extends OrcamentoModel
{

    private $pagina_retorno = 'orcamentos-form.html';

    public function __construct($submit = false)
    {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao)
    {
        // Inclui o registro
        if ($acao == "incluir") {
            $id = $this->insert();
            if ($id) {
                header("Location: " . BASE_SITE_URL . "/orcamento?msg=ok");
            } else {
                header("Location: " . BASE_SITE_URL . "/orcamento?msg=erro");
            }
            die;
        } // Altera o registro selecionado
        elseif ($acao == "alterar") {
            $id = getRequest("id_registro");
            if ($this->update($id)) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=erro&id=" . $id);
            }
            die;
        } // Apaga um registro do banco de dados
        elseif ($acao == "excluir") {
            $this->setIdTable(getRequest('id'));
            if ($this->excluir()) {
                //Registra o log da alteração            
                // Log::addNewLog(false, $this, null, null, true);
                exit('ok');
            } else {
                exit('erro');
            }
        } // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        }
    }

    /**
     * Rertorna a Listagem para o Admin
     */
    public function getListagemAdmin()
    {
        $lista_resultados = parent::getListaAdmin();
        if ($lista_resultados === false) return false;

        $array_results = array();
        foreach ($lista_resultados as $object) {
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';
            $object->botoes = '<a href="' . BASE_ADMIN_URL . '/' . $this->pagina_retorno . '?id=' . $object->{$this->key} . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

            $array_results[] = $object;
        }

        return json_encode($array_results);
    }

    /* Insere o registro no banco de dados
    *
    */
    public function insert()
    {
        $this->setRazaoSocial(getRequest("razao_social"));
        $this->setDatahoraContato(date("Y-m-d H:m:s"));
        $this->setEndereco(getRequest("endereco"));
        $this->setCidade(getRequest("cidade"));
        $this->setEstado(getRequest("estado"));
        $this->setTelefone(getRequest("telefone"));
        $this->setCelular(getRequest("celular"));
        $this->setEmail(strtolower(getRequest("email")));
        $this->setMensagem(getRequest("mensagem"));
        $this->setStatus(getRequest("status") == "A" ? "A" : "P");

        $id = $this->incluir();
        if(isset($_POST['id_produto']) && $_POST['id_produto'] != ""){
            foreach ($_POST['id_produto'] as $key => $id_produto){
                parent::insertOrcamentoProduto($id_produto,$id,$_POST['quantidade'][$key]);
            }
        }

        setcookie('orcamento', '', time() + 36000);
        unset($_COOKIE['orcamento']);
        $this->setIdTable($id);

        //Registra o log da inclusão
        Log::addNewLog(true, $this);

        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
     */
    public function update($id)
    {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $this->setDatahoraContato(formatarData("bd2",$this->getDatahoraContato()));
        $this->setStatus(getRequest("status") == "A" ? "A" : "P");
        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    public function getProdutosOrcamento($id_orcamento){
        return parent::getProdutosOrcamento($id_orcamento);
    }

}
