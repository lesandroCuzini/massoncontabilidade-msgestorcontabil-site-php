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

class Contato extends ContatoModel
{

    private $pagina_retorno = 'contatos-form.html';

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
                header("Location: " . BASE_SITE_URL . "/contato?msg=ok");
            } else {
                header("Location: " . BASE_SITE_URL . "contato?msg=erro");
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
            $this->setIdTable(getRequest('id'));
            if ($this->excluir()) {
                //Registra o log da alteração
                // Log::addNewLog(false, $this, null, null, true);
                exit('ok');
            } else {
                exit('erro');
            }
        } // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        }
        elseif ($acao == "incluir_site") {
                $secret = "6LebzTsUAAAAAArc57pO8lzH5Ui3-YZRwZE3eClj";
                $ip = explode('.',$_SERVER['REMOTE_ADDR']);
                $ip = $ip[0].$ip[1].$ip[2].$ip[3];
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => [
                        'secret' => $secret,
                        'response' => $_POST['g-recaptcha-response'],
                        'remoteip' => $ip
                    ],
                    CURLOPT_RETURNTRANSFER => true
                ]);
                $output = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($output);
                if ($json->success == true){
                    $envio = $this->enviar();
                    if ($envio) header("Location: " . BASE_SITE_URL . "/contato?msg=ok"); else
                        header("Location: " . BASE_SITE_URL . "/contato?msg=erro");
                    die;
                }else{
                    header("Location: " . BASE_SITE_URL . "/contato?msg=erro");
                }

        }
    }

    /**
     * Rertorna a Listagem para o Admin
     */
    public function getListagemAdmin()
    {
        $lista_resultados = parent::getListaAdmin();
        if ($lista_resultados === false) return false;

        $array_results = array();
        foreach ($lista_resultados as $object) {
            $object->status = $object->status == 'A' ? '<span style="color:#00B285"><i class="fa fa-check" aria-hidden="true"></i></span>' : '<span style="color:#F00"><i class="fa fa-times" aria-hidden="true"></i></span>';
            $object->botoes = '<a href="' . BASE_ADMIN_URL . '/contatos-form.html?id=' . $object->{$this->key} . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            $array_results[] = $object;
        }

        return json_encode($array_results);
    }

    /* Insere o registro no banco de dados
     *
     */
    public function insert()
    {

        date_default_timezone_set('America/Sao_Paulo');
        $this->setDataCadastro(date('Y-m-d H:i:s'));
        $this->setNome(getRequest("nome"));
        $this->setEmail(getRequest("email"));
        $this->setTelefone(getRequest("telefone"));
        $this->setCelular(getRequest("celular"));
        $this->setMensagem(getRequest("mensagem"));
        $this->setStatus("P");
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
    public function update($id)
    {
        $this->setIdTable($id);
        $this->getByCod();

        $log_obj_original = clone($this); //Objeto de Log
        $this->setDataCadastro(formatarData("bd2",$this->getDataCadastro()));
        $this->setStatus(getRequest("status")=="A"?"A":"P");
        $return = $this->alterar();

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    protected function enviar()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $this->setDataCadastro(date('Y-m-d H:i:s'));
        $this->setNome(getRequest("nome"));
        $this->setEmail(getRequest("email"));
        $this->setTelefone(getRequest("telefone"));
        $this->setCelular(getRequest("celular"));
        $this->setMensagem(getRequest("mensagem"));
        $this->setStatus("P");
        //exit(var_dump(getRequest("telefone")));
        //$departamento = new AssuntoContato();
        //$selecionado = $departamento->getformulario($this->getAssunto());
        //exit(var_dump($selecionado[0]->email_responsavel));
        /*$departamento->getByCod();*/
        /*if($_SERVER["REMOTE_ADDR"] != "10.195.0.1"){*/
        $id = $this->incluir();
        $this->setIdTable($id);
        /*}*/

        ini_set("allow_url_fopen", 1);
        $mensagem = file_get_contents("view/mails/contato.html");


        $mensagem = str_replace("##base_url_site##", BASE_SITE_URL, $mensagem);
        $mensagem = str_replace("##hora##", date("d/m/Y H:i:s"), $mensagem);
        $mensagem = str_replace("##nome##", $this->getNome(), $mensagem);
        //$mensagem = str_replace("##ip##", $this->getIp(), $mensagem);
        $mensagem = str_replace("##assunto##", utf8_decode('Contato Ms Gestor Contabil Online'), $mensagem);
        $mensagem = str_replace("##email##", $this->getEmail(), $mensagem);
        $mensagem = str_replace("##telefone##", $this->getTelefone(), $mensagem);
        $mensagem = str_replace("##celular##", $this->getCelular(), $mensagem);
        //$mensagem = str_replace("##cidade##", $this->getCidade(), $mensagem);
        //$mensagem = str_replace("##estado##", $this->getEstado(), $mensagem);
        //$mensagem = str_replace("##assunto##", $this->getAssunto(), $mensagem);
        $mensagem = str_replace("##mensagem##", nl2br($this->getMensagem()), $mensagem);
       // exit(var_dump($mensagem));
        $mensagem = wordwrap($mensagem);
        $assunto = utf8_decode("Contato realizado no site.");
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8";
        $headers .= 'From: ' . $this->getEmail() . '>' . "\r\n";
        require_once("class.phpmailer.php");
        require_once("class.smtp.php");
        // Inicia a classe PHPMailer
        $mailer = new PHPMailer();
        $mailer->IsSMTP();        // Ativar SMTP
        $mailer->SMTPDebug = false;       // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
        $mailer->SMTPAuth = true;     // Autenticação ativada
        //$mailer->SMTPSecure = 'ssl';  // SSL REQUERIDO pelo GMail
        $mailer->Host = 'mail.msgestorcontabilonline.com.br'; // SMTP utilizado
        $mailer->Port =  587;
        $mailer->Username = 'contato.site@msgestorcontabilonline.com.br'; //Login de autenticação do SMTP
        $mailer->Password = 'sitegestor1234'; //Senha de autenticação do SMTP
        $mailer->FromName = "Contato Ms Gestor Contabil Online"; //Nome que será exibido para o destinatário
        $mailer->From = 'contato.site@msgestorcontabilonline.com.br'; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
        $mailer->AddAddress('contato@msgestorcontabilonline.com.br', utf8_decode('Contato Ms Gestor Contabil Online')); //Destinatários
        $mailer->IsHTML(true);
        $mailer->Subject = "Contato realizado no site.";
        $mailer->Body = $mensagem;
        $envio = $mailer->Send();
        //exit(var_dump($envio));
        return ($id && $envio);
    }
}
