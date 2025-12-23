<?php

/*
* @copyright 2014 Innovare Company
*
* DISCLAIMER
*
* Do not edit or add anything to this file
* If you need to edit this file consult Innovare Company.
*
*  @author Innovare Company <contato@innovarecompany.com.br>
*  @copyright 2014 Innovare Company
*
*/

class Configuracao extends Model
{

    /**
     * Atributos privados da classe
     */
    public $id_table;
    public $name;
    public $value;
    public $data_add;
    public $id_user_add;
    public $data_atu;
    public $id_user_atu;

    protected $table = "config";

    public function postProcess($acao)
    {
        if ($acao == "alterar") {
            $retorno = false;
            foreach ($_POST as $key => $value) {
                if ($key != 'acao') {
                    $retorno = $this->setField($key, $value);
                }
            }
            if(isset($_POST["mostrar_calculadora_simulacao_mensalidade"])){
                $retorno = $this->setField("mostrar_calculadora_simulacao_mensalidade","S");
            }
            if(!isset($_POST["mostrar_calculadora_simulacao_mensalidade"])){
                $retorno = $this->setField("mostrar_calculadora_simulacao_mensalidade","N");
            }
            if (isset($_FILES['SITE_LOGOTIPO']['tmp_name']) && $_FILES['SITE_LOGOTIPO']['tmp_name']) {
                $retorno = $this->setField('SITE_LOGOTIPO', uploadArquivo($_FILES['SITE_LOGOTIPO'], 'uploads/tema/', 'logotipo'));
            }
            if (isset($_FILES['SITE_IMAGEM_HEADER_HOME']['tmp_name']) && $_FILES['SITE_IMAGEM_HEADER_HOME']['tmp_name']) {
                $retorno = $this->setField('SITE_IMAGEM_HEADER_HOME', uploadArquivo($_FILES['SITE_IMAGEM_HEADER_HOME'], 'uploads/tema/', 'imagem_header'));
            }
            if (isset($_FILES['SITE_IMAGEM_PADRAO_COMPARTILHAMENTO_FACEBOOK']['tmp_name']) && $_FILES['SITE_IMAGEM_PADRAO_COMPARTILHAMENTO_FACEBOOK']['tmp_name']) {
                $retorno = $this->setField('SITE_IMAGEM_PADRAO_COMPARTILHAMENTO_FACEBOOK',uploadArquivo($_FILES["SITE_IMAGEM_PADRAO_COMPARTILHAMENTO_FACEBOOK"], 'uploads/', 'imagem_compartilhamento', true, array('tema/'), array(array(200, 200))));
            }
            if ($retorno)
                header("Location: " . BASE_ADMIN_URL . "/configuracoes-lista.html?msg=ok");
            else
                header("Location: " . BASE_ADMIN_URL . "/configuracoes-lista.html?msg=erro");
        }
        elseif ($acao == "apagar-imagem-header"){
            if($this->updateImageHeader()){
                exit('1');
            }
            else{
                exit('0');
            }
        }
        elseif ($acao == "liberar_ip") {
            $manutencao = getRequest('liberar_acesso');
            $ip_liberado = getRequest('liberar_ip');
            if ($this->changeManutencao($manutencao, $ip_liberado))
                header("Location: " . BASE_ADMIN_URL );
            else
                header("Location: " . BASE_ADMIN_URL . "/?msg=erro");
            die;
        }
        elseif ($acao == "remover_ip") {
            $ip_removido = getRequest('ip');
            if ($this->removerIpLiberado($ip_removido))
                header("Location: " . BASE_ADMIN_URL );
            else
                header("Location: " . BASE_ADMIN_URL . "/?msg=erro");
            die;
        }
    }

    public function getListAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $retorno = self::$conexao->executeS($query);
        $dados = $this->getListaRegistros($retorno);
        if ($dados !== false) {
            foreach ($dados as $linha) {
                define(strtoupper($linha->{'name'}), htmlentities($linha->{'value'}));
            }
            return true;
        }
        return false;
    }

    public function setField($name, $value)
    {
        $query = "UPDATE config SET `value` = '" . $value . "' WHERE upper(`name`) = upper('" . $name . "')";
        $retorno = self::$conexao->execute($query);
        return $retorno;
    }

    public function getField($name)
    {
        $query = "SELECT `value` FROM config WHERE upper(`name`) = upper('" . $name . "')";
        $retorno = self::$conexao->executeS($query);
        $linha = $this->getLinhaRegistro($retorno);
        if ($linha !== false) {
            return $linha->value;
        }
        return false;
    }

    /**
     * Função utilizada para apagar imagem do Cabeçalho
     * @return bool
     */
    public function updateImageHeader(){
        //unlink(UPLOADS_URL."/tema/".SITE_IMAGEM_HEADER_HOME);
        $query = "UPDATE ".$this->table." SET value='' WHERE name='SITE_IMAGEM_HEADER_HOME'";
        return $retorno = self::$conexao->execute($query);
    }
    public function changeManutencao($manutencao, $ip_liberado) {
        $query = "UPDATE config SET `value` = '".$manutencao."' WHERE upper(`name`) = 'MANUTENCAO'";
        $retorno = self::$conexao->execute($query);

        if ($manutencao == "false") {
            $query = "UPDATE config SET `value` = '' WHERE upper(`name`) = 'MANUTENCAO_IP'";
            $retorno = self::$conexao->execute($query);
        } else {
            if ($ip_liberado != "") {
                if (MANUTENCAO_IP == "")
                    $query = "UPDATE config SET `value` = '".$ip_liberado."' WHERE upper(`name`) = 'MANUTENCAO_IP'";
                else
                    $query = "UPDATE config SET `value` = CONCAT(`value`, ',', '".$ip_liberado."') WHERE upper(`name`) = 'MANUTENCAO_IP'";
                $retorno = self::$conexao->execute($query);
            }
        }

        return $retorno;
    }
    public function removerIpLiberado($ip_removido){
        if (MANUTENCAO_IP != "") {
            $array_ips = explode(',', MANUTENCAO_IP);
            $array_liberados = array();
            foreach($array_ips as $ip) {
                if ($ip != $ip_removido)
                    $array_liberados[] = $ip;
            }
            $array_ips = implode($array_liberados, ',');

            $query = "UPDATE config SET `value` = '".$array_ips."' WHERE upper(`name`) = 'MANUTENCAO_IP'";
            $retorno = self::$conexao->execute($query);

            return $retorno;
        } else {
            return true;
        }
    }

}
