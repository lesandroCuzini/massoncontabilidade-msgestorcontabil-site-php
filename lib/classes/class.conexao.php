<?php
/*
* @copyright 2014 3dots
*
* DISCLAIMER
*
* Do not edit or add anything to this file
* If you need to edit this file consult 3dots.
*
*  @author 3dots <contato@3dots.com.br>
*  @copyright 2014 3dots
*  @version  3.2 - 3dots
*
*/
class Conexao {

  /* Variáveis Globais */
  private $servidor;
  private $usuario;
  private $senha;
  public $banco;
  private $conn;
  private $resultado;
  private $sql;

  public function getConexao() {
    return $this->conn;
  }

  /* Método Construtor */
  public function __construct($server, $user, $pass, $banco) {
    $this->setServidor($server);
    $this->setUsuario($user);
    $this->setSenha($pass);
    $this->setBanco($banco);
  }

  /* Sets */
  public function setServidor($server) {
    $this->servidor = $server;
  }

  public function setUsuario($user) {
    $this->usuario = $user;
  }

  public function setSenha($pass) {
    $this->senha = $pass;
  }

  public function setBanco($banco) {
    $this->banco = $banco;
  }

  /* Método que abre a conexão com o Banco de Dados */
  public function connDB() {
    $this->conn = new mysqli($this->servidor, $this->usuario, $this->senha, $this->banco);
    mysqli_set_charset($this->conn,"utf8");
    //Verifica a conexão
    if ($this->conn->connect_errno) {
      printf("Falha na conex&atilde;o com o banco: %s\n", $this->conn->connect_error);
      exit();
    }
  }

  /* Método que fecha a conexão com o Banco de Dados */
  public function closeConnDB() {
    return $this->conn->close;
  }

  /**
  * Método que executa comando SQL
  *
  * @param string $sql
  *
  * @return boolean
  */
  public function execute($sql) {
    $this->connDB();
    $retorno = false;
    if (!$retorno = $this->conn->query($sql))
      $retorno = "Erro ao executar o comando:\n".$this->conn->error;
    return $retorno;
  }

  /**
  * Método que executa comando SQL
  *
  * @param string $sql
  *
  * @return array for select
  */
  public function executeS($sql) {
    $this->connDB();
    $retorno = false;
    if ($resultado = $this->conn->query($sql)) {
      $array_result = array();
      while($object = $resultado->fetch_object()) {
        $array_result[] = $object;
      }
      $retorno = $array_result;

      $resultado->close();
    } else {
      $retorno = "Erro ao executar o comando:\n".$this->conn->error;
    }
    return $retorno;
  }

  /* Conexão aos banco de dados */
  public static function getInstance() {
    $conexao = new Conexao(BD_HOST, BD_USER, BD_PASS, BD_DB);

    return $conexao;
  }

}
