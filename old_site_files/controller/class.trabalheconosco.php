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

class TrabalheConosco extends TrabalaheConoscoModel {
    
    private $pagina_retorno = 'trabalhe-conosco.html';

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
                    header("Location: " . BASE_SITE_URL . '/' . $this->pagina_retorno."?msg=ok");
                } else {
                    header("Location: " . BASE_SITE_URL . '/' . $this->pagina_retorno."?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_SITE_URL . '/' . $this->pagina_retorno."?msg=erro");
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
    }

    /**
     * Rertorna a Listagem para o Admin
     */
    public function getListagemAdmin() {
        $lista_resultados = parent::getListaAdmin(0);
        if ($lista_resultados === false)
            return false;
        
        $array_results = array();
        foreach($lista_resultados as $object) {;
            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/trabalhe-conosco-form.html?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            
            $array_results[] = $object;
        }
            
        return json_encode($array_results);
    }
    
    /* Insere o registro no banco de dados
     *
     */
    public function insert() {
        $this->setNomeTrabalheConosco(getRequest("nome_trabalhe_conosco"));
        $this->setEmailTrabalheConosco(getRequest("email_trabalhe_conosco"));
        $this->setTelefoneTrabalheConosco(getRequest("telefone_trabalhe_conosco"));
        $this->setMensagemTrabalheConosco(getRequest("mensagem_trabalhe_conosco"));
        if (isset($_FILES['url_curriculo']) && $_FILES['url_curriculo']['tmp_name'] != "") {
            $curriculo = uploadArquivo($_FILES['url_curriculo'], 'uploads/curriculos/', 'curriculo');
            $this->setUrlCurriculo($curriculo);
        }
        $id = $this->incluir();
        $this->setIdTable($id);
        
        //Registra o log da inclusão            
        //Log::addNewLog(true, $this);
        
        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
     */
    public function update($id) {
        $this->setIdTable($id);
        $this->getByCod();
        //$log_obj_original = clone($this); //Objeto de Log

        $this->setNomeEmpresa(getRequest("nome_empresa"));
        $this->setDescricaoEmpresa(getRequest("descricao_empresa"));
        if (isset($_FILES['imagem_empresa']) && $_FILES['imagem_empresa']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES['imagem_empresa'], 'uploads/empresa/', 'empresa');
            $this->setImagemEmpresa($imagem_bg);
        }

        $return = $this->alterar();
            
        //Registra o log da alteração            
        //Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    public function getEmpresa()
    {
        return parent::getEmpresa();
    }


}
