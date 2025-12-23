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

class NossaEquipe extends NossaEquipeModel {
    
    private $pagina_retorno = 'nossa_equipe-form.html';

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
            $object->foto = ($object->foto != "") ? "<img src='".UPLOADS_URL."/nossa-equipe/".$object->foto."' width='80'>" : ' - ';
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

        $this->setNome(getRequest("nome"));
        if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["foto"], 'uploads/', 'nossa_equipe', true, array('nossa-equipe/'), array(array(300, 300)));
            $this->setFoto($imagem_bg);
        }
        $this->setFuncao(getRequest("funcao"));

        $link_facebook = getRequest("link_facebook");
        if($link_facebook == ""){$link_facebook = "#";}

        $link_twitter = getRequest("link_twitter");
        if($link_twitter == ""){$link_twitter = "#";}

        $link_linkedin = getRequest("link_linkedin");
        if($link_linkedin == ""){$link_linkedin = "#";}

        $link_google_plus = getRequest("link_google_plus");
        if($link_google_plus == ""){$link_google_plus = "#";}

        $this->setLinkFacebook($link_facebook);
        $this->setLinkTwitter($link_twitter);
        $this->setLinkLinkedin($link_linkedin);
        $this->setLinkGooglePlus($link_google_plus);
        $this->setExibirHome(getRequest("exibir_home")=="S"?"S":"N");
        $this->setPosicao(getRequest("posicao"));
        $this->setStatus(getRequest("status")=="A"?"A":"I");

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
        if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["foto"], 'uploads/', 'nossa_equipe', true, array('nossa-equipe/'), array(array(300, 300)));
            $this->setFoto($imagem_bg);
        }
        $this->setFuncao(getRequest("funcao"));

        $link_facebook = getRequest("link_facebook");
        if($link_facebook == ""){$link_facebook = "#";}

        $link_twitter = getRequest("link_twitter");
        if($link_twitter == ""){$link_twitter = "#";}

        $link_linkedin = getRequest("link_linkedin");
        if($link_linkedin == ""){$link_linkedin = "#";}

        $link_google_plus = getRequest("link_google_plus");
        if($link_google_plus == ""){$link_google_plus = "#";}

        $this->setLinkFacebook($link_facebook);
        $this->setLinkTwitter($link_twitter);
        $this->setLinkLinkedin($link_linkedin);
        $this->setLinkGooglePlus($link_google_plus);
        $this->setExibirHome(getRequest("exibir_home")=="S"?"S":"N");
        $this->setPosicao(getRequest("posicao"));
        $this->setStatus(getRequest("status")=="A"?"A":"I");

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }
    /*
     * Retorna os Integrantes da Nossa Equipe Filtrando Por Exibir Home e Status , organizando por posicao
     */
    public function getNossaEquipeHome()
    {
        return parent::getNossaEquipeHome();
    }
}
