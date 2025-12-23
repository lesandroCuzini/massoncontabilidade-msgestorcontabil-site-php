<?php

/*
 * 2017 Innovare Company
 *
 * DISCLAIMER
 *
 * Do not edit or add anything to this file
 * If you need to edit this file consult Innovare Company.
 *
 *  @author Innovare Company <contato@innovarecompany.com.br>
 *  @copyright 2017 Innovare Company
 *  @version  1.0
 *
 */

class LangConstante extends LangConstanteModel {

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {

        // Nova conta
        /*if ($acao == "new_account") {
            $id_plano = getRequest("id_plano");
            $retorno = $this->novaConta();
            if ($retorno == "ok") 
                header("Location: " . URL_RAIZ . $this->getUrlEmpresa()."/dashboard");
            else
                header("Location: " . BASE_SITE_URL . "/contratar/?plano=".$id_plano."&msg=".$retorno);
            die;
        }
        //Login Administrador
        elseif ($acao == "login_admin") {
            $retorno = $this->loginAdministrador();
            if ($retorno != "erro" && $retorno != "bloqueado" && $retorno != "pendente") 
                header("Location: " . URL_RAIZ . $retorno . "/dashboard");
            else
                header("Location: " . BASE_SITE_URL . "login/?msg=".$retorno);
            die;
        }*/
    }

    /**
     * Retornam todas configurações pré-definidas começam com o parametro
     * @param int $id
     *
     * @return Array
     */
    public function getConstantesByIdLang($id)
    {
        $retorno = parent::getConstantesByIdLang($id);

        if ($retorno === false)
            return false;

        return $retorno;
    }



}
