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

class TextoFoto extends TextoFotoModel
{

    public function __construct($submit = false)
    {
        parent::__construct($submit);
    }

    /**
     * Busca o total de registros encontrados na tabela
     * @author Dheyson Wildny
     *
     * @return int
     */
    public function getTotalAdmin()
    {
        return parent::getTotalAdmin();
    }

    /**
     * Retorna lista com registros cadastrados no banco
     * @author Dheyson Wildny
     *
     * @param Int $rpp
     * @param Int $pag_atual
     *
     * @return array
     */
    public function getListaAdmin($rpp, $pag_atual)
    {
        $lista_resultados = parent::getListaAdmin($rpp, $pag_atual);

        //cria o array com os registros já tratados
        $array_results = array();
        if ($lista_resultados === false) return false;

        foreach ($lista_resultados as $object) {
            $object->img_status = ($object->status == "A" ? ADMIN_IMAGES_URL . "/btn_ball_green.gif" : ADMIN_IMAGES_URL . "/btn_ball_red.gif");
            $array_results[] = $object;
        }

        return $array_results;
    }

    public function insereFotos($id_registro)
    {
        $this->setIdTexto($id_registro);

        if (isset($_FILES['file']["name"]) && $_FILES['file']["name"] != "") {
            $this->setUrlImagem(uploadArquivo($_FILES["file"], 'uploads/textos/album', 'texto', true, array('/grandes/', '/medias/'), array(array(600, 490), array(500, 500))));
        }
        $this->setStatus('A');
        $id = $this->incluir();
        return $id;
    }

    public function getFotosEnviadas($id_fk)
    {
        $resultado = parent::getFotosEnviadas($id_fk);
        return $resultado;
    }

    public function deletaImagem($id_imagem_foto, $id_objeto)
    {
        $resultado = parent::deletaImagem($id_imagem_foto, $id_objeto);
        return $resultado;
    }
}
