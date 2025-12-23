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

class Banner extends BannerModel
{
    private $pagina_retorno = 'banners-form.html';

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
                $this->updateCamposImagem($id_registro, 'url_imagem');
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
            $banner_url = '<img src="'.UPLOADS_URL.'/banners/small/'.$object->url_imagem.'" alt="" style="max-width:150px;" />';
            $object->url_imagem = $banner_url;

            $bannerTipo = new Bannertipo();
            $bannerTipo->setIdTable($object->id_banner_tipo);
            $bannerTipo->getByCod();

            $tipo_banner  = "<b>". $bannerTipo->descricao . "</b><br />";
            $tipo_banner .= (!empty($object->titulo) ? $object->titulo . "<br />" : "");
            $object->tipo_banner = $tipo_banner;

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
        date_default_timezone_set('America/Sao_Paulo');

        $id_banner_tipo = getRequest('id_banner_tipo');
        $this->setIdBannerTipo($id_banner_tipo);
        $this->setNomeBanner(getRequest("nome_banner"));
        $this->setPosicao((getRequest("posicao") == '' ? '0' : getRequest("posicao")));
        $this->setTarget(getRequest("target") == "_blank" ? "_blank" : "_self");
        $this->setAltImagem(getRequest("alt_imagem"));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        $linkBanner = getRequest('link_banner');
        if ($linkBanner == "#" || $linkBanner == "") {
            $linkBanner = "";
        } else {
            if (substr($linkBanner, 0, 4) != "http") {
                $linkBanner = "http://" . $linkBanner;
            }
        }
        $this->setLinkBanner($linkBanner);

        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $nome_imagem_bg = 'banner_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            if ($id_banner_tipo == 1) {
                $imagem_bg = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/banners/', $nome_imagem_bg, true, array('large/', 'small/'), array(array(1920, 700), array(600, 250)));
                $this->setUrlImagem($imagem_bg);
            } else {
                $imagem_bg = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/banners/', $nome_imagem_bg, true, array('large/', 'small/'), array(array(1920, 400), array(600, 250)));
                $this->setUrlImagem($imagem_bg);
            }
        }

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

        date_default_timezone_set('America/Sao_Paulo');

        $id_banner_tipo = getRequest('id_banner_tipo');
        $this->setIdBannerTipo($id_banner_tipo);
        $this->setNomeBanner(getRequest("nome_banner"));
        $this->setPosicao((getRequest("posicao") == '' ? '0' : getRequest("posicao")));
        $this->setTarget(getRequest("target") == "_blank" ? "_blank" : "_self");
        $this->setAltImagem(getRequest("alt_imagem"));
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        $linkBanner = getRequest('link_banner');
        if ($linkBanner == "#" || $linkBanner == "") {
            $linkBanner = "";
        } else {
            if (substr($linkBanner, 0, 4) != "http") {
                $linkBanner = "http://" . $linkBanner;
            }
        }
        $this->setLinkBanner($linkBanner);

        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $nome_imagem_bg = 'banner_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            if ($id_banner_tipo == 1) {
                $imagem_bg = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/banners/', $nome_imagem_bg, true, array('large/', 'small/'), array(array(1920, 700), array(600, 250)));
                $this->setUrlImagem($imagem_bg);
            } else {
                $imagem_bg = uploadBanner($_FILES["url_imagem"], $_FILES['url_imagem']['tmp_name'], 'uploads/banners/', $nome_imagem_bg, true, array('large/', 'small/'), array(array(1920, 400), array(600, 250)));
                $this->setUrlImagem($imagem_bg);
            }
        }

        $imagem_url = parent::buscaImagemNula('id_banner', $id,"url_imagem");

        $return = $this->alterar();

        $imagem_url_atual = parent::buscaImagemNula('id_banner', $id, "url_imagem");

        if ($imagem_url[0]->url_imagem != $imagem_url_atual[0]->url_imagem) {
            unlink('uploads/banners/' . $imagem_url[0]->url_imagem);
            unlink('uploads/banners/large/' . $imagem_url[0]->url_imagem);
            unlink('uploads/banners/small/' . $imagem_url[0]->url_imagem);
        }

        //Registra o log da alteração
        Log::addNewLog(true, $log_obj_original, $this);

        return $return;
    }

    /**
     * Recupera os banners pelo tipo
     *
     * @param $statys
     * @param $id_banner_tipo
     * @param $order_by
     */
    public function getBannersByTipo($status, $id_banner_tipo, $order_by)
    {
        return parent::getBannersByTipo($status, $id_banner_tipo, $order_by);
    }

}