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

class Artigo extends ArtigoModel
{

    private $pagina_retorno = 'artigos-form.html';

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
        } // Altera o registro
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
                $this->updateCamposImagem($id_registro, 'url_imagem');
                $this->updateCamposImagem($id_registro, 'url_imagem_capa');
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
            $imagem_capa = '<img src="' .UPLOADS_URL. '/artigos/small/' .$object->url_imagem_capa. '" alt="" style="max-width:150px;" />';
            $object->url_imagem_capa = $imagem_capa;

            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

            $object->botoes = '<a href="' . BASE_ADMIN_URL . '/' . $this->pagina_retorno . '?id=' . $object->{$this->key} . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

            $array_results[] = $object;
        }

        return json_encode($array_results);
    }

    /**
     * Insere o registro no banco de dados
     *
     * @return bool
     */
    public function insert()
    {
        date_default_timezone_set('America/Sao_Paulo');

        $this->setDatahoraCadastro(date('Y-m-d H:i:s'));
        $this->setDataArtigo(getRequest('data_artigo'));
        $this->setTitulo(getRequest('titulo'));
        $this->setDescricao(getRequest('descricao'));

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));
        $this->setConteudo($conteudo);

        $this->setMetaTitulo(getRequest('meta_titulo'));
        $this->setMetaDescricao(getRequest('meta_descricao'));
        $this->setUrlRewrite(url_amigavel($this->getTitulo()));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        // Imagem
        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $nome_imagem = 'artigo_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/artigos/', $nome_imagem, true, array('large/', 'small/'), array(array(768, 276), array(480, 172)));
            $this->setUrlImagem($imagem);
        }

        // Imagem de Capa
        if (isset($_FILES['url_imagem_capa']) && $_FILES['url_imagem_capa']['tmp_name'] != "") {
            $nome_imagem_capa = 'artigo_capa_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_capa = uploadBanner($_FILES["url_imagem_capa"], $_FILES['url_imagem_capa']['tmp_name'], 'uploads/artigos/', $nome_imagem_capa, true, array('small/'), array(array(370, 240)));
            $this->setUrlImagemCapa($imagem_capa);
        }

        $id = $this->incluir();
        $this->setIdTable($id);

        //Registra o log da alteração
        Log::addNewLog(true, $this);

        return $id;
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

        date_default_timezone_set('America/Sao_Paulo');

        $this->setDatahoraCadastro(formatarData('bd2', $this->getDatahoraCadastro()));
        $this->setDataArtigo(getRequest('data_artigo'));
        $this->setTitulo(getRequest('titulo'));
        $this->setDescricao(getRequest('descricao'));

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));
        $this->setConteudo($conteudo);

        $this->setMetaTitulo(getRequest('meta_titulo'));
        $this->setMetaDescricao(getRequest('meta_descricao'));
        $this->setUrlRewrite(url_amigavel($this->getTitulo()));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        // Imagem
        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $nome_imagem = 'artigo_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/artigos/', $nome_imagem, true, array('large/', 'small/'), array(array(768, 276), array(480, 172)));
            $this->setUrlImagem($imagem);
        }

        $url_imagem = $this->getUrlImagem();

        // Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem) && $this->getUrlImagem() != $url_imagem) {
            unlink('uploads/artigos/' . $url_imagem);
            unlink('uploads/artigos/small/' . $url_imagem);
            unlink('uploads/artigos/large/' . $url_imagem);
        }

        // Imagem de Capa
        if (isset($_FILES['url_imagem_capa']) && $_FILES['url_imagem_capa']['tmp_name'] != "") {
            $nome_imagem_capa = 'artigo_capa_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_capa = uploadBanner($_FILES["url_imagem_capa"], $_FILES['url_imagem_capa']['tmp_name'], 'uploads/artigos/', $nome_imagem_capa, true, array('small/'), array(array(370, 240)));
            $this->setUrlImagemCapa($imagem_capa);
        }

        $url_imagem_capa = $this->getUrlImagemCapa();

        // Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem_capa) && $this->getUrlImagemCapa() != $url_imagem_capa) {
            unlink('uploads/artigos/' . $url_imagem_capa);
            unlink('uploads/artigos/small/' . $url_imagem_capa);
            unlink('uploads/artigos/large/' . $url_imagem_capa);
        }

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    /**
     * Lista os artigos conforme a data de publicaçao
     *
     * @param $data_atual
     * @param $status
     * @return mixed
     */
    public function getArtigosByDataArtigo($data_atual, $status)
    {
        return parent::getArtigosByDataArtigo($data_atual, $status);
    }

    /**
     * Retorna o ID através da URL amigável
     *
     * @param $url_rewrite
     *
     * @return integer|false
     */
    public function getIdByUrlRewrite($url_rewrite)
    {
        $result = parent::getIdByUrlRewrite($url_rewrite);
        if (!$result)
            return false;

        return $result->id_artigo;
    }
}