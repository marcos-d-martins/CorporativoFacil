<?php
    
    $pegarDadosAjax = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    $limparCodigo = array_map('strip_tags', $pegarDadosAjax);
    $dados = array_map('trim', $limparCodigo);
    
    
    $acaoFormulario = $dados['acaoFormulario'];
    $dadosJson = array();
    unset($dados['acaoFormulario']);
    sleep(1);
    
    if($acaoFormulario):
        require '../../Config/Conf.inc.php';
        $inserir = new Inserir();
        $leitura = new Ler();
    endif;
    /*
        ? = nenhum caractere ou apenas 1 caractere. {0,1   }
        * = {0,} => ou nenhum ou infinitos algarismos/caracteres.  
        + = {1,} => pode ter um caractere ou pode ser infinito. 
    */
    switch($acaoFormulario):
        case 'cadastro':
            if(in_array('', $dados)):
                $dadosJson['erro'] = "Nenhum campo pode ficar em branco!";
            elseif(!preg_match('/^[a-z0-9\_\.\-]+@[a-z0-9\_\.\-]*[a-z]+[a-z]{2,4}$/', $dados['email'])):
                $dadosJson['erro'] = "O email deve ser válido! ";
            elseif(!preg_match('/^\([0-9]{2}\)[9]{1}\.[0-9]{4}\-[0-9]{4}$/', $dados['telefone'])):
                $dadosJson['erro'] = "Formato de telefone inválido! O formato deve ser (00)9.9999-9999";
            else:
                $leitura->consultaManual("SELECT id FROM usuarios WHERE email = :email", "email={$dados['email']}");
                if($leitura->resultado()):
                    $dadosJson['erro'] = "Esse email já foi usado! Informe outro.";
                else:
                    $inserir->executarInsercao('usuarios', $dados);
                    $dadosJson['deu_certo'] = "cadastro com sucesso!";
                endif;
            endif;
            break;
        default :
            $dadosJson['erro'] = "Erro:";
    endswitch;
        
    
    echo json_encode($dadosJson);