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

class UsuarioAdmin extends UsuarioAdminModel {

    private $pagina_retorno = 'usuarios-form.html';
    
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
        // Altera a senha do registro selecionado
        elseif ($acao == "alterar_senha") {
            $id = getRequest("id_registro");
            if ($this->alterarSenha($id)) {
                $acao_posterior = getRequest('acao_posterior');
                if ($acao_posterior == "new") {
                    header("Location: " . BASE_ADMIN_URL . '/senha-lista.html'."?msg=ok");
                } else {
                    header("Location: " . BASE_ADMIN_URL . '/senha-lista.html'."?msg=ok&id=" . $id);
                }
            } else {
                header("Location: " . BASE_ADMIN_URL . '/senha-lista.html'."?msg=erro&id=" . $id);
            }
            die;
        }
        // Apaga um registro do banco de dados
        elseif ($acao == "excluir") {
            $id = getRequest('id');

            //Somente o usuário 1 pode alterar ou excluir o usuário 1
            if($id != 1 || ($id == 1 && getSession('id', 'user') == 1)) {
                $this->setIdTable(getRequest('id'));
                if ($this->excluir()) {
                    //Registra o log da alteração
                    Log::addNewLog(false, $this, null, null, true);
                    exit('ok');
                } else {
                    exit('erro');
                }
            } else {
                exit('erro');
            }
        }
        /// Verifica se foi solicitado Login
        elseif ($acao == "login") {
            $retorno = $this->verificaLogin();
            exit($retorno);
        }
        // Verifica se foi solicitado Logout
        elseif ($acao == "logout") {
            $this->setSessionsLogin("logout");
            header("Location: " . BASE_ADMIN_URL . "/login.html");
        }
        // Retorna a listagem de registros no admin
        elseif ($acao == "getlistagem") {
            exit($this->getListagemAdmin());
        }
        // Retorna a listagem de registros no admin
        elseif ($acao == "setCiente") {
            $this->setCiente(getRequest('id_usuario'),getRequest('id_ciente'));
            header("Location: " . BASE_ADMIN_URL . "/usuarios-form.html?id=".getRequest('id_usuario'));
            die;
        }
    }

    /**
     * Verifica o login do usuário
     * 
     * @return Array
     */
    public function verificaLogin() {
        //Registra a quantidade de tentativas de login
        $qtde_tentativas = isset($_SESSION['erros_login']) ? $_SESSION['erros_login']+1 : 1;
        $_SESSION['erros_login'] = $qtde_tentativas;
        
        //Verifica se o captcha foi preenchido se necessáriox
        if (isset($_POST['g-recaptcha-response']) && getRequest('validar_captcha') == 'sim') {
            $captcha = $_POST['g-recaptcha-response'];
            $return_erro_captcha = 'erro_captcha';
            if ($captcha == '') {
                $array_retorno = array(
                    'sucesso' => false,
                    'qtde_tentativas' => $qtde_tentativas,
                    'erro' => $return_erro_captcha,
                );
                return json_encode($array_retorno);
            } else {
                //Classe de verificação do captcha
                include_once('re-captcha.php');
                $secret_key = CAPTCHA_SECRET_KEY;
                $reCaptcha = new ReCaptcha($secret_key);
                $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $captcha);
                if ($response == null || !$response->success) {
                    $array_retorno = array(
                        'sucesso' => false,
                        'qtde_tentativas' => $qtde_tentativas,
                        'erro' => $return_erro_captcha,
                    );
                    return json_encode($array_retorno);
                }
            }
        }
        
        //Verifica os dados do login
        $login = getRequest('login');
        $senha = md5(sha1(getRequest('senha')));
        $retorno = parent::verifyLogin($login, $senha);
        if ($retorno !== false) {
            if(isset($retorno->restricoes_horarios)) {
                if ($retorno->restricoes_horarios == 'S') {
                    $dias_autorizados = explode(';', $retorno->dias_autorizados);
                    $diasemana_numero = date('w', strtotime(date('Y-m-d'))); //0=domingo ... 6=sabado
                    if ($dias_autorizados[$diasemana_numero] == "N") {
                        //Registra o log de acesso proibido
                        $str_log_acesso = 'Tentativa de acesso em <strong>dia não autorizado</strong> registrado em <strong>' . date('d/m/Y') . ' às ' . date('H:i:s') . '</strong><br />';
                        $str_log_acesso .= 'IP no momento da tentativa: <strong>' . $_SERVER['REMOTE_ADDR'] . '</strong><br />';
                        $str_log_acesso .= 'Tentativa de Acesso por: <strong>' . $_SERVER['HTTP_USER_AGENT'] . '</strong><br /><br />';
                        parent::setNewAcessoRestrito($retorno->id_usuario, $str_log_acesso);
                        $array_retorno = array(
                            'sucesso' => false,
                            'qtde_tentativas' => $qtde_tentativas,
                            'ip_remoto' => $_SERVER['REMOTE_ADDR'],
                            'erro' => 'erro_dia_nao_autorizado'
                        );
                        return json_encode($array_retorno);
                    } else {
                        $horarios_autorizados = explode(';', $retorno->horarios_autorizados);
                        if (!(date('H:i') >= $horarios_autorizados[0] && date('H:i') <= $horarios_autorizados[1]) &&
                            !(date('H:i') >= $horarios_autorizados[2] && date('H:i') <= $horarios_autorizados[3])
                        ) {
                            //Registra o log de acesso proibido
                            $str_log_acesso = 'Tentativa de acesso em <strong>horário não autorizado</strong> registrado em ' . date('d/m/Y') . ' às ' . date('H:i:s') . '<br />';
                            $str_log_acesso .= 'IP no momento da tentativa: <strong>' . $_SERVER['REMOTE_ADDR'] . '</strong><br />';
                            $str_log_acesso .= 'Tentativa de Acesso por: <strong>' . $_SERVER['HTTP_USER_AGENT'] . '</strong><br /><br />';
                            parent::setNewAcessoRestrito($retorno->id_usuario, $str_log_acesso);

                            $array_retorno = array(
                                'sucesso' => false,
                                'qtde_tentativas' => $qtde_tentativas,
                                'ip_remoto' => $_SERVER['REMOTE_ADDR'],
                                'erro' => 'erro_horario_nao_autorizado'
                            );
                            return json_encode($array_retorno);
                        }
                    }
                }
            }
            
            
            //exit(var_dump($retorno));
            
            if (isset($_SESSION['redirecionar']) && $_SESSION['redirecionar'] != '') {
                $redirecionar = $_SESSION['redirecionar'];
                unset($_SESSION['redirecionar']);
            } else {
                $redirecionar = BASE_ADMIN_URL.'/home.html';
            }
            
            $array_retorno = array(
                'sucesso' => true,
                'redirect' => $redirecionar
            );
            $_SESSION['erros_login'] = 0; //reseta a quantidade de tentativas
            
            //Criam as sessões de autorização
            $this->setSessionsLogin('login', $retorno);
            if($array_retorno['sucesso'] == true){
                date_default_timezone_set('America/Sao_Paulo');
                parent::ultimoAcesso(date('Y-m-d H:i:s'),$retorno->id_usuario,$retorno->qtde_acessos);
            }
            return json_encode($array_retorno);
        } else {
            $array_retorno = array(
                'sucesso' => false,
                'qtde_tentativas' => $qtde_tentativas,
                'erro' => 'erro_login',
            );
            return json_encode($array_retorno);
        }
    }

    /**
     * Cria ou apagam as sessões do login/logout
     * 
     * @param String $tipo login ou logout
     * @param Array $retorno
     */
    private function setSessionsLogin($tipo, $retorno = "") {
        //Cria Sessões
        if ($tipo == 'login') {
            $info_nome = explode(' ', $retorno->nome_completo);
            
            setSession($retorno->id_usuario, 'id_usuario', 'info_user');
            setSession($retorno->id_perfil, 'id_perfil', 'info_user');
            setSession($info_nome[0], 'nome', 'info_user');
            setSession($retorno->nome_completo, 'nome_completo', 'info_user');
            setSession($retorno->email, 'email', 'info_user');
            
        }
        //Apagam Sessões
        else {
            setSession(null, 'id_usuario', 'info_user');
            setSession(null, 'id_perfil', 'info_user');
            setSession(null, 'nome', 'info_user');
            setSession(null, 'nome_completo', 'info_user');
            setSession(null, 'email', 'info_user');
            
        }
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
            $object->nome_completo = $object->acessos_restritos != "" ? $object->nome_completo.' <span style="color:#F00;">'.(!$object->id_usuario_ciente ? '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' : '').'</span>' : $object->nome_completo;
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
        $this->setDataCadastro(date('Y-m-d H:i:s'));
        $this->setIdPerfil(getRequest("id_perfil"));
        $this->setNomeCompleto(getRequest("nome_completo"));
        $this->setEmail(getRequest("email"));
        $this->setLogin(getRequest("login"));
        $this->setSenha(md5(sha1(getRequest("senha"))));
        $this->setUltimoAcesso('1970-01-01 00:00:00');
        $this->setQtdeAcessos(0);
        $this->setAcessosRestritos('');
        $this->setStatus(getRequest("status")=="A"?"A":"I");
        $this->setRestricoesHorarios(getRequest("restricoes_horarios")=="S"?"S":"N");
        
        //Restrições Dias/Horarios
        if ($this->getRestricoesHorarios() == "S") {
            $dias_autorizados = $_POST['dia_semana'];
            $str_dias = "";
            for ($i=0; $i<7; $i++) {
                if (isset($dias_autorizados[$i])) {
                    $valor = $dias_autorizados[$i]=="S"?"S":"N";
                    $str_dias .= ($str_dias == "" ? $valor : ";".$valor);
                } else {
                    $str_dias .= ($str_dias == "" ? "N" : ";N");
                }
            }
            $this->setDiasAutorizados($str_dias);
            
            $str_horarios  = "";
            $str_horarios .= getRequest("entrada").";";
            $str_horarios .= getRequest("saida_intervalo").";";
            $str_horarios .= getRequest("entrada_intervalo").";";
            $str_horarios .= getRequest("saida");
            $this->setHorariosAutorizados($str_horarios);
        
        }
        $this->setIdUsuarioCiente(0);
        $this->setDatahoraCiente('1970-01-01 00:00:00');
        $id = $this->incluir();
        $this->setIdTable($id);
        
        //Registra o log da inclusão            
        Log::addNewLog(true, $this);
        
        return $id;
    }

    /**
     * Altera o registro no banco de dados
     *
     * @param int $id
     */
    public function update($id) {
        $return = false;

        //Somente o usuário 1 pode alterar ou excluir o usuário 1
        if($id != 1 || ($id == 1 && getSession('id_usuario', 'info_user') == 1)) {
            $this->setIdTable($id);
            $this->getByCod();
            $log_obj_original = clone($this); //Objeto de Log
            
            $this->setDataCadastro($this->getDataCadastro());
            $this->setIdPerfil(getRequest("id_perfil"));
            $this->setNomeCompleto(getRequest("nome_completo"));
            $this->setEmail(getRequest("email"));
            $this->setLogin(getRequest("login"));
            if (getRequest("senha") != "")
                $this->setSenha(md5(sha1(getRequest("senha"))));
            $this->setAcessosRestritos($this->getAcessosRestritos());
            $this->setUltimoAcesso(formatarData("bd2", $this->getUltimoAcesso()));
            $this->setStatus(getRequest("status")=="A"?"A":"I");
            $this->setRestricoesHorarios(getRequest("restricoes_horarios")=="S"?"S":"N");
            $this->setQtdeAcessos($this->getQtdeAcessos());
            //Restrições Dias/Horarios
            if ($this->getRestricoesHorarios() == "S") {
                $dias_autorizados = $_POST['dia_semana'];
                $str_dias = "";
                for ($i=0; $i<7; $i++) {
                    if (isset($dias_autorizados[$i])) {
                        $valor = $dias_autorizados[$i]=="S"?"S":"N";
                        $str_dias .= ($str_dias == "" ? $valor : ";".$valor);
                    } else {
                        $str_dias .= ($str_dias == "" ? "N" : ";N");
                    }
                }
                $this->setDiasAutorizados($str_dias);
                
                $str_horarios  = "";
                $str_horarios .= getRequest("entrada").";";
                $str_horarios .= getRequest("saida_intervalo").";";
                $str_horarios .= getRequest("entrada_intervalo").";";
                $str_horarios .= getRequest("saida");
                $this->setHorariosAutorizados($str_horarios);
            
            } else {
                $this->setDiasAutorizados("");
                $this->setHorariosAutorizados("");
            }
            $this->setIdUsuarioCiente(0);
            $this->setDatahoraCiente('1970-01-01 00:00:00');
            $return = $this->alterar();
            
            //Registra o log da alteração            
            Log::addNewLog(false, $log_obj_original, $this);
        }

        return $return;
    }

    /**
     * Altera o registro no banco de dados
     * @param $id
     * @return array|bool
     */
    public function alterarSenha($id) {


        $this->setIdTable($id);
        $this->getByCod();
        $log_obj_original = clone($this); //Objeto de Log

        if(!getRequest("nova_senha") || !getRequest("senha") || !getRequest("confirmacao")){
            return false;
        }

        if($this->getSenha() != md5(sha1(getRequest("senha")))){
            return false;
        }

        if(getRequest("nova_senha") != getRequest('confirmacao')){
            return false;
        }

        $return = $this->alterarCampo('senha',md5(sha1(getRequest("nova_senha"))),$id);

        //Registra o log da alteração
        Log::addNewLog(false, $log_obj_original, $this);

        return $return;
    }

    public function setCiente($id_usuario, $id_ciente){
        return parent::setCiente($id_usuario, $id_ciente);
    }
}
