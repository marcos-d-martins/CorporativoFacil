<?php

    require '../../../Config/Conf.inc.php';
    
    $lerDados = new Ler();
    $lerDados->executarLeitura('categorias');
    $tpl = file_get_contents('categories.tpl.html');
    
    foreach($lerDados->resultado() AS $dados):
        extract($dados);
        
        $dados['pubdate'] = date('Y-d-m H:i', strtotime($dados['data_hoje']));
        $dados['data_hoje'] = date('d/m/Y H:i',  strtotime($dados['data_hoje'])) . ' horas';
        
        $links = explode('&','#'.implode('#&#', array_keys($dados)).'#');
        echo str_replace($links,  array_values($dados),$tpl);
    endforeach;

