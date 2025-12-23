<?php
    $obj_tipo_servico_calculadora = new TipoServicoCalculadora();
    $tipo_servico_calculadora = $obj_tipo_servico_calculadora;
    $tipo_servico_calculadora->setIdTable(getRequest("id_tipo_servico_calculadora"));
    $tipo_servico_calculadora->getByCod();
    echo json_encode($tipo_servico_calculadora);
?>