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

class Servico extends ServicoModel
{

    private $pagina_retorno = 'servicos-form.html';

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
                $this->updateCamposImagem($id_registro, 'url_banner_topo');
                $this->updateCamposImagem($id_registro, 'url_imagem_destaque');
                $this->updateCamposImagem($id_registro, 'url_icone');
                //Registra o log da alteração
                Log::addNewLog(true, $this, null, null, true);
                exit('ok');
            } else {
                exit('erro');
            }
        } // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        } // Adiciona o Título do Complemento
        elseif ($acao == "addComplemento") {
            $id = getRequest("id_servico");
            $tab = 2;
            if ($this->addComplemento($id)) {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok&id=" . $id . "&tab=" . $tab);
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=erro&id=" . $id . "&tab=" . $tab);
            }
            die;
        } // Remove o Título do Complemento
        elseif ($acao == "removerComplemento") {
            $retorno = $this->removerComplemento();

            exit(json_encode(array('retorno' => $retorno)));
        } // Adiciona Item do Complemento
        elseif ($acao == "addComplementoItem") {
            $id = getRequest("id_servico");
            $tab = 2;
            if ($this->addComplementoItem($id)) {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=ok&id=" . $id . "&tab=" . $tab);
            } else {
                header("Location: " . BASE_ADMIN_URL . '/' . $this->pagina_retorno . "?msg=erro&id=" . $id . "&tab=" . $tab);
            }
            die;
        } // Exclui o item da complemento
        elseif ($acao == "removerComplementoItem") {
            $retorno = $this->removerComplementoItem();

            exit(json_encode(array('retorno' => $retorno)));
        }
    }

    /**
     * Rertorna a Listagem para o Admin
     *
     */
    public function getListagemAdmin()
    {
        $lista_resultados = parent::getListaAdmin(0);
        if ($lista_resultados === false)
            return false;

        $array_results = array();
        foreach ($lista_resultados as $object) {
            $object->destacar_home = $object->destacar_home == 'S' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

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
        $this->setDatahoraAtualizacao('1970-01-01 00:00:00');

        $this->setTitulo(getRequest('titulo'));
        $this->setSubtitulo(getRequest('subtitulo'));

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));
        $this->setConteudo($conteudo);

        $this->setMetaTitulo(getRequest('meta_titulo'));
        $this->setMetaDescricao(getRequest('meta_descricao'));
        $this->setUrlRewrite(url_amigavel($this->getTitulo()));
        $this->setDestacarHome(getRequest("destacar_home") == "S" ? "S" : "N");
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        $id = $this->incluir();
        $this->setIdTable($id);

        //Banner do Topo
        if (isset($_FILES['url_banner_topo']) && $_FILES['url_banner_topo']['tmp_name'] != "") {
            $nome_imagem_banner = 'banner_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_banner = uploadBanner($_FILES["url_banner_topo"], $_FILES['url_banner_topo']['tmp_name'], 'uploads/servicos/banners/', $nome_imagem_banner, true, array('large/'), array(array(1920, 400)));
            $this->setUrlBannerTopo($imagem_banner);
        }

        //Imagem de Destaque
        if (isset($_FILES['url_imagem_destaque']) && $_FILES['url_imagem_destaque']['tmp_name'] != "") {
            $nome_imagem_destaque = 'destaque_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_destaque = uploadBanner($_FILES["url_imagem_destaque"], $_FILES['url_imagem_destaque']['tmp_name'], 'uploads/servicos/file/', $nome_imagem_destaque, true, array('large/'), array(array(720, 480)));
            $this->setUrlImagemDestaque($imagem_destaque);
        }

        //Icone
        if (isset($_FILES['url_icone']) && $_FILES['url_icone']['tmp_name'] != "") {
            $nome_icone = 'icone_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $icone = uploadBanner($_FILES["url_icone"], $_FILES['url_icone']['tmp_name'], 'uploads/servicos/icones/', $nome_icone, true, array('large/'), array(array(59, 61)));
            $this->setUrlIcone($icone);
        }

        $return = $this->alterar();

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
        
        $url_banner_topo = $this->getUrlBannerTopo();
        $url_imagem_destaque = $this->getUrlImagemDestaque();
        $url_icone = $this->getUrlIcone();

        $this->setDatahoraCadastro(formatarData('bd2', $this->getDatahoraCadastro()));
        $this->setDatahoraAtualizacao(date('Y-m-d H:i:s'));

        $this->setTitulo(getRequest('titulo'));
        $this->setSubtitulo(getRequest('subtitulo'));

        $conteudo = str_replace("'", "`", html_entity_decode($_POST['conteudo']));
        $this->setConteudo($conteudo);

        $this->setMetaTitulo(getRequest('meta_titulo'));
        $this->setMetaDescricao(getRequest('meta_descricao'));
        $this->setUrlRewrite(url_amigavel($this->getTitulo()));
        $this->setDestacarHome(getRequest("destacar_home") == "S" ? "S" : "N");
        $this->setStatus(getRequest("status") == "A" ? "A" : "I");

        //Banner do Topo
        if (isset($_FILES['url_banner_topo']) && $_FILES['url_banner_topo']['tmp_name'] != "") {
            $nome_imagem_banner = 'banner_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_banner = uploadBanner($_FILES["url_banner_topo"], $_FILES['url_banner_topo']['tmp_name'], 'uploads/servicos/banners/', $nome_imagem_banner, true, array('large/'), array(array(1920, 400)));
            $this->setUrlBannerTopo($imagem_banner);
        }

        //Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_banner_topo) && $this->getUrlBannerTopo() != $url_banner_topo) {
            unlink('uploads/servicos/banners/' . $url_banner_topo);
            unlink('uploads/servicos/banners/large/' . $url_banner_topo);
        }

        //Imagem de Destaque
        if (isset($_FILES['url_imagem_destaque']) && $_FILES['url_imagem_destaque']['tmp_name'] != "") {
            $nome_imagem_destaque = 'destaque_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $imagem_destaque = uploadBanner($_FILES["url_imagem_destaque"], $_FILES['url_imagem_destaque']['tmp_name'], 'uploads/servicos/file/', $nome_imagem_destaque, true, array('large/'), array(array(720, 480)));
            $this->setUrlImagemDestaque($imagem_destaque);
        }

        //Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_imagem_destaque) && $this->getUrlImagemDestaque() != $url_imagem_destaque) {
            unlink('uploads/servicos/file/' . $url_imagem_destaque);
            unlink('uploads/servicos/file/large/' . $url_imagem_destaque);
        }

        //Icone
        if (isset($_FILES['url_icone']) && $_FILES['url_icone']['tmp_name'] != "") {
            $nome_icone = 'icone_srv_' . $id . '_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $icone = uploadBanner($_FILES["url_icone"], $_FILES['url_icone']['tmp_name'], 'uploads/servicos/icones/', $nome_icone, true, array('large/'), array(array(59, 61)));
            $this->setUrlIcone($icone);
        }

        //Caso tenha feito upload de uma nova imagem, remove a antiga
        if (!empty($url_icone) && $this->getUrlIcone() != $url_icone) {
            unlink('uploads/servicos/icones/' . $url_icone);
            unlink('uploads/servicos/icones/large/' . $url_icone);
        }

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    /**
     * Adiciona o Título do Complemento
     *
     * @param $id
     * @return bool|string
     */
    public function addComplemento($id)
    {
        $this->setIdTable($id);
        $this->getByCod();

        $infoComplemento = array(
            'id_servico' => $id,
            'titulo' => getRequest('titulo'),
            'subtitulo' => getRequest('subtitulo')
        );

        $return = $this->addInfoComplemento($infoComplemento);

        return $return;
    }

    /**
     * Remove o Título do Complemento
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
     * @param $id_servico
     * @return mixed
     */
    public function getComplementosByIdServico($id_servico)
    {
        return parent::getComplementosByIdServico($id_servico);
    }

    /**
     * Retorna as informações do título do complemento do serviço
     *
     * @param $id_servico
     * @return array|bool|object
     */
    public function getInfoComplementoByIdServico($id_servico) {
        $complemento = parent::getInfoComplementoByIdServico($id_servico);
        if (!$complemento)
            return false;

        return $complemento;
    }

    /**
     * Adiciona os Itens do Complemento
     *
     * @param $id
     * @return bool|string
     */
    public function addComplementoItem($id)
    {
        $this->setIdTable($id);
        $this->getByCod();

        // Ícone do Item do Complemento
        $url_icone = '';
        if (isset($_FILES['url_icone']) && $_FILES['url_icone']['tmp_name'] != "") {
            $nome_url_icone = 'icone_complemento_' . date('YmdHis') . str_replace(".", "", substr((string)microtime(), 1, 8));
            $url_icone = uploadBanner($_FILES["url_icone"], $_FILES['url_icone']['tmp_name'], 'uploads/servicos/icones/', $nome_url_icone, true, array('large/'), array(array(62, 62)));
        }

        $infoComplementoItem = array(
            'id_servico' => $id,
            'id_servico_complemento' => getRequest('id_servico_complemento'),
            'titulo' => getRequest('titulo'),
            'subtitulo' => getRequest('subtitulo'),
            'url_icone' => $url_icone
        );

        $return = $this->addInfoComplementoItem($infoComplementoItem);

        return $return;
    }

    /**
     * Remove o Item do Complemento
     *
     * @return bool
     */
    public function removerComplementoItem()
    {
        $id_registro = getRequest('id_registro');
        $id_complemento_item = getRequest('id_complemento_item');

        $infoComplementoItem = $this->getInfoComplementoItemById($id_registro, $id_complemento_item);
        if (!$infoComplementoItem)
            return false;

        $retornoRemocao = parent::removeComplementoItem($id_registro, $id_complemento_item);
        if ($retornoRemocao) {
            if (!empty($infoComplementoItem->url_icone)) {
                unlink('uploads/servicos/icones/'.$infoComplementoItem->url_icone);
            }
        }
        return true;
    }

    /**
     * Retorna os itens dos complementos
     *
     * @param $id_servico
     * @param $id_servico_complemento
     * @return false
     */
    public function getComplementosItens($id_servico, $id_servico_complemento)
    {
        $itens = parent::getComplementosItens($id_servico, $id_servico_complemento);
        if (!$itens)
            return false;

        foreach($itens as $key => $object) {
            // Ícone do complemento
            $itens[$key]->url_icone_complemento = '';
            if (!empty($object->url_icone)) {
                $itens[$key]->url_icone_complemento = UPLOADS_URL . '/servicos/icones/' . $object->url_icone;
            }
        }

        return $itens;
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

        return $result->id_servico;
    }

    /**
     * @param $destacar_home
     * @param $status
     * @return mixed
     */
    public function getServicosDestaques($destacar_home, $status)
    {
        return parent::getServicosDestaques($destacar_home, $status);
    }
}