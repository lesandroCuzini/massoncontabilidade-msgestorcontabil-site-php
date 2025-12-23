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

class Noticia extends NoticiaModel {
    
    private $pagina_retorno = 'noticias-form.html';
    private $limite_caracteres_home = 120;

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        /*// Inclui o registro
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
        }*/
    }

    /**
     * Retornam todas notícias cadastradas e ativas no site
     *
     * @param int $rpp
     * @param int $pag_atual
     *
     * @return Array
     */
    public function getAllNoticias($rpp = 5, $pag_atual = 1) {
        $lista_resultados = parent::getAllNoticias($rpp, $pag_atual);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach($lista_resultados as $object) {
            $object->url_imagem_capa = UPLOADS_URL.'/noticias/'.$object->url_imagem_capa;
            $object->url_imagem_noticia = UPLOADS_URL.'/noticias/'.$object->url_imagem_noticia;
            $object->resumo_home = (strlen($object->subtitulo) > $this->limite_caracteres_home ? substr($object->subtitulo, 0, $this->limite_caracteres_home).'...' : $object->subtitulo);
            $object->url_noticia = BASE_SITE_URL.'/noticia/'.$object->url_amigavel;

            $array_results[] = $object;
        }

        return $array_results;
    }

    /**
     * Retorna o total de notícias cadastradas e ativas
     *
     * @return Array
     */
    public function getTotalNoticias() {
        return parent::getTotalNoticias();
    }

    /**
     * Retornam as noticias da home
     *
     * @param int $quantidade
     *
     * @return Array
     */
    public function getNoticiasHome($quantidade = 0) {
        $lista_resultados = parent::getNoticiasHome($quantidade);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach($lista_resultados as $object) {
            $object->url_imagem_capa = UPLOADS_URL.'/noticias/'.$object->url_imagem_capa;
            $object->url_imagem_noticia = UPLOADS_URL.'/noticias/'.$object->url_imagem_noticia;
            $object->resumo_home = (strlen($object->subtitulo) > $this->limite_caracteres_home ? substr($object->subtitulo, 0, $this->limite_caracteres_home).'...' : $object->subtitulo);
            $object->url_noticia = BASE_SITE_URL.'/noticia/'.$object->url_amigavel;

            $array_results[] = $object;
        }

        return $array_results;

    }

    /**
     * Retorna o registro através de seu ID
     *
     * @param int $id_registro
     *
     * @return Array
     */
    public function getRegistroByLang($id_registro) {
        $info_registro = parent::getRegistroByLang($id_registro);
        if ($info_registro === false)
            return false;

        $this->id_table = $info_registro->id_noticia;
        $this->id_lang = $info_registro->id_lang;
        $this->datahora_cadastro = $info_registro->datahora_cadastro;
        $this->data_noticia = $info_registro->data_noticia;
        $this->destacar_home = $info_registro->destacar_home;
        if ($info_registro->url_imagem_capa != "")
            $this->url_imagem_capa = UPLOADS_URL.'/noticias/'.$info_registro->url_imagem_capa;
        if ($info_registro->url_imagem_noticia)
            $this->url_imagem_noticia = UPLOADS_URL.'/noticias/'.$info_registro->url_imagem_noticia;
        $this->titulo = $info_registro->titulo;
        $this->subtitulo = $info_registro->subtitulo;
        $this->descricao = $info_registro->descricao;
        $this->seo_title = $info_registro->seo_title;
        $this->seo_description = $info_registro->seo_description;
        $this->seo_keywords = $info_registro->seo_keywords;
        $this->url_amigavel = BASE_SITE_URL.'/noticia/'.$info_registro->url_amigavel;
        $this->status = $info_registro->status;

        return true;
    }


}
