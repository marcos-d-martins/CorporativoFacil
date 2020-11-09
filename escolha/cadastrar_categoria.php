<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../../css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../../img/favicon.ico">

    <title>Cadastrar categoria</title>
    <style>
        .mostrar-usuario-na-sessao{
            position: absolute;
            right: 1em;
            top: 2.5em;
            border: 1px solid #08c;
            padding: 20px 35px;
            margin: 3.2em 0;
            display: inline-block;
            color: #fff;
            border-left: 2px solid #000;
            border-bottom: 2px solid #000;
            max-width: 12em;
            font-weight: 300;
        }
        .mostrar-usuario-na-sessao:hover{
            transition: 0.6s;
            background-color: #fff;
            color: #006699; 
        }
        
        .publicacoes{
            margin: 1em;
        }
    </style>
</head>
<body>
    <header>
        <?php
            session_start();
            date_default_timezone_set('Brazil/East');

            require('../../../Config/Conf.inc.php');   

            if(!class_exists('Autenticacao')):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../../formulario-login.php?acao=restrito');
                die;
            endif;
            $autentica = new Autenticacao();

            if(!$autentica->verificaLogin()):
                unset($_SESSION['autenticado']);
                header('Location: ../../formulario-login.php?acao=restrito');
                //var_dump($autentica);
            else:
                $usuario = $_SESSION['autenticado'];
            endif;
        
            if($_SESSION['autenticado']['nivel'] == 3):
                echo "<h2><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
            else:
                echo "<h2><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";                
            endif;     
            
            $categorias = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            
            if(isset($categorias['cadastrar_categoria'])):
                unset($categorias['cadastrar_categoria']);
                require('../modelos/AdmCategorias.class.php');
                $admCategorias = new AdmCategorias();
                $admCategorias->execucao($categorias);
                
                if($admCategorias->getResult()):
                    errosDoUsuarioCustomizados($admCategorias->getErro()[0], $admCategorias->getErro()[1]);
                    echo "<a class='btn_enviar' href='categorias.php' style='text-decoration: none;'>Clique para ver todas as categorias.</a>";
                else:
                    errosDoUsuarioCustomizados("Erro: nenhum cmapo pode ficar em branco.", CORPF_LARANJADO);
                endif;
            endif;
            /*$img = "IMAGEM EM PNG.png";
            $imFormatada = str_replace(" ", "-", strtolower($img));
            echo $imFormatada;
            echo "<hr>";
            $numero = 1;
            for($contador = 1; $contador <= 10; $contador++):
                echo "<br>".str_pad($contador, 2, 0, STR_PAD_LEFT);
            endfor;*/
        ?>
    </header>
    <main>
        <button id="voltar">Voltar</button>
        <header>
            <h1 class="titulo_campo">cadastrar categoria</h1>
        </header>
        <section>
            <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['usuario'];?></b>
            Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
            </span>
            <form name="formulario_categorias" method="post">
                <label>
                    <span class="titulo_campo">Título da categoria</span>
                    <input type="text" name="titulo" class="campos_formulario" value="<?php if(isset($categorias['titulo'])): echo $categorias['titulo'];  endif; ?>" autofocus>
                </label>

                <label>
                    <span class="titulo_campo">conteúdo</span>
                    <textarea class="textarea_conteudo" name="conteudo"><?php if(isset($categorias['conteudo'])): echo $categorias['conteudo'];  endif;?></textarea>
                </label>

                <label>
                    <span class="titulo_campo">Data</span>
                    <input type="text" class="campos_formulario" placeholder="informe a hora" name="data_hoje" value="<?= date('d/m/Y H:i:s');?>">
                </label>

                <input type="submit" class="btn_enviar" value="cadastrar categoria" name="cadastrar_categoria">
                <!--<img src="../../../img/ajax-loader.gif" class="gif_formulario" alt="[carregando...]" title="CARREGANDO">-->
            </form>
        </section>
    </main>
    <script src="../../../js/jQuery.js" type="text/javascript"></script>
    <!--<script src="../../../js/script.js" type="text/javascript"></script>-->
    <script src="../../../js/enviar-dados.js" type="text/javascript"></script>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
           history.back();
        });
    </script>
</body>
</html>