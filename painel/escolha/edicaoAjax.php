<?php
    
    $dadosATratar = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    $limpeza = array_map('strip_tags', $dadosATratar);
    $dadosEdicao = array_map('trim', $limpeza);
    echo "<pre>";
    print_r($dadosEdicao);
    echo "</pre>";
    
    $acao = $dadosEdicao['acao'];
    //echo json_encode($acao);
    echo json_encode($dadosEdicao);
    $id = $dadosEdicao['id'];
    
    if($acao):
        require '../../../Config/Conf.inc.php';
        $ler = new Ler();
        $editar = new Editar();
        $excluir = new Excluir();
    endif;
    
    
    unset($dadosEdicao['acao']);
    var_dump($ler);
    
    switch($acao):
        case 'editar':
            $ler->executarLeitura('publicacao', "WHERE id = {$dadosEdicao['id']}");
            //var_dump($ler);exit;
            if($ler->resultado()):
                //unset($dadosEdicao['id']);
                $leituraParaEdicao = $ler->resultado()[0];
                var_dump($leituraParaEdicao);
                //Bem aqui, tenho de destruir o ID para ele não ser editado junto aos outros campos.
                $editar->executarEdicao('publicacao', $leituraParaEdicao, "WHERE id = :id", "id={$id}");
                var_dump($editar);
            endif;
        break;
        
        
        default: $respostaJSON['erro'] = "Erro:";
    endswitch;