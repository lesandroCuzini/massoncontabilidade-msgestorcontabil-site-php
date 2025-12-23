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

class Contato extends ContatoModel {
    
    private $pagina_retorno = 'contato-form.html';

    public function __construct($submit = false) {
        parent::__construct($submit);
    }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
        // Altera o registro selecionado
        if ($acao == "alterar") {
            $id = getRequest("id_registro");
            if ($this->update($id)) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . "/".$this->pagina_retorno."?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . "/".$this->pagina_retorno."?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/?msg=erro&id=' . $id);
            }
            die;
        } // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        } // Envia Formulário do Site
        elseif ($acao == "setContato") {
            $this->setContato();
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
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';

            $object->botoes = '<a href="'.BASE_ADMIN_URL.'/'.$this->pagina_retorno.'?id='.$object->{$this->key}.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            
            $array_results[] = $object;
        }
            
        return json_encode($array_results);
    }

    /**
     * Insere o registro no banco de dados
     */
    public function setContato()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $this->setDatahoraContato(date('Y-m-d H:i:s'));
        $this->setIpRegistro($_SERVER['REMOTE_ADDR']);
        $this->setNome(getRequest('nome'));
        $this->setEmail(getRequest('email'));
        $this->setTelefone(getRequest('telefone'));
        $this->setCelular(getRequest('celular'));
        $this->setMensagem(getRequest('mensagem'));
        $this->setDatahoraAlteracaoStatus(date('Y-m-d H:i:s'));
        $this->setStatus("P");

        $id = $this->incluir();
        $this->setIdTable($id);
        Log::addNewLog(true, $this);

        if ($id) {
//            ini_set("allow_url_fopen", 1);
//            $mensagem = file_get_contents("view/mails/fale_conosco.html");

//            $mensagem = str_replace("##base_url_site##", BASE_SITE_URL, $mensagem);
//            $mensagem = str_replace("##logotipo##", UPLOADS_URL . "/temas/logotipo/" . SITE_LOGOTIPO, $mensagem);
//            $mensagem = str_replace("##hora##", date("d/m/Y H:i:s"), $mensagem);
//            $mensagem = str_replace("##nome##", $this->getNome(), $mensagem);
//            $mensagem = str_replace("##email##", $this->getEmail(), $mensagem);
//            $mensagem = str_replace("##telefone##", $this->getTelefone(), $mensagem);
//            $mensagem = str_replace("##endereco##", $this->getCidade().' / '.$this->getEstado(), $mensagem);
//            $mensagem = str_replace("##assunto##", $assunto->descricao, $mensagem);
//            $mensagem = str_replace("##mensagem##", htmlentities($this->getMensagem()), $mensagem);
//            $mensagem = wordwrap($mensagem);

//            $headers = 'MIME-Version: 1.0' . "\r\n";
//            $headers .= "Content-Type: text/html; charset=UTF-8";
//            $headers .= 'From: ' . $this->getEmail() . '>' . "\r\n";

//            require_once("phpmailer/PHPMailer.php");
//            require_once("phpmailer/SMTP.php");

//            // Inicia a classe PHPMailer
//            $mailer = new PHPMailer();
//            $mailer->isSMTP();              // Ativar SMTP
//            $mailer->SMTPDebug = 2;     // Debugar: 1=erros e mensagens, 2=mensagens apenas
//            $mailer->SMTPAuth = true;
//            $mailer->Mailer = "smtp";
//            $mailer->SMTPSecure = 'ssl';
//            $mailer->Host = 'mail.grupioni.com.br';
//            $mailer->Port = 465;
//            $mailer->Username = 'site@grupioni.com.br';
//            $mailer->Password = 'ym[YFtLDAHcg';

//            $mailer->FromName = "Contato";
//            $mailer->From = 'site@grupioni.com.br';
//            $mailer->AddAddress('dheysonws@gmail.com', 'Contato Mensagem');
//            //$mailer->AddCC('email@email.com.br');
//            $mailer->IsHTML(true);
//            $mailer->Subject = "Contato realizado no site.";
//            $mailer->Body = utf8_decode($mensagem);
//            $envio = $mailer->Send();

//            if($envio && $id) {
            $retorno = array(
                'retorno' => 'sucesso'
            );
//            } else {
//                $retorno = array(
//                    'retorno' => 'falha'
//                );
//            }
        } else {
            $retorno = array(
                'retorno' => 'falha'
            );
        }

        exit(json_encode($retorno));
    }

    /**
     * Altera o registro no banco de dados
     *
     */
    public function update($id)
    {
        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        $this->setDatahoraContato(formatarData("bd2", getRequest('datahora_contato')));
        $this->setDatahoraAlteracaoStatus(date('Y-m-d H:i:s'));
        $this->setStatus(getRequest("status") == "A" ? "A" : "P");

        $return = $this->alterar();

        //Registra o log da alteração            
        Log::addNewLog(true, $log_obj_original, $this);

        return $return;
    }
}
