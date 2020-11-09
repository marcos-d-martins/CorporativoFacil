<?php
/**
 * Description of AdmGalerias
 * Irá administrar(atualização de dados, inserção, e remoção) 
 * @author Marcos Daniel
 */
class AdmPublicacoes {
    private $id;
    private $dados;
    private $id_autor;
    private $erro;
    private $resultado;
    
    const tabelaBanco = 'publicacao';
    
    public function executaCadastro(array $dados) {
        $this->dados = $dados;
        $this->id_autor = $this->dados['id_autor'];
        if(in_array('', $this->dados)):
            $this->erro = ["Nenhum campo pode ficar em branco!", CORPF_LARANJADO];
            $this->resultado = false;
        else:
            $this->formatacaoDeDados();
        
            if($this->dados['imagem']):
                $imagem = new Uploads();
                $imagem->formataImagem($this->dados['imagem'], $this->dados['descricao']);
            endif;
            
            if(isset($imagem) && $imagem->getResultados()):
                $this->dados['imagem'] = $imagem->getResultados();
                $this->cadastrarPublicacaoCategoria();
            else:
                $this->dados['imagem'] = null;
                $this->cadastrarPublicacaoCategoria();
                errosDoUsuarioCustomizados("A imagem não pode ser cadastrada no Banco de Dados.", CORPF_LARANJADO);
            endif;
        endif;
    }
    public function executaEdicao($id, array $dados) {
        $this->id = (int) $id;
        $this->dados = $dados;
        
        if(in_array('', $this->dados)):
            $this->resultado = false;
            $this->erro = ["Para editar <b>*{$this->dados['descricao']}*</b>, nenhum campo pode ficar em branco.", CORPF_LARANJADO];
        else:
            $this->formatacaoDeDados();
            $this->editarPublicacao();
        endif;
    }

    public function getErro() {
        return $this->erro;
    }
    public function getResult() {
        return $this->resultado;
    }
    
    public function registrarDataOriginal($data_requerida) {
        $this->dados['data_da_publicacao'] = $data_requerida;
        $insere = new Inserir();
        $insere->executarInsercao('datas', $this->dados);
        if($insere->getResult()):
            errosDoUsuarioCustomizados("A data original foi guardada", CORPF_VERDE);
        else:
            errosDoUsuarioCustomizados("Erro ao guardar a data", CORPF_LARANJADO);
        endif;
    }
    
    
    public function enviarGaleria(array $imagens, $idPost) {
        $this->dados = $imagens;
        $this->id = (int) $idPost;
        
        $image = new Ler();
        $image->executarLeitura(self::tabelaBanco, "WHERE id = :id", "id={$this->id}");
        if(!$image->resultado()):
            $this->erro = ["Erro ao enviar galeria: índice {$this->id} não encontrado.", CORPF_VERMELHO];
            $this->resultado = false;
        else:
            $image = $image->resultado()[0]['descricao'];
            
            $todasAsImagensGaleria = array();
            $qtdImagensGaleria = count($this->dados['tmp_name']);
            $indicesDasImagens = array_keys($this->dados);
            
            for($contador = 0; $contador < $qtdImagensGaleria; $contador++):
                foreach($indicesDasImagens AS $indices):
                    $todasAsImagensGaleria[$contador][$indices] = $this->dados[$indices][$contador];
                endforeach;
            endfor;
            
            $galeria = new Uploads();
            $imagens = 0;
            $qtdImagensEnviadas = 0;
            
            
        endif;
    }
    //AQUI PREVINE INSERÇÃO DE CÓDIGOS HTML;
    private function formatacaoDeDados() {
        $imagem = $this->dados['imagem'];
        $conteudo = $this->dados['conteudo'];
        unset($this->dados['imagem'], $this->dados['conteudo']);
        $this->dados = array_map('strip_tags', $this->dados);
        $this->dados = array_map('trim', $this->dados);        
        /*Aqui vão as 
        colunas do Banco:
           $variavel['coluna_banco'] = recebe os campos do formulário já 
                formatados pelos métodos que formatam datas e strings.  */
        $this->dados['descricao'] = Verificacao::tratamentoDeStrings($this->dados['descricao']);
        $this->dados['data_da_publicacao'] = Verificacao::datas($this->dados['data_da_publicacao']);
        $this->dados['imagem'] = ( $imagem ? $imagem : null );
        $this->dados['conteudo'] = $conteudo;
        $this->dados['id_categoria'] = ( $this->dados['id_categoria'] == 'null' ? null : $this->dados['id_categoria']);
        $this->dados['id_autor'] = (   $this->dados['id_autor'] == 'null' ? null : $this->dados['id_autor']   );
    }
    /** 
        método que verifica se uma categoria já existe no banco. Se sim, o método reescreve o nome da mesma.
    private function verificaCategoriaExistente() {
        $where = ( !empty($this->dados['id_categoria']) ? "ca.id = {$this->dados['id_categoria']} AND" : "");
        
        $lerCategoriaExistente = new Ler();
        $lerCategoriaExistente->executarLeitura('publicacao p', "LEFT JOIN categorias ca ON ca.id = p.id_categoria "
                . "WHERE {$where} id_categoria = :categoria", "categoria={$this->dados['id_categoria']}");
        if($lerCategoriaExistente->resultado()):
            errosDoUsuarioCustomizados("Essa categoria já existe no banco!", CORPF_LARANJADO);
            return false;
        else:
            //errosDoUsuarioCustomizados("Categoria cadastrada!", CORPF_VERDE);
            echo "A consulta no Banco de Dados está errada!";
            return true;
        endif;
    }
    */
    private function cadastrarPublicacaoCategoria() {
        $insere = new Inserir();
        $insere->executarInsercao(self::tabelaBanco, $this->dados);
        if($insere->getResult()):
            $this->erro = ["A publicação<b> {$this->dados['descricao']}</b> foi cadastrada no sistema", CORPF_VERDE];
            $this->resultado = $insere->getResult();
        endif;
    }
    private function editarPublicacao() {
        $editar = new Editar();
        $editar->executarEdicao(self::tabelaBanco, $this->dados, "WHERE id = :id", "id={$this->id}");
        if($editar->Resultados()):
            $this->resultado = true;
            $this->erro = ["Publicação <b>*{$this->dados['descricao']}*</b> editada com sucesso.", CORPF_VERDE];
            $this->registrarDataOriginal($this->dados['data_da_publicacao']);
        else:
            $this->resultado = false;
            $this->erro = ["erro", CORPF_AMARELO];
        endif;
    }
}