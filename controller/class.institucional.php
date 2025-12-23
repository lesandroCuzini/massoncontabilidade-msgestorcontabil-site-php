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

class Institucional extends InstitucionalModel
{

    private $pagina_retorno = 'institucionais-form.html';

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
        } // Adiciona Complemento
        elseif ($acao == "addComplemento") {
            $id = getRequest("id_institucional");
            $tab = 2;
            if ($this->addComplemento($id)) {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok&id=" . $id . "&tab=" . $tab);
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=erro&id=" . $id . "&tab=" . $tab);
            }
            die;
        } // Remove Complemento
        elseif ($acao == "removerComplemento") {
            $retorno = $this->removerComplemento();

            exit(json_encode(array('retorno' => $retorno)));
        } // Listagem do Admin
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

            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

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

        $this->setTitulo(getRequest('titulo'));
        $this->setSubtitulo(getRequest('subtitulo'));

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));
        $this->setConteudo($conteudo);

        $this->setMetaTitulo(getRequest('meta_titulo'));
        $this->setMetaDescricao(getRequest('meta_descricao'));
        $this->setUrlRewrite(url_amigavel($this->getTitulo()));

        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        //Banner do Topo
        if (isset($_FILES['url_banner_topo']) && $_FILES['url_banner_topo']['tmp_name'] != "") {
            $nome_imagem_banner = 'banner_institucional_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_banner = uploadBanner($_FILES["url_banner_topo"], $_FILES['url_banner_topo']['tmp_name'], 'uploads/institucionais/banners/', $nome_imagem_banner, true, array('large/'), array(array(1920, 400)));
            $this->setUrlBannerTopo($imagem_banner);
        }

        $url_banner_topo = $this->getUrlBannerTopo();

        //Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_banner_topo) && $this->getUrlBannerTopo() != $url_banner_topo) {
            unlink('uploads/institucionais/banners/' . $url_banner_topo);
            unlink('uploads/institucionais/banners/large/' . $url_banner_topo);
        }

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    /**
     * Adiciona um Complemento
     *
     * @param $id
     * @return mixed
     */
    public function addComplemento($id)
    {
        $this->setIdTable($id);
        $this->getByCod();

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));

        $infoComplemento = array(
            'id_institucional' => $id,
            'titulo' => getRequest('titulo'),
            'conteudo' => $conteudo
        );

        $return = $this->addInfoComplemento($infoComplemento);

        return $return;
    }

    /**
     * Remove um Complemento
     *
     * @return bool
     */
    public function removerComplemento()
    {
        $id_registro = getRequest('id_registro');
        $id_complemento = getRequest('id_complemento');

        $complemento = parent::removerInfoComplemento($id_registro, $id_complemento);
        if (!$complemento)
            return false;

        return true;
    }

    /**
     * Retorna os complementos pelo ID
     *
     * @param $id_institucional
     * @return mixed
     */
    public function getComplementosByIdInstitucional($id_institucional)
    {
        return parent::getComplementosByIdInstitucional($id_institucional);
    }
}