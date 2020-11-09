<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../../css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../../img/favicon.ico">
    
    <style>
        body{
            background: #333;
        }
    </style>
    
    
    <title>Editar publicação</title>
</head>
<body class="editar_post">
    <header>
        <?php
            session_start();
            date_default_timezone_set('Brazil/East');
            require('../../../Config/Conf.inc.php');   

            if(!class_exists('Autenticacao')):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../index.php');
                die;
            endif;
            
            
            $id = filter_input(INPUT_GET,'fw', FILTER_DEFAULT);
            echo "<h1>Publicação id " . $id . "</h1>";
           
            if(isset($id)):
                $id = $id / 1024 / 1024 / 3;
            endif;
            
            $publicacao = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if(isset($publicacao['editar_publicacao'])):
                unset($publicacao['editar_publicacao']);
            
                require('../modelos/AdmPublicacoes.class.php');
                
                $admPublicacoes = new AdmPublicacoes();
                $admPublicacoes->executaEdicao($id, $publicacao);
                
                if($admPublicacoes->getResult()):
                    errosDoUsuarioCustomizados($admPublicacoes->getErro()[0], $admPublicacoes->getErro()[1]);
                    echo "<a class='btn_enviar' href='publicacoes.php' style='text-decoration: none;'>Clique para ver todas as publicações.</a>";
                endif;
                
            else:
                $lerDadosParaEdicao = new Ler();
                $lerDadosParaEdicao->executarLeitura('publicacao', "WHERE id = :id", "id={$id}");
                
                if(!$lerDadosParaEdicao->resultado()):
                    //Nesse bloco é quando tentei atualizar uma categoria que não existe.
                    header('Location:publicacoes.php?msg=false');
                else:
                    /* 
                      Bem aqui, peguei a variável $publicacao e reescrevi a mesma, 
                        alimentando-a com os dados da leitura no Banco de Dados(AFINAL, É UM FORMULÁRIO DE EDIÇÃO, 
                        OU SEJA, ME TRARÁ OS DADOS JÁ PRONTOS PARA EDITAR ALGUM CAMPO) ao invés 
                        de recuperar os dados do formulário.
                     */
                    $publicacao = $lerDadosParaEdicao->resultado()[0];
                endif;
            endif;
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
            <h1 class="titulo_campo">Editar publicação</h1>
            <h2>
                <?php
                    if($_SESSION['autenticado']['nivel'] == 3):
                        echo "<h1><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
                    else:
                        echo "<h1><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
                    endif;
                ?>
            </h2>
        </header>
        <article>
            <header>
                <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['usuario'];?></b> |
                Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
                </span>
            </header>
            <form action="" name="formulario_publicacoes" method="post">
                <label>
                    <span class="titulo_campo">Título da publicação</span>
                    <input type="text" name="descricao" class="campos_formulario" value="<?php if(isset($publicacao['descricao'])): echo $publicacao['descricao'];  endif; ?>" autofocus>
                </label>

                <label>
                    <span class="titulo_campo">Conteúdo</span>
                    <textarea class="textarea_conteudo" name="conteudo"><?php if(isset($publicacao['conteudo'])): echo $publicacao['conteudo'];  endif;?></textarea>
                </label>
                <span class="titulo_campo">Carregar capa da galeria(JPEG ou PNG)</span>
                <input  type="file" class="campos_formulario_arquivo" name="imagem" value="<?php if(isset($publicacao['imagem'])): echo $publicacao['imagem']; endif; ?>">
                <label>
                    <span class="titulo_campo">Data</span>
                    <input type="text" class="campos_formulario" placeholder="informe a hora" name="data_da_publicacao" value="<?= date('d/m/Y H:i:s');?>">
                </label>

                <label>
                    <span class="titulo_campo">Categoria</span>
                    <select name="id_categoria" class="titulo_campo">

                        <option value="null">Escolha uma categoria: </option>
                        <?php
                            $lerInformacoes = new Ler();
                            $lerInformacoes->consultaManual("SELECT DISTINCT ca.id AS id_cat,
                                     p.id_categoria AS categoria_da_publicacao,
                                     ca.titulo AS titulo, ca.conteudo AS conteudo, au.nome AS autor 
                                     FROM categorias ca LEFT JOIN publicacao p ON p.id_categoria = ca.id
                                     LEFT JOIN autor au ON au.id = p.id_autor GROUP BY ca.titulo
                                     ORDER BY titulo ASC");

                            if(!$lerInformacoes->resultado()):
                                echo "<option disabled='disabled'>Não encontrou a categoria que procura? Cadastre uma nova</option>";
                            else:
                                foreach($lerInformacoes->resultado() AS $categorias):
                                    //var_dump($lerInformacoes);
                                    echo "<option value=\"{$categorias['id_cat']}\" ";                                
                                    if($categorias['id_cat'] == $publicacao['id_categoria']):
                                        echo " selected='selected' ";
                                    endif;
                                    echo ">{$categorias['titulo']}</option>";

                                endforeach;
                            endif;
                        ?>
                    </select>
                </label>
                <label>
                    <span class="titulo_campo">Autor</span>
                    <select name="id_autor" class="titulo_campo">
                        <option value="null">Selecione um autor</option>
                        <?php
                            $lerAutor = new Ler();
                            $lerAutor->consultaManual("SELECT DISTINCT u.id AS id_autor,
                                p.id_autor, u.nome AS autor 
                                FROM usuarios u LEFT JOIN publicacao p ON u.id = p.id_autor");

                            if(!$lerAutor->resultado()):
                                 echo "<option disabled='disabled'>Não há autores</option>";
                            else:
                                foreach($lerAutor->resultado() AS $autores):
                                    echo "<option value=\"{$autores['id_autor']}\" ";
                                    if($autores['id_autor'] == $publicacao['id_autor']):
                                        echo " selected=\"selected\"  ";
                                    endif;

                                    echo " style = 'color:black;'><b> &rsaquo; </b>{$autores['autor']}</option>";
                                endforeach;
                            endif;
                        ?>
                    </select>
                </label>

                <input type="submit" class="btn_enviar" value="Editar publicacao" name="editar_publicacao">
            </form>
        </article>
    </main>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
          selector: '#publicar',
          language: 'pt_BR'
        });
    </script>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
           history.back();
        });
    </script>
</body>
</html>