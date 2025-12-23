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

class Texto extends TextoModel {
    
    private $pagina_retorno = 'textos-form.html';

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        switch ($acao){
            case "incluir":
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
                break;
            case "alterar":
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
                break;
            case "excluir":
                $this->setIdTable(getRequest('id'));
                if ($this->excluir()) {
                    //Registra o log da alteração
                    // Log::addNewLog(false, $this, null, null, true);
                    exit('ok');
                } else {
                    exit('erro');
                }
            case "getlistagem":
                exit($this->getListagemAdmin());
            case "upload":
                $ProdutoFoto = new TextoFoto();
                $resultado = $ProdutoFoto->insereFotos(getRequest('id_registro'));
                exit($resultado);
            case "imagens-enviadas":
                $id_registro = getRequest('id_registro');
                $insereFotos = new TextoFoto();
                $resultado = $insereFotos->getFotosEnviadas($id_registro);
                $dados = json_encode($resultado);
                exit($dados);
            case "deleta_imagem":
                $id_imagem_foto = getRequest('data_id_objeto_foto');
                $id_objeto = getRequest('id_registro');
                $deletaFoto = new TextoFoto();
                $resultado = $deletaFoto->deletaImagem($id_imagem_foto, $id_objeto);
                exit($resultado);

            case "apagar-imagem-texto":
                $id_texto = getRequest('id_texto');
                $campo = getRequest('campo');
                $update = $this->updateCamposImagem($id_texto,$campo,'');
                if($update){
                    exit('1');
                }
                exit('0');
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
            $object->url_imagem = ($object->url_imagem != "") ? '<img style="width:100px" src="'.UPLOADS_URL.'/textos/'.$object->url_imagem .'">' : "-";
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
        $this->setMetaTitle(getRequest("meta_title"));
        $this->setMetaDescription(addslashes(getRequest("meta_description")));
        $this->setMetaKeywords(getRequest("meta_keywords"));
        $this->setTitulo(getRequest("titulo"));
        $this->setSubtitulo(getRequest("subtitulo"));
        $this->setMissao(getRequest("missao"));
        $this->setVisao(getRequest("visao"));
        $this->setValores(getRequest("valores"));
        $this->setDescricao(addslashes($_POST['descricao']));

        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem"], 'uploads/', 'texto', true, array('textos/'), array(array(1920, 500)));
            $this->setUrlImagem($imagem_bg);
        }
        if (isset($_FILES['url_imagem_home']) && $_FILES['url_imagem_home']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem_home"], 'uploads/', 'texto', true, array('textos/'), array(array(960, 960)));
            $this->setUrlImagemHome($imagem_bg);
        }
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
        $this->setMetaTitle(getRequest("meta_title"));
        $this->setMetaDescription(htmlspecialchars(getRequest("meta_description")));
        $this->setMetaKeywords(getRequest("meta_keywords"));
        $this->setTitulo(getRequest("titulo"));
        $this->setSubtitulo(getRequest("subtitulo"));
        $this->setMissao(getRequest("missao"));
        $this->setVisao(getRequest("visao"));
        $this->setValores(getRequest("valores"));
        $this->setDescricao(addslashes($_POST['descricao']));

        if (isset($_FILES['url_imagem']) && $_FILES['url_imagem']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem"], 'uploads/', 'texto', true, array('textos/'), array(array(1920, 500)));
            $this->setUrlImagem($imagem_bg);
        }
        if (isset($_FILES['url_imagem_home']) && $_FILES['url_imagem_home']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["url_imagem_home"], 'uploads/', 'texto', true, array('textos/'), array(array(960, 960)));
            $this->setUrlImagemHome($imagem_bg);
        }
        $this->setStatus(getRequest("status")=="A"?"A":"I");
        
        $return = $this->alterar();
            
        //Registra o log da alteração            
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }
    public function updateCamposImagem($id_texto, $campo, $valor)
    {
        return parent::updateCamposImagem($id_texto, $campo, $valor);
    }
}
