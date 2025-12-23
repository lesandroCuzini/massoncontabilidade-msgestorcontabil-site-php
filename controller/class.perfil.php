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

class Perfil extends PerfilModel {
    
    private $pagina_retorno = 'perfis-form.html';
    
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
                Log::addNewLog(false, $this, null, null, true);
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

    /* Insere o registro no banco de dados
     *
     */
    public function insert() {
        $this->setNome(getRequest("nome"));
        $this->setStatus(getRequest("status")=="A"?"A":"I");
        $id = $this->incluir();
        
        $this->setIdTable($id);
        
        //Paginas
        $pag_visualizar = $_POST['visualizar'];
        $pag_incluir    = $_POST['incluir'];
        $pag_alterar    = $_POST['alterar'];
        $pag_excluir    = $_POST['excluir'];
        $paginas = new Pagina();
        $lista_paginas = $paginas->listAll();
        parent::clearPaginasPerfil();
        if ($lista_paginas !== false) {
            foreach($lista_paginas as $dados_pagina){
                $visualizar = (isset($pag_visualizar[$dados_pagina->id_pagina]) && $pag_visualizar[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                $incluir = (isset($pag_incluir[$dados_pagina->id_pagina]) && $pag_incluir[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                $alterar = (isset($pag_alterar[$dados_pagina->id_pagina]) && $pag_alterar[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                $excluir = (isset($pag_excluir[$dados_pagina->id_pagina]) && $pag_excluir[$dados_pagina->id_pagina]=="S" ? "S" : "N");
               
                //Add a página para o perfil
                parent::addPaginaPerfil($dados_pagina->id_pagina, $visualizar, $incluir, $alterar, $excluir); 
            }
        }
        
        //Registra o log da inclusão            
        Log::addNewLog(true, $this);
        
        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
    */
    public function update($id) {
        $return = false;
        if($id != 1 || getSession("id_perfil", "info_user") == 1) {
            $this->setIdTable($id);
            $this->getByCod();
            $log_obj_original = clone($this); //Objeto de Log
            
            $this->setNome(getRequest("nome"));
            $this->setStatus(getRequest("status")=="A"?"A":"I");

            $return = $this->alterar();
            
            //Paginas
            $pag_visualizar = $_POST['visualizar'];
            $pag_incluir    = $_POST['incluir'];
            $pag_alterar    = $_POST['alterar'];
            $pag_excluir    = $_POST['excluir'];
            $paginas = new Pagina();
            $lista_paginas = $paginas->listAll('A');
            parent::clearPaginasPerfil();
            if ($lista_paginas !== false) {
                foreach($lista_paginas as $dados_pagina){
                    $visualizar = (isset($pag_visualizar[$dados_pagina->id_pagina]) && $pag_visualizar[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                    $incluir = (isset($pag_incluir[$dados_pagina->id_pagina]) && $pag_incluir[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                    $alterar = (isset($pag_alterar[$dados_pagina->id_pagina]) && $pag_alterar[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                    $excluir = (isset($pag_excluir[$dados_pagina->id_pagina]) && $pag_excluir[$dados_pagina->id_pagina]=="S" ? "S" : "N");
                   
                    //Add a página para o perfil
                    parent::addPaginaPerfil($dados_pagina->id_pagina, $visualizar, $incluir, $alterar, $excluir); 
                }
            }
            
            //Registra o log da alteração            
            //Log::addNewLog(false, $log_obj_original, $this);
        }
        return $return;
    }
    
    /**
     * Verifica se a permissão já foi concedida ao Perfil
     * 
     * @param int $id_perfil
     * @param int $id_pagina
     * @param String $tipo visualizar, incluir, alterar, excluir
     * 
     * @return boolean
     */
    public static function verificaPermissao($id_perfil, $id_pagina, $tipo) {
         $autorizacao = parent::verificaPermissao($id_perfil, $id_pagina, $tipo);
         return $autorizacao;
    }
  
    /**
     * Rertorna a Listagem para o Admin
     */
    public function getListagemAdmin() {
        $lista_resultados = parent::getListaAdmin();
        if ($lista_resultados === false)
            return false;
        
        $array_results = array();
        foreach($lista_resultados as $object) {
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';
            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/'.$this->pagina_retorno.'?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            
            
            $array_results[] = $object;
        }
            
        return json_encode($array_results);
    }
    
    /**
     * Retorna lista com todos perfis exceto Super Admin
     * @author 3dots
     *
     * @return array
     */
    public function getPerfisDisponiveis() {
        return parent::getPerfisDisponiveis();
    }
}
