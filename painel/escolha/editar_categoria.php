<!DOCTYPE html>
<html>
<?php
    session_start();
    date_default_timezone_set('Brazil/East');
    require('../../../Config/Conf.inc.php');
    
    if(!class_exists('Autenticacao')):
        errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
        header('Location:../index.php');
        die;
    endif;    
?>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../../css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;500;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../../img/favicon.ico">
    <title>Editar categoria</title>
</head>
<body>
    <header>
        <?php

            $autentica = new Autenticacao();
            if(!$autentica->verificaLogin()):
                unset($_SESSION['autenticado']);
                header('Location: ../../formulario-login.php?acao=restrito');
                //var_dump($autentica);
            else:
                $usuario = $_SESSION['autenticado'];
            endif;
            if(!class_exists('Autenticacao')):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../pagInicial.php?acao=naoAutorizado');
                die;
            endif;
            $categorias = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            $id = filter_input(INPUT_GET, 'fl',FILTER_VALIDATE_INT);            
            
            if(isset($id)){
                $id = $id / 749 / 785 / 999;
            }
            
            if(isset($categorias['editar_categoria'])):
                unset($categorias['editar_categoria']);
                require('../modelos/AdmCategorias.class.php');
                
                $admCategorias = new AdmCategorias();
                $admCategorias->executarEdicao($id, $categorias);
                
                if(!$admCategorias->getResult()):
                    errosDoUsuarioCustomizados($admCategorias->getErro()[0], $admCategorias->getErro()[1]);
                else:
                    errosDoUsuarioCustomizados($admCategorias->getErro()[0], $admCategorias->getErro()[1]);
                    echo "<a class='btn_enviar' href='categorias.php' style='text-decoration: none;'>Clique para ver todas as categorias.</a>";
                endif;
            else:
                $lerDadosParaEdicao = new Ler();
                $lerDadosParaEdicao->executarLeitura("categorias", "WHERE id = :id", "id={$id}");
                
                if(!$lerDadosParaEdicao->resultado()):
                    echo "Erro";
                else:
                    $categorias = $lerDadosParaEdicao->resultado()[0];
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
            <h1 class="titulo_campo">Editar categoria</h1>
            <h2>
                <?php
                    if($_SESSION['autenticado']['nivel'] == 3):
                        echo "<h1><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
                    else:
                        echo "<h1><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2> Entrou no bloco de autor";
                    endif;
                ?>
            </h2>
        </header>
        <article>
            <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['usuario'];?></b> |
                Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
            </span>
            <form action="" name="formulario_categorias" method="post">
                <label>
                    <span class="titulo_campo">Título da categoria</span>
                    <input type="text" name="titulo" class="campos_formulario" value="<?php if(isset($categorias['titulo'])): echo $categorias['titulo'];  endif; ?>" autofocus>
                </label>

                <label>
                    <span class="titulo_campo">conteúdo</span>
                    <textarea class="textarea_conteudo" name="conteudo"><?php if(isset($categorias['conteudo'])): echo $categorias['conteudo'];  endif;?></textarea>
                </label>

                <label>
                    <span class="titulo_campo">criada em:</span>
                    <input type="text" class="campos_formulario" placeholder="informe a hora" name="data_hoje" value="<?php if(isset($categorias['data_hoje'])): echo date('d/m/Y H:i:s', strtotime($categorias['data_hoje'])); endif;?>">
                </label>

                <input type="submit" class="btn_enviar" value="editar categoria" name="editar_categoria">
            </form>
        </article>
    </main>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
           history.back();
        });
    </script>
</body>
</html>