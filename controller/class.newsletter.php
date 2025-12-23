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

class Newsletter extends NewsletterModel {

    public function __construct($submit = false)
    {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        // Add Newsletter
        if ($acao == "addNewsletter") {
            $this->addNewsletter();
        }
        // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        }
    }

    /**
     * Retorna a Listagem para o Admin
     */
    public function getListagemAdmin() {
        $lista_resultados = parent::getListaAdmin(0);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach($lista_resultados as $object) {
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/'.$this->pagina_retorno.'?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

            $array_results[] = $object;
        }
        return json_encode($array_results);
    }

    /**
     * Insere o registro no banco de dados
     */
    public function addNewsletter() {
        $this->setEmail(getRequest("newsletter"));

        $id = $this->incluir();
        $this->setIdTable($id);

        if ($id) {
            $retorno = array(
                'retorno' => 'sucesso'
            );
        } else {
            $retorno = array(
                'retorno' => 'erro'
            );
        }

        exit(json_encode($retorno));
    }

}