<?php

/*
 * 2021 3dots
 *
 * DISCLAIMER
 *
 * Do not edit or add anything to this file
 * If you need to edit this file consult 3dots.
 *
 *  @author 3dots <contato@3dots.com.br>
 *  @copyright 2021 3dots
 *  @version  3.2
 *
 */

class Link extends LinkModel
{
    private $pagina_retorno = 'links-form.html';

    public function __construct($submit = false)
    {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     */
    public function postProcess($acao)
    {
        // Inclui o registro
        if ($acao == "incluir") {
            $id = $this->insert();
            if ($id) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=erro");
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
            $id_registro = getRequest('id');
            $this->setIdTable($id_registro);
            if ($this->excluir()) {
                //Registra o log da alteração
                Log::addNewLog(true, $this, null, null, true);
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
        $lista_resultados = parent::getListaAdmin(0);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach ($lista_resultados as $object) {
            $linkTipo = new LinkTipo();
            $linkTipo->setIdTable($object->id_link_tipo);
            $linkTipo->getByCod();

            $descricao = $linkTipo->descricao;
            $object->tipo_link = $descricao;

            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

            $object->botoes = '<a href="' . BASE_ADMIN_URL . '/' . $this->pagina_retorno . '?id=' . $object->{$this->key} . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

            $array_results[] = $object;
        }

        return json_encode($array_results);
    }

    /**
     * Insere o registro no banco de dados
     */
    public function insert()
    {
        $id_link_tipo = getRequest('id_link_tipo');
        $this->setIdLinkTipo($id_link_tipo);
        $this->setNome(getRequest("nome"));
        $this->setPosicao((getRequest("posicao") == '' ? '0' : getRequest("posicao")));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        $url = getRequest('url');
        if ($url == "#" || $url == "") {
            $url = "";
        } else {
            if (substr($url, 0, 4) != "http") {
                $url = "http://" . $url;
            }
        }
        $this->setUrl($url);

        $id = $this->incluir();
        $this->setIdTable($id);

        //Registra o log da inclusão
        Log::addNewLog(true, $this);

        return $id;
    }

    /**
     * Altera o registro no banco de dados
     */
    public function update($id)
    {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $id_link_tipo = getRequest('id_link_tipo');
        $this->setIdLinkTipo($id_link_tipo);
        $this->setNome(getRequest("nome"));
        $this->setPosicao((getRequest("posicao") == '' ? '0' : getRequest("posicao")));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        $url = getRequest('url');
        if ($url == "#" || $url == "") {
            $url = "";
        } else {
            if (substr($url, 0, 4) != "http") {
                $url = "http://" . $url;
            }
        }
        $this->setUrl($url);

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(true, $log_obj_original, $this);

        return $return;
    }

    /**
     * Recupera os Links pelo tipo
     *
     * @param $id_link_tipo
     * @param $status
     * @param $order_by
     * @return array|false
     */
    public function getLinksByTipo($id_link_tipo, $status, $order_by)
    {
        return parent::getLinksByTipo($id_link_tipo, $status, $order_by);
    }

}