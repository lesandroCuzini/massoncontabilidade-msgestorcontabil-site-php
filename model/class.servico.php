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

class ServicoModel extends Model {

    /**
     *
     * Atributos privados da classe
     */
    public $id_table;
    public $datahora_cadastro;
    public $datahora_atualizacao;
    public $titulo;
    public $subtitulo;
    public $conteudo;
    public $url_banner_topo;
    public $url_imagem_destaque;
    public $url_icone;
    public $meta_titulo;
    public $meta_descricao;
    public $url_rewrite;
    public $destacar_home;
    public $status;

    public $table = 'servicos';
    public $key = "id_servico";
    
    /**
     * Retorna lista com registros cadastrados no banco
     * @author 3dots
     *
     * @return array
     */
    protected function getListaAdmin() {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY `id_servico`";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Limpa os campos de imagens
     *
     * @param $id_registro
     * @param $campo
     *
     * @return bool
     */
    protected function updateCamposImagem($id_registro, $campo) {
        $query = "UPDATE ".$this->table." SET ".$campo." = '' 
                  WHERE ".$this->key." = '".$id_registro."'";

        $resultado = Conexao::getInstance()->execute($query);
        return $resultado;
    }

    /**
     * Insere o registro na tabela servicos_complementos
     *
     * @param $infoComplemento
     * @return bool|string
     */
    protected function addInfoComplemento($infoComplemento) {
        $query = "INSERT INTO `servicos_complementos` (
                      `id_servico`, `titulo`, `subtitulo`
                  ) VALUES (
                      '".$infoComplemento['id_servico']."', 
                      '".$infoComplemento['titulo']."', 
                      '".$infoComplemento['subtitulo']."' 
                  )";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * Remove o registro do título do complemento
     *
     * @param $id_registro
     * @param $id_complemento
     * @return bool|string
     */
    public function removerInfoComplemento($id_registro, $id_complemento) {
        $query = "DELETE FROM `servicos_complementos` 
                    WHERE `id_servico_complemento` = '".$id_complemento."' 
                    AND `id_servico` = '".$id_registro."' ";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * @param $id_servico
     * @return array|false
     */
    protected function getComplementosByIdServico($id_servico) {
        $query = "SELECT * FROM ". $this->table ."_complementos 
                    WHERE `id_servico` = '". $id_servico ."' ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Retorna as informações do título do complemento do serviço
     *
     * @param $id_servico
     * @return array|bool|object
     */
    protected function getInfoComplementoByIdServico($id_servico) {
        $query = "SELECT * FROM " . $this->table . "_complementos 
                    WHERE `id_servico` = '".$id_servico."' 
                    ORDER BY `id_servico_complemento` DESC";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado);
    }

    /**
     * Insere o registro na tabela servicos_complementos_itens
     *
     * @param $infoComplementoItem
     * @return bool|string
     */
    protected function addInfoComplementoItem($infoComplementoItem) {
        $query = "INSERT INTO `servicos_complementos_itens` (
                      `id_servico`, `id_servico_complemento`, `titulo`, `subtitulo`, `url_icone`
                  ) VALUES (
                      '".$infoComplementoItem['id_servico']."', 
                      '".$infoComplementoItem['id_servico_complemento']."', 
                      '".$infoComplementoItem['titulo']."', 
                      '".$infoComplementoItem['subtitulo']."', 
                      '".$infoComplementoItem['url_icone']."' 
                  )";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * Recupera as informações do Item do Complemento
     *
     * @param $id_registro
     * @param $id_complemento_item
     * @return array|bool|object
     */
    public function getInfoComplementoItemById($id_registro, $id_complemento_item) {
        $query = "SELECT * FROM `servicos_complementos_itens` 
                    WHERE `id_servico_complemento_item` = '".$id_complemento_item."' 
                    AND `id_servico` = '".$id_registro."' ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado);
    }

    /**
     * Remove o registro do Item do Complemento
     *
     * @param $id_registro
     * @param $id_complemento_item
     * @return bool|string
     */
    public function removeComplementoItem($id_registro, $id_complemento_item) {
        $query = "DELETE FROM `servicos_complementos_itens` 
                    WHERE `id_servico_complemento_item` = '".$id_complemento_item."' 
                    AND `id_servico` = '".$id_registro."' ";

        return Conexao::getInstance()->execute($query);
    }

    /**
     * Retorna os itens dos complementos
     *
     * @param $id_servico
     * @param $id_servico_complemento
     * @return array|false
     */
    public function getComplementosItens($id_servico, $id_servico_complemento) {
        $query = "SELECT * FROM " .$this->table. "_complementos_itens 
                    WHERE `id_servico` = '".$id_servico."' 
                    AND `id_servico_complemento` = '".$id_servico_complemento."' 
                    ORDER BY `id_servico_complemento_item` ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }

    /**
     * Retorna o ID através da URL amigável
     *
     * @param $url_rewrite
     *
     * @return array|false
     */
    protected function getIdByUrlRewrite($url_rewrite) {
        $query = "SELECT * FROM `". $this->table ."` 
                    WHERE `url_rewrite` = '". $url_rewrite ."' 
                    LIMIT 1 ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getLinhaRegistro($resultado);
    }

    /**
     * @param $destacar_home
     * @param $status
     * @return array|bool|object
     */
    protected function getServicosDestaques($destacar_home, $status) {
        $query = "SELECT * FROM ". $this->table ." 
                    WHERE `destacar_home` = '". $destacar_home ."'
                    AND `status` = '". $status ."' ";

        $resultado = Conexao::getInstance()->executeS($query);
        return $this->getListaRegistros($resultado);
    }
}
