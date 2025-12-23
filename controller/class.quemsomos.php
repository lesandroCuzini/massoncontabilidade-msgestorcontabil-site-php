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

class QuemSomos extends QuemSomosModel
{

    private $pagina_retorno = 'quem-somos-form.html';

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
        if ($acao == "alterar") {
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
        }
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
            $titulos  = "<b>". $object->nome_pagina . "</b><br />";
            $titulos .= (!empty($object->titulo_sobre) ? $object->titulo_sobre . "<br />" : "");
            $object->titulo = $titulos;

            $object->botoes = '<a href="' . BASE_ADMIN_URL . '/' . $this->pagina_retorno . '?id=' . $object->{$this->key} . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

            $array_results[] = $object;
        }

        return json_encode($array_results);
    }

    /**
     * Altera o registro no banco de dados
     *
     * @param $id
     * @return bool
     */
    public function update($id)
    {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $this->setDatahoraCadastro(formatarData('bd2', $this->getDatahoraCadastro()));
        $this->setDatahoraAtualizacao(date('Y-m-d H:i:s'));
        $this->setNomePagina(getRequest('nome_pagina'));

        $descricaoHome = str_replace("'", "`", html_entity_decode($_POST['descricao_home']));
        $this->setDescricaoHome($descricaoHome);

        $this->setTituloSobre(getRequest('titulo_sobre'));
        $this->setTituloNossoDiferencial(getRequest('titulo_nosso_diferencial'));
        $this->setTituloComoFunciona(getRequest('titulo_como_funciona'));

        $descricaoSobre = str_replace("'", "`", html_entity_decode($_POST['descricao_sobre']));
        $this->setDescricaoSobre($descricaoSobre);

        $descricaoNossoDiferencial = str_replace("'", "`", html_entity_decode($_POST['descricao_nosso_diferencial']));
        $this->setDescricaoNossoDiferencial($descricaoNossoDiferencial);

        $descricaoComoFunciona = str_replace("'", "`", html_entity_decode($_POST['descricao_como_funciona']));
        $this->setDescricaoComoFunciona($descricaoComoFunciona);

        // Imagem Sobre Nós
        if (isset($_FILES['url_imagem_sobre']) && $_FILES['url_imagem_sobre']['tmp_name'] != "") {
            $nome_imagem_sobre = 'quem_somos_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_sobre = uploadBanner($_FILES["url_imagem_sobre"], $_FILES['url_imagem_sobre']['tmp_name'], 'uploads/quem-somos/', $nome_imagem_sobre, true, array('small/'), array(array(360, 240)));
            $this->setUrlImagemSobre($imagem_sobre);
        }

        // Imagem Nosso Diferencial
        if (isset($_FILES['url_imagem_nosso_diferencial']) && $_FILES['url_imagem_nosso_diferencial']['tmp_name'] != "") {
            $nome_imagem_diferencial = 'nosso_diferencial_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_diferencial = uploadBanner($_FILES["url_imagem_nosso_diferencial"], $_FILES['url_imagem_nosso_diferencial']['tmp_name'], 'uploads/quem-somos/', $nome_imagem_diferencial, true, array('small/'), array(array(360, 240)));
            $this->setUrlImagemNossoDiferencial($imagem_diferencial);
        }

        // Imagem Como Funciona
        if (isset($_FILES['url_imagem_como_funciona']) && $_FILES['url_imagem_como_funciona']['tmp_name'] != "") {
            $nome_imagem_como_funciona = 'como_funciona_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_como_funciona = uploadBanner($_FILES["url_imagem_como_funciona"], $_FILES['url_imagem_como_funciona']['tmp_name'], 'uploads/quem-somos/', $nome_imagem_como_funciona, true, array('small/'), array(array(360, 240)));
            $this->setUrlImagemComoFunciona($imagem_como_funciona);
        }

        $url_imagem_sobre = $this->getUrlImagemSobre();
        $url_imagem_nosso_diferencial = $this->getUrlImagemNossoDiferencial();
        $url_imagem_como_funciona = $this->getUrlImagemComoFunciona();

        // Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem_sobre) && $this->getUrlImagemSobre() != $url_imagem_sobre) {
            unlink('uploads/quem-somos/' . $url_imagem_sobre);
            unlink('uploads/quem-somos/small/' . $url_imagem_sobre);
        }

        // Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem_nosso_diferencial) && $this->getUrlImagemNossoDiferencial() != $url_imagem_nosso_diferencial) {
            unlink('uploads/quem-somos/' . $url_imagem_nosso_diferencial);
            unlink('uploads/quem-somos/small/' . $url_imagem_nosso_diferencial);
        }

        // Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem_como_funciona) && $this->getUrlImagemComoFunciona() != $url_imagem_como_funciona) {
            unlink('uploads/quem-somos/' . $url_imagem_como_funciona);
            unlink('uploads/quem-somos/small/' . $url_imagem_como_funciona);
        }

        $this->setUrlRewrite(url_amigavel($this->getNomePagina()));

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }
}