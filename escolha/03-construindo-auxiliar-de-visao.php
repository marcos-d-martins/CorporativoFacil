<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require '../../../Config/Conf.inc.php';
            
            Viewing::loadTemplate('categories');
            
            $leitura = new Ler();
            $leitura->executarLeitura('categorias');
            
            foreach($leitura->resultado() AS $categorias):
                Viewing::requisicao("categories", $categorias);
            endforeach;
                
        ?>
    </body>
</html>
