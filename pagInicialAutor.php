<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../css/fonticon.css" rel="stylesheet" type="text/css"/>
        <link href="../../css/painel.css" rel="stylesheet" type="text/css"/>
        <link href="../../css/bot.css" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;500;600&display=swap" rel="stylesheet">
        <link rel="shortcut icon" href="../../img/favicon.ico">
        <title>Corporativo Fácil</title>
        <style>            
            .elementos{
                display: flex;
                max-width:1800px;
                margin-bottom: 6em;
                flex-basis: calc(800px - 115px);
                text-align: center;
                //justify-content: flex-start;       
            }
            body{
                margin: 5px;
            }
        </style>
    </head>
    <?php 
        session_start();
        date_default_timezone_set('Brazil/East');
    ?>
    <body>
        
        <?php
            $acao = filter_input(INPUT_GET, 'acao', FILTER_DEFAULT);
            if($acao == 'naoAutorizado'):
                errosDoUsuarioCustomizados("Você tentou acessar uma página interna sem passar pela pág Inicial.", CORPF_AMARELO);
            endif;
        
            $diaSemana = date('w');
            $diasEmPortugues = array(
                            0 => "Domingo",
                            1 => "Segunda-feira",
                            2 => "Terça-feira",
                            3 => "Quarta-feira",
                            4 => "Quinta-feira",
                            5 => "Sexta-feira",
                            6 => "Sábado"   );
            $mes = date('m');
            $mesEmPortugues = array(
                                    "01"=>"Janeiro",
                                    "02"=>"Fevereiro",
                                    "03"=>"Março",
                                    "04"=>"Abril",
                                    "05"=>"Maio",
                                    "06"=>"Junho",
                                    "07"=>"Julho",
                                    "08"=>"Agosto",
                                    "09"=>"Setembro",
                                    "10"=>"Outubro",
                                    "11"=>"Novembro",
                                    "12"=>"Dezembro"
            );
                    //terça     = date(2);
                    //Abril     = date(04);
            echo $diasEmPortugues[$diaSemana] . " , dia <b>" . date('d') . "</b> de <b>" . $mesEmPortugues[$mes] . "</b> de <b>" . date('Y') . "</b>" .". ". date('H')." horas, ".date('i')." minutos e " . date('s') . " segundos";
            require('../../Config/Conf.inc.php');
            
            //echo Verificacao::imagens('contato.jpg', "FotoDeteste","../../");
                        
            $autentica = new Autenticacao(1);
            $sair = filter_input(INPUT_GET, 'sair', FILTER_VALIDATE_BOOLEAN);
            
            if(!$autentica->verificaLogin()):
                unset($_SESSION['autenticado']);
                header('Location: ../formulario-login.php?acao=restrito');
                //var_dump($autentica);
            else:
                $usuario = $_SESSION['autenticado'];
            endif;
            
            
            
            if($sair):
                unset($_SESSION['autenticado']);
                header('Location: ../formulario-login.php?acao=sair');
            endif;
            echo "<hr>";
        ?>        
        <header class="cabecalho_principal">
            <div class="conteudo_cabecalho">
                <img src="../../img/empresa2.png" class="foto" alt="logotipo_corpFacil">
                <nav class="menu">
                    <ul>
                        <li class="usuario_autor">Bem vindo, <?= $usuario['usuario']?> !</li>
                        <li><a href="pagInicialAutor.php?sair=true" class="botao_sair" style="margin: 0">sair</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main>
            <div class="cta">
                <article class="cta_content">
                    <header>
                        <h1>Aqui você tem o necessário para automatizar o fluxo de sua empresa. Descubra as vantagens de trabalhar com a gente!</h1>
                    </header>

                    <p>Domine HTML5 e CSS3 de uma vez por todas!</p>
                </article>
            </div>
            <div class="elementos">
                <nav class="categorias">
                    <ul>
                        <li><a href="#" class="itens_menu">Seções</a></li>
                        <li><a href="#" class="itens_menu">Postagens</a></li>
                        <li><a href="escolha/galerias.php" class="itens_menu">Galerias</a></li>
                        <li><a href="escolha/publicacoes.php" class="itens_menu">Publicações</a></li>
                        <li><a href="escolha/categorias.php" class="itens_menu">Categorias</a></li>
                        <li><a href="../ajax/ajax.php" class="itens_menu">AjaxPHP</a></li>
                    </ul>
                </nav>
                <article class="suas_publicacoes">
                    <header>
                        <h1>Suas publicações</h1>
                    </header>
                    <?php
                        $usuario = $_SESSION['autenticado']['usuario'];

                        $lerPublicacoes = new Ler();
                        $lerPublicacoes->consultaManual("SELECT u.nome AS autor, p.descricao AS titulo_publicacao,
                            p.conteudo AS conteudo FROM usuarios u LEFT JOIN publicacao p ON u.id = p.id_autor
                            WHERE u.usuario = '{$usuario}' GROUP BY p.descricao, p.conteudo");

                        if($lerPublicacoes->resultado()):
                            foreach($lerPublicacoes->resultado() AS $publicDESTEAutor):
                                extract($publicDESTEAutor);
                    ?>
                        <div class="titulo_publicacao">
                            <h1><?= $publicDESTEAutor['titulo_publicacao']; ?></h1>
                        </div>
                        <a href="escolha/publicacoes.php" class="suas_publicacoes" id="clique">                        
                            <section class="publicacoes">
                                <h2><?= $publicDESTEAutor['conteudo']; ?></h2>
                            </section>
                            <?php
                                    endforeach;
                                else:
                                    errosDoUsuarioCustomizados("Você não publicou nada ainda. ", CORPF_LARANJADO);
                                    echo "<a class='sem_resultados' href='cadastrar_publicacao.php' style='text-decoration: none;'>Clique aqui para publicar</a>";
                                endif;
                            ?>
                        </a>
                </article>
            </div>
        <?php 
            //SISTEMA DE NAVEGAÇÃO .
            /*
            $ativar = filter_input(INPUT_GET, 'ativar', FILTER_DEFAULT);
            
            if(!empty($ativar)):
                $incluir = __DIR__ . '\\escolha\\' . strip_tags(trim($ativar)) . 'php';
            else:
                $incluir = __DIR__;
                echo $incluir;
            endif;
            
            if(file_exists($incluir)):
                require_once($incluir);
            else:
                echo "<div class=\"content notfound\">";
                errosDoUsuarioCustomizados("Erro ao incluir página <b>/{$ativar}.php!</b>", CORPF_VERMELHO);
                echo "</div>";
            endif;*/
        ?>
        </main>
        <!--<audio src="../../img/cornucopia.mp3" controls>Overkill's cover of Black Sabbath's Cornucopia.</audio>-->
        <script>
            $("#id").on("click",function(){
                $(this).css("border","1px solid yellow");
            });
        </script>
        <script src="../../js/jQuery.js" type="text/javascript"></script>
        <script src="../../js/script.js" type="text/javascript"></script>
        
    </body>
</html>