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
*  @version  1.0
*
*/
class Session {

  private $session_id;
  private static $conexao;

  public function __construct(){
    self::$conexao = Conexao::abreConexao();

    session_set_save_handler(
      array($this, "open"),
      array($this, "close"),
      array($this, "read"),
      array($this, "write"),
      array($this, "destroy"),
      array($this, "gc")
    );

    $this->gc();
  }

  public function open(){}
  public function close(){}

  public function read($id){
    $this->session_id = $id;
    $query = "SELECT `data` FROM `sessions` WHERE id = '".$id."'";
    $resultado = self::$conexao->runQuery($query);
    if (mysql_num_rows($resultado) > 0) {
      $dados_session = mysql_fetch_assoc($resultado);
      return $dados_session["data"];
    } else {
      return false;
    }
  }

  public function write($id, $data) {
    $this->session_id = $id;
    $query = "REPLACE INTO `sessions` (`id`, `data`, `time`) VALUES ('".$id."', '".$data."', ADDTIME(CURRENT_TIME, '00:30:00'))";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado) {
      $this->atualizaStatus($data);
      return true;
    } else {
      return false;
    }
  }

  public function destroy($id) {
    $this->session_id = $id;
    $query = "DELETE FROM `sessions` WHERE `id` = '".$id."'";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado)
      return true;
    else
      return false;
  }

  public function gc(/** @noinspection PhpUnusedParameterInspection */$max = 10) {
    $query = "DELETE FROM `sessions` WHERE `time` < CURRENT_TIME";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado)
      return true;
    else
      return false;
  }

  public function atualizaStatus($data) {
    $status = (trim($data) == "") ? "N" : "S";
    $query = "UPDATE `sessions` SET online = '".$status."' WHERE `id` = '".$this->session_id."'";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado)
      return true;
    else
      return false;
  }

}
