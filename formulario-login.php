<?php 
    session_start();
    require('../Config/Conf.inc.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width initial-scale=1">
        <meta charset="UTF-8">
        <title>Entre no painel Administrativo</title>
        <link href="../css/fonticon.css" rel="stylesheet" type="text/css"/>
        <link href="../css/estiloDoLogin.css" rel="stylesheet">
        <link href="../css/painel.css" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <form action="" method="post" class="box" enctype="multipart/form-data">
        <?php
            $autenticar = new Autenticacao();

            $campos = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if(!empty($campos['formulario'])):
                $autenticar->botao_login($campos);
                if($autenticar->getResultado() && $campos['tipoDeUsuario'] == 3):
                    //SE O USUÁRIO ESTIVER AUTENTICADO, COLOCO ELE PARA DENTRO DO PAINEL. 
                    header('Location:painel/pagInicial.php');
                elseif($autenticar->getResultado() && $campos['tipoDeUsuario'] == 1):
                    //SE O USUÁRIO ESTIVER AUTENTICADO, COLOCO ELE PARA DENTRO DO PAINEL. 
                    header('Location:painel/pagInicialAutor.php');
                else:
                    errosDoUsuarioCustomizados($autenticar->getErro()[0], $autenticar->getErro()[1]);
                endif;

            endif;
            
            
            $semAcesso = filter_input(INPUT_GET, 'acao', FILTER_DEFAULT);
            
            if(isset($semAcesso)):
                if($semAcesso == 'restrito'):
                    errosDoUsuarioCustomizados("Informe usuário e senha para entrar!", CORPF_VERMELHO);
                elseif($semAcesso == 'sair'):
                    errosDoUsuarioCustomizados("Você saiu! Volte logo.", CORPF_VERDE);
                endif;
            endif;
            ?>
                
            <h1>Entre</h1>
            <label class="radio"> <input type="radio" name="tipoDeUsuario" value=3 class="radio_button">Administrador</label>
            <label class="radio"> <input type="radio" name="tipoDeUsuario" value=1 class="radio_button">Autor </label>
            
            <input type="text" name="usuario" placeholder="Usuário"  autofocus>
            <input type="password" name="senha" placeholder="Senha">
            
            <input type="submit" name="formulario" value="Entrar">
        </form>
    </body>
</html>