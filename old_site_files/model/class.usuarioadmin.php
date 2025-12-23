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
 *  @version  1.0
 *
 */

class UsuarioAdminModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $id_perfil;
    public $data_cadastro;
    public $nome_completo;
    public $email;
    public $login;
    public $senha;
    public $ultimo_acesso;
    public $qtde_acessos;
    public $acessos_restritos;
    public $restricoes_horarios;
    public $dias_autorizados;
    public $horarios_autorizados;
    public $id_usuario_ciente;
    public $datahora_ciente;
    public $status;

    public $table = 'usuarios_admin';
    public $key = "id_usuario";


    /**
     * Retorna lista com registros cadastrados no banco
     * @author Dheyson Wildny
     *
     * @param Int $rpp
     * @param Int $pag_atual
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM " . $this->table;

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Verifica o login do usuário
     * 
     * @param String $login
     * @param String $senha
     * 
     * @return Array
     */
    protected function verifyLogin($login, $senha) {
        $query = "SELECT * FROM ".$this->table." 
                    WHERE status = 'A' 
                    AND login = '".$login."' 
                    AND senha = '".$senha."'";
        $resultado = Conexao::getInstance()->executeS($query);
        
        return $this->getLinhaRegistro($resultado);
    }
    
    /**
     * Atualiza a quantidade de acessos
     * 
     * @param int $id_usuario
     * 
     * @return Boolean
     */
    protected function atualizaAcessos($id_usuario) {
        $query = "UPDATE ".$this->table." SET 
                    ultimo_acesso = CURRENT_TIMESTAMP, 
                    qtde_acessos = qtde_acessos + 1
                  WHERE id_usuario = '".$id_usuario."' ";
        $resultado = Conexao::getInstance()->execute($query);
        
        return $resultado;
    }

    /**
     * Função para registrar o acesso não autorizado do usuário
     * 
     * @param int $id_usuario
     * @param String $msg
     * 
     * @return Boolean
     */
    protected function setNewAcessoRestrito($id_usuario, $msg) {
        $query = "UPDATE ".$this->table." SET 
                    acessos_restritos = CONCAT('".$msg."', acessos_restritos),
                    id_usuario_ciente = 0,
                    datahora_ciente = NULL
                  WHERE id_usuario = '".$id_usuario."' ";
        $resultado = Conexao::getInstance()->execute($query);
        
        return $resultado;
    }

    public function setCiente($id_usuario, $id_ciente)
    {
        $query = "UPDATE ".$this->table." SET
                    id_usuario_ciente = ".(int)$id_ciente.",
                    datahora_ciente = NOW()
                  WHERE id_usuario = '".$id_usuario."' ";
        return Conexao::getInstance()->execute($query);
    }
}
