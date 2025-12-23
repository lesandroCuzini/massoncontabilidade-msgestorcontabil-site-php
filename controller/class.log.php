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

class Log extends LogModel {

  public function __construct($submit = false) {
    parent::__construct($submit);
  }

    /**
     * Verifica o post enviado pelo formulário
     *
     */
    public function postProcess($acao) {
    
    }

    /**
     * Adiciona o registro de Log
     * 
     * @param object $obj_original
     * @param object $obj_modificado
     * @param String $info_complementar
     *
     * @return Boolean
     */
    public static function addNewLog($novo_registro = false, $obj_original = null, $obj_modificado = null, $info_complementar = null, $registro_excluido = false) {
        //Se foi exclusão
        if ($registro_excluido) {
            $str_log = 'O registro "'.$obj_original->getIdTable().'" foi apagado do sistema.<br />';
        }
        //Se foi adição de novo registro
        elseif ($novo_registro) {
            $str_log = 'Foi adicionado o registro "'.$obj_original->getIdTable().'".<br />';
        } 
        //Se foi alteração
        else {
            //Verifica se houveram alterações
            $str_log = "";
            if ($obj_original !== $obj_modificado) {
                foreach($obj_original as $key => $value) {
                    if ($value != $obj_modificado->{$key}) {
                        $str_log .= 'O campo "'.strtoupper($key).'" foi alterado de "'.$value.'" para "'.$obj_modificado->{$key}.'".<br />';
                    }
                }
            }
        }
        
        //Adicionad o novo Log
        if ($str_log != "") {
            $log = new Log();
            $log->setTabela($obj_original->getTable());
            $log->setIdUsuario(getSession('id_usuario', 'info_user'));
            $log->setDatahoraAlteracao(date('Y-m-d H:i:s'));
            $log->setMensagem($str_log.$info_complementar);
            $log->incluir();
        }
    }

}
