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

class Banner extends BannerModel {
    
    private $pagina_retorno = 'banners-form.html';

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
        foreach($lista_resultados as $object) {
            $object->url_imagem = ($object->url_imagem != "") ? "<img src='".UPLOADS_URL."/banners/".$object->url_imagem."' width='100'>" : ' - ';
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';
            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/'.$this->pagina_retorno.'?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            
            $array_results[] = $object;
        }
            
        return json_encode($array_results);
    }

    /* Insere o registro no banco de dados
    *
    */
    public function insert() {
        $this->setPosicao(getRequest("posicao"));
        $this->setNome(getRequest("nome"));
        $linkBanner = $_POST['link_banner'];
        if (substr($linkBanner, 0, 4) != "http" && $linkBanner != "#" && $linkBanner != "") {
            $linkBanner = "http://" . $linkBanner;
        }

        $this->setLinkBanner($linkBanner);
        $this->setAltImagem(getRequest("alt_imagem"));
        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem"], 'uploads/', 'banner', true, array('banners/'), array(array(1200, 600)));
            $this->setUrlImagem($imagem_bg);
        }
        $this->setStatus(getRequest("status")=="A"?"A":"I");
        $this->setTarget(getRequest("target")=="_blank"?"_blank":"_self");

        $id = $this->incluir();
        $this->setIdTable($id);

        //Registra o log da inclusão
        Log::addNewLog(true, $this);

        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
     */
    public function update($id) {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $this->setNome(getRequest("nome"));
        $this->setAltImagem(getRequest("alt_imagem"));
        $linkBanner = $_POST['link_banner'];
        if (substr($linkBanner, 0, 4) != "http" && $linkBanner != "#" && $linkBanner != "") {
            $linkBanner = "http://" . $linkBanner;
        }

        $this->setLinkBanner($linkBanner);
        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem"], 'uploads/', 'banner', true, array('banners/'), array(array(1200, 600)));
            $this->setUrlImagem($imagem_bg);
        }
        $this->setPosicao(getRequest("posicao"));
        $this->setStatus(getRequest("status")=="A"?"A":"I");
        $this->setTarget(getRequest("target")=="_blank"?"_blank":"_self");

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }
    /**
     * Retornam os banners pelo tipo
     * 
     * @param int $id_tipo
     * @param int $quantidade
     * @param String $order
     * 
     * @return Array
     */
    public function getBannersByTipo($id_tipo, $quantidade = 0, $order = "") {
        return parent::getBannersByTipo($id_tipo, $quantidade, $order);
    }


}
