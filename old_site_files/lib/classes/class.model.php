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

class Model
{

    // conexao estática única - singleton
    public static $conexao;
    // filtro para listas do admin
    public $filtro;

    /**
     * Este método deverá ser chamado quando ocorrer requisições REST
     * @author Eduardo
     * @param boolean $submit - se haverá ações ou não
     * @param int $con -> se for diferente de 1, a conexao é outra
     */
    public function __construct($submit = false, $con = 1)
    {
        error_reporting(E_ALL);

        // conexao local
        self::$conexao = Conexao::getInstance();
        // filtros
        $this->filtro = "filtro_" . $this->getTable();
        // tratamento de requisicoes POST ou GET
        if (($submit) && ((count($_POST) > 0) || (count($_GET) > 1))) {
            // resgata o tipo de ação solicitada
            $acao = getRequest("acao");
            if ($acao != "") $this->postProcess($acao);
        } // deletando registros via requisicao DELETE
        elseif ($submit && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            parse_str(file_get_contents("php://input"), $del);
            // usuario Innovare não será apagado, assim como o Perfil SuperAdmin
            if ($del["id"] != "" && ((!($this instanceof Usuario) && !($this instanceof Perfil)) || $del["id"] != 1)) {
                $this->setIdTable($del["id"]);
                // excluir imagens - método deve estar em cada model correspondente
                $this->excluirImagens();
                $this->excluir();
                // suicídio do usuário que está logado
                if ($this instanceof Usuario && $del["id"] == getSession("id", "user")) setSession(null, "user");
            }
        }
    }

    /**
     * Este método deverá ser chamado quando ocorrer chamadas GET e/ou SET
     * @author Dheyson
     * @param string $metodo O nome do método quer será chamado
     * @param array $parametros Parâmetros que serão passados aos métodos
     * @return void|mixed
     */
    public function __call($metodo, $parametros)
    {
        if (substr($metodo, 0, 3) == 'set') { #se for set, seta um valor para a propriedade
            $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            if (isset($parametros[0])) $this->$var = $parametros[0];
        } elseif (substr($metodo, 0, 3) == 'get') { #se for get, retorna o valor da propriedade
            $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            return $this->$var;
        }
    }

    /**
     * Este método deverá ser chamado quando houver necessidade setar valores
     * para os atributos
     * @author Dheyson
     *
     * @param array $result Retorno dos dados da consulta na tabela
     */
    public function setFields($result)
    {
        $query = "SHOW FULL FIELDS FROM " . $this->getTable();
        $resultado_fields = self::$conexao->executeS($query);
        if (count($resultado_fields) > 0) {
            foreach ($resultado_fields as $campos) {
                $arrayType = explode("(", $campos->{'Type'});
                $type = strtolower($arrayType[0]);
                switch ($type) {
                    case ($type == 'smallint' || $type == 'int' || $type == 'bigint'):
                        $valor_campo = intval($result->{$campos->Field});
                        break;
                    case ($type == 'double'):
                        $valor_campo = number_format($result->{$campos->Field}, 2, ".", "");
                        break;
                    case ($type == 'varchar' || $type == 'char' || $type == 'text' || $type == 'longtext'):
                        $valor_campo = $result->{$campos->Field};
                        break;
                    case ($type == 'datetime'):
                        $valor_campo = formatarData("user", $result->{$campos->Field});
                        break;
                    case ($type == 'date'):
                        $valor_campo = formatarData("data", $result->{$campos->Field});
                        break;
                    default:
                        $valor_campo = $result->{$campos->Field};
                }
                $this->{$campos->{'Field'}} = $valor_campo;
            }
        }
    }

    /**
     * Busca informações do Registro
     * @author Dheyson
     *
     */
    public function getByCod()
    {

        $query = "SELECT * FROM " . $this->getTable() . " 
    WHERE " . $this->getKey() . " = '" . $this->getIdTable() . "'";

        $resultado = self::$conexao->executeS($query);
        if (count($resultado) > 0) {
            $dados = $resultado[0];
            $this->setFields($dados);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Busca informações da Classe
     * @author Gustavo Ferrari
     * @param bool $where
     * @param bool $orderby
     * @param bool $limit
     * @param bool $total
     * @return array
     */
    public function getLista($where = false, $orderby = false, $limit = false, $total = false)
    {

        if (!$total) {
            $query = "SELECT * FROM " . $this->getTable();
        } else {
            $query = "SELECT COUNT(*) as total FROM " . $this->getTable();
            if ($where) {
                $query .= " WHERE " . $where;
            }
            $result = self::getLinhaRegistro(self::$conexao->executeS($query));
            return $result->total;
        }
        if ($where) {
            $query .= " WHERE " . $where;
        }
        if ($orderby) {
            $query .= " ORDER BY " . $orderby;
        }

        if ($limit) {
            $query .= " LIMIT " . $limit;
        }

        return self::getListaRegistros(self::$conexao->executeS($query));
    }

    /**
     * Pega o último registro da tabela
     * @author Dheyson
     *
     * @return int $id_auto_increment
     */
    public function getLastReg()
    {
        $query = "SHOW TABLE STATUS LIKE '" . $this->getTable() . "'";
        $info_table = self::$conexao->executeS($query);
        $dados = $this->getLinhaRegistro($info_table);
        return $dados->Auto_increment - 1;
    }

    /**
     * Pega o último registro da tabela
     * @author Dheyson
     *
     * @return int $id_auto_increment
     */
    public function getInserted()
    {
        return self::$conexao->getConexao()->insert_id;
    }

    /**
     * Retorna a quantidade de registros (ativos ou nao) de uma tabela
     * @author Eduardo
     *
     * @param string $status
     * @return int total de registros
     */
    public function getTotal($status = "")
    {
        $query = "SELECT COUNT(DISTINCT(" . $this->getKey() . ")) AS total FROM " . $this->getTable() . "";
        if ($status != "") $query .= " WHERE status = '" . $status . "'";
        $resultado = self::$conexao->executeS($query);
        $resultado = $this->getLinhaRegistro($resultado);
        return isset($resultado->total) ? $resultado->total : 0;
    }

    /**
     * Busca a lista de registros cadastrados
     * @author Eduardo
     *
     * @param string $status
     * @param string $order
     * @return Array of objects
     */
    public function listAll($status = "A", $order = "")
    {
        $query = "SELECT * FROM " . $this->getTable() . " WHERE 1=1";
        if ($status != "") $query .= " AND " . $this->getTable() . ".status = 'A'";
        if ($order != "") $query .= " ORDER BY " . $order;

        $retorno = self::$conexao->executeS($query);
        return $this->getListaRegistros($retorno);
    }

    /**
     * Adiciona o registro
     * @author Dheyson
     *
     */
    public function incluir()
    {
        $query = "SHOW FULL FIELDS FROM " . $this->getTable();
        $resultado_fields = self::$conexao->executeS($query);
        $string_fields = '';
        $string_values = '';
        if (count($resultado_fields) > 0) {
            $i = 0;
            foreach ($resultado_fields as $campos) {
                if ($campos->{'Key'} != 'PRI') {
                    $string_fields .= ($i == 0) ? $campos->{'Field'} : ", " . $campos->{'Field'};
                    $string_values .= ($i == 0) ? "'" . (string)$this->{$campos->{'Field'}} . "'" : ", '" . (string)$this->{$campos->{'Field'}} . "'";
                    $i++;
                }
            }
        }

        $query = "INSERT INTO " . $this->getTable() . " ( " . $string_fields . " )
                    VALUES ( " . $string_values . " );";
        //exit($query);
        $resultado = self::$conexao->execute($query);

        if ($resultado) {
            $id = $this->getInserted();
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Altera o registro
     * @author Dheyson
     *
     */
    public function alterar()
    {
        $query = "SHOW FULL FIELDS FROM " . $this->getTable();
        $resultado_fields = self::$conexao->executeS($query);
        $string_update = '';
        if (count($resultado_fields) > 0) {
            $i = 0;
            foreach ($resultado_fields as $campos) {
                if (($campos->Key != 'PRI') && ($campos->Field != "data_cadastro")) {
                    $string_update .= ($i == 0) ? $campos->Field . " = '" . $this->{$campos->Field} . "'" : ", " . $campos->Field . " = '" . $this->{$campos->Field} . "'";
                    $i++;
                }
            }
        }
        $query = "UPDATE " . $this->getTable() . " SET " . $string_update . " 
    WHERE " . $this->getKey() . " = '" . $this->getIdTable() . "';";

        $resultado = self::$conexao->execute($query);

        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Exclui o registro
     * @author Dheyson
     *
     */
    public function excluir()
    {
        $query = "DELETE FROM " . $this->getTable() . " WHERE " . $this->getKey() . " = '" . $this->getIdTable() . "';";
        $resultado = self::$conexao->execute($query);
        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Exclui todos os registros
     * @author Dheyson
     *
     */
    public function limpar()
    {
        $query = "DELETE FROM " . $this->getTable();
        $resultado = self::$conexao->execute($query);
        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retorna varias linhas de registro como objetos
     * @author Eduardo
     * @param $result , retorno da funcao runQuery
     * @param $tables string, nomes das tabelas usadas no sql
     * @param $aliases string, array com os alias e seus tipos
     * @return array of objects
     */
    protected function getListaRegistros($result, $tables = "", $aliases = "")
    {
        if ($result === false) return false;

        $num_linhas = count($result);
        if ($num_linhas < 1) return false;
        $lista = array();
        $i = 0;
        if (gettype($result) == "object" || gettype($result) == "array") {
            foreach ($result as $linha) {
                $complemento['i'] = $i;
                if (++$i == $num_linhas) $complemento['ultimo'] = true; else
                    $complemento['ultimo'] = false;

                $linha = (object)array_merge((array)$linha, (array)$complemento);

                $lista[] = $this->execDefs($linha, $tables, $aliases);
            }
        }
        else{
            return $result;
        }
        return $lista;
    }

    /**
     * Retorna varias linhas de registro como arrays em casos especiais
     * @author Eduardo
     * @param $result , retorno da funcao runQuery
     * @return array of objects
     */
    protected function getArrayRegistros($result)
    {
        if ($result === false) return false;
        $num_linhas = count($result);
        if ($num_linhas < 1) return false;
        $lista = array();
        $i = 0;
        foreach ($result as $linha) {
            $linha->i = $i;
            if (++$i == $num_linhas) $linha->ultimo = true; else
                $linha->ultimo = false;
            $lista[] = $linha;
        }
        return $lista;
    }

    /**
     * Retorna apenas a primeira linha de um result set, já como objeto
     * @author Eduardo
     * @param $result , retorno da funcao runQuery
     * @param $tables string, nomes das tabelas usadas no sql
     * @param $aliases string, array com os alias e seus tipos
     * @return object
     */
    protected function getLinhaRegistro($result, $tables = "", $aliases = "")
    {
        if (!isset($result[0]) || $result === false) return false;
        $linha = $result[0];
        return $this->execDefs($linha, $tables, $aliases);
    }

    /**
     * Formata tipos pré-definidos de campos
     * @author Eduardo, Dheyson
     * @param $linha
     * @param $tables
     * @param $aliases
     * @return bool|array
     */
    protected function execDefs($linha, $tables, $aliases)
    {
        if ($linha === false) return false;
        $colunas_tipos = $this->getTypesColumns($tables, $aliases);
        foreach ($linha as $key => $value) {
            if (array_key_exists($key, $colunas_tipos)) {
                $type = $colunas_tipos[$key];
                switch ($type) {
                    case ($type == 'smallint' || $type == 'int' || $type == 'bigint'):
                        $linha->$key = intval($value);
                        break;
                    case ($type == 'double'):
                        $linha->$key = number_format($value, 2, ".", "");
                        break;
                    case ($type == 'varchar' || $type == 'char' || $type == 'text' || $type == 'longtext'):
                        $linha->$key = $value;
                        break;
                    case ($type == 'datetime'):
                        $linha->$key = formatarData("user", $value);
                        break;
                    case ($type == 'date'):
                        $linha->$key = formatarData("data", $value);
                        break;
                    default:
                        $linha->$key = $value;
                }
            } else
                $linha->$key = $value;
        }
        return $linha;
    }

    /**
     * Filtram somente as colunas das tabelas inclusas no select
     * @author Dheyson, Eduardo
     * @param $tables
     * @param $aliases
     * @return array
     */
    public function getTypesColumns($tables, $aliases)
    {
        if ($tables) $tabelas = explode(",", $tables);
        $tabelas[] = $this->getTable();

        $all_columns = $this->createFieldsDataBase();

        $colunas_tabelas = array();
        foreach ($tabelas as $tabela) {
            foreach ($all_columns["dados"][trim($tabela)] as $key => $colunas) {
                $colunas_tabelas[$key] = $colunas;
            }
        }

        if ($aliases) {
            foreach ($aliases as $key => $alias) {
                $colunas_tabelas[$key] = $alias;
            }
        }

        return $colunas_tabelas;
    }

    /**
     * Cria um arquivo com todos tipos de campos do banco com suas tabelas como chave.
     * @author Dheyson
     */
    public function createFieldsDataBase()
    {
        $filename = __DIR__ . "/../setup/fields_" . self::$conexao->banco . ".php";

        if (file_exists($filename)) {
            $fd = fopen($filename, 'r');
            $file_content = fread($fd, filesize($filename));
            fclose($fd);
            $array_columns = unserialize($file_content);
        } else {
            $array_columns["indice"] = 0;
        }

        $query = "SELECT COUNT(*) as total FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '" . self::$conexao->banco . "'";
        $retorno = self::$conexao->executeS($query);
        $dados_cont = $retorno[0];

        if ($dados_cont->total != $array_columns["indice"]) {
            $query = "SELECT table_name, column_name, data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '" . self::$conexao->banco . "' ORDER BY table_name, column_name";
            $retorno = self::$conexao->executeS($query);
            if (count($retorno) > 0) {
                $array_columns = array("indice" => count($retorno),
                    "dados" => array());
                foreach ($retorno as $dados) {
                    $array_columns["dados"][$dados->table_name][$dados->column_name] = $dados->data_type;
                }

                $f = fopen($filename, "w");
                fwrite($f, serialize($array_columns) . "\n");
                fclose($f);

                return $array_columns;
            } else
                return false;
        } else
            return $array_columns;
    }

    /**
     * Função para alterar apenas um campo, sem a necessidade de usar getByCod() e alterar()
     * Gustavo - Innovare - 08/06/2016
     * @param $campo
     * @param $valor
     * @param $id
     * @return array
     */
    public function alterarCampo($campo, $valor, $id)
    {
        $query = "UPDATE " . $this->getTable() . "
                SET `" . $campo . "` = '" . $valor . "'
              WHERE `" . $this->getKey() . "` = '" . $id . "'";

        return self::$conexao->execute($query);
    }

    public function getByWord($searchs, $word, $limit)
    {
        $query = "SELECT * FROM " . $this->getTable() . " WHERE 1=1 AND( ";
        $query2 = '';
        foreach ($searchs as $search) {
            $query2 .= " OR `" . $search . "` LIKE '%" . $word . "%' ";
        }
        $query2 = ltrim($query2, ' OR');
        $query .= $query2 . ') ';
        $query .= ' AND status = "A"';
        $query .= ' LIMIT ' . $limit;

        return $this->getListaRegistros(self::$conexao->executeS($query));
    }

}
