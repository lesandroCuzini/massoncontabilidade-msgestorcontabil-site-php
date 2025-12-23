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
*  @version  3.2 - Eduardo
*
*/
class UrlRewrite extends Model {

  /**
   *
   * Atributos privados da classe
   */
  public $id_table;
  public $url_origem;
  public $url_destino;
  public $tipo_url;
  public $status;

  protected $table = "url_rewrite";

  /**
   *Busca o destino atraves da origem
   *
   */
  public function getDestino() {
    $query = "SELECT * FROM ".$this->table." WHERE url_origem = '".$this->url_origem."' AND status = 'A' AND tipo_url = '".$this->tipo_url."'";
    $retorno = self::$conexao->executeS($query);
    $dados = $this->getLinhaRegistro($retorno);
    if ($dados !== false) {
      $this->id_table = $dados->id_url_rewrite;
      $this->url_origem = $dados->url_origem;
      $this->url_destino = $dados->url_destino;
      return true;
    }
    return false;
  }

  /**
   *Busca a origem atraves do destino
   *
   */
  public function getOrigem() {
    $query = "SELECT * FROM ".$this->table." WHERE url_destino = '".$this->url_destino."' AND status = 'A' AND tipo_url = '".$this->tipo_url."'";
    $retorno = self::$conexao->executeS($query);
    $dados = $this->getLinhaRegistro($retorno);
    if ($dados !== false) {
      $this->id_table = $dados->id_url_rewrite;
      $this->url_origem = $dados->url_origem;
      $this->url_destino = $dados->url_destino;
      return true;
    }
    return false;
  }

}
