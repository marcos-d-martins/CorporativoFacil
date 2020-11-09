<?php

    require '../../../Config/Conf.inc.php';
    
    $lerDados = new Ler();
    $lerDados->executarLeitura('categorias');
    $tpl = file_get_contents('categories.tpl.html');
    var_dump($tpl);
    
    foreach($lerDados->resultado() AS $dados):
        extract($dados);
        
    endforeach;
    