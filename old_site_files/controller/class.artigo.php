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

class Artigo extends ArtigoModel {
    
    private $pagina_retorno = 'artigos-form.html';

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
            $object->imagem_capa_artigo = ($object->imagem_capa_artigo != "") ? "<img src='".UPLOADS_URL."/artigos/".$object->imagem_capa_artigo."' width='100'>" : ' - ';
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

        date_default_timezone_set('America/Sao_Paulo');

        $this->setTituloArtigo(getRequest("titulo_artigo"));
        $this->setDescricaoArtigo(addslashes($_POST["descricao_artigo"]));
        if (isset($_FILES['imagem_capa_artigo']) && $_FILES['imagem_capa_artigo']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["imagem_capa_artigo"], 'uploads/', 'imagem_capa_artigo', true, array('artigos/'), array(array(300, 225)));
            $this->setImagemCapaArtigo($imagem_bg);
        }
        $this->setDataArtigo(date('Y-m-d H:i'));
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

        $this->setTituloArtigo(getRequest("titulo_artigo"));
        $this->setDescricaoArtigo(addslashes($_POST["descricao_artigo"]));
        if (isset($_FILES['imagem_capa_artigo']) && $_FILES['imagem_capa_artigo']['tmp_name'] != "") {
            $imagem_bg = uploadArquivo($_FILES["imagem_capa_artigo"], 'uploads/', 'imagem_capa_artigo', true, array('artigos/'), array(array(300, 225)));
            $this->setImagemCapaArtigo($imagem_bg);
        }
        $this->setDataArtigo(formatarData("bd2",getRequest("data_artigo")));
        $this->setStatus(getRequest("status")=="A"?"A":"I");

        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    /**
     * Retorna a Data Formata Artigo Ex( 26 de Outubro de 2017)
     * @string $data
     * @string string
     */
    public function getDataArtigoByData($data){
        $array_mes = array( '01' => "Janeiro" , '02' => "Fevereiro", '03' => "Março" , '04' => "Abril" , '05' => "Maio" , '06' => "Junho",
           '07' => "Julho" , '08' => "Agosto", '09' => "Setembro", '10' => "Outubro" , '11' => "Novembro" , '12' => "Dezembro");
        $data_explode = explode("/",$data);
        $ano = explode(" ",$data_explode[2]);

        return $data_explode[0]." de ".$array_mes[$data_explode[1]]." de ".$ano[0];
    }

    /**
     * Pegar Breve Descricao Artigo,
     * @string $descricao
     * @return string
     */
    public function getBreveDescricaoArtigo($descricao){
        $limite = 180;
        $descricao_strip = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($descricao));
        $descricao_strip = trim($descricao_strip);
        if(strlen($descricao_strip) > $limite){
            $descricao = substr($descricao_strip,0,$limite)."...";
        }
        else{
            $descricao =  substr($descricao_strip,0,$limite);
        }
        return $descricao;
    }

    /**
     * Retorna o Artigo por Id
     * @int $id
     * @return object
     */
    public function getArtigoById($id)
    {
        return parent::getArtigoById($id);
    }
}
