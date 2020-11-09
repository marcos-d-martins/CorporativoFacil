<?php
/**
 * Description of AdmGalerias
 * Irá administrar(atualização de dados, inserção, e remoção) 
 * @author Marcos Daniel
 */
class AdmUsuario {
    private $id;
    private $dados;
    private $erro;
    private $resultado;    
    
    const tabelaBanco = 'usuarios';
    
    public function execucao(array $dados) {
        $this->dados = $dados;
        if(in_array('', $this->dados)):
            $this->resultado = false;
            $this->erro = ["Nenhum campo pode ficar em branco!", CORPF_LARANJADO];
        else:
            $this->formatacaoDeDados();
            $this->cadastrarUsuario();
        endif;
    }
    public function executarEdicao($id , array $dados) {
        $this->dados = $dados;
        $this->id = (int) $id;
        
        if(in_array('', $this->dados)):
            $this->resultado = false;
            $this->erro = ["Para atualizar a categoria <b>{$this->dados['nome']}</b> nenhum campo pode ficar em branco!", CORPF_LARANJADO];
        else:
            $this->formatacaoDeDados();
            $this->editarUsuario();
        endif;
    }
    
    public function getErro() {
        return $this->erro;
    }
    public function getResult() {
        return $this->resultado;
    }
    
    
    //AQUI PREVINE INSERÇÃO DE CÓDIGOS HTML;
    private function formatacaoDeDados() {
        $this->dados = array_map('strip_tags', $this->dados);
        $this->dados = array_map('trim', $this->dados);
        /*Aqui vão as 
        colunas do Banco:
           $variavel['coluna_banco'] = recebe os campos do formulário já 
                formatados pelos métodos que formatam datas e strings.  */ 
        
        //      colunas do banco  =     campos do formulário.
        $this->dados['nome'] = strip_tags(trim($this->dados['nome']));
        $this->dados['email'] = Verificacao::validadorDeEmail($this->dados['email']);
        $this->dados['telefone'] = Verificacao::validadorDeTelefone($this->dados['telefone']);
        //$this->dados['data_hoje'] = Verificacao::datas($this->dados['data_hoje']);
    }
    /** 
        método que verifica se uma categoria já existe no banco. Se sim, o método reescreve o nome da mesma.
    private function verificaCategoriaExistente() {
        //Aqui, estou vendo se já existe um id nesse formulário. Se sim, quer dizer que estou EDITANDO um registro.
        //Se não, não passo nenhum id e prossigo com o cadastro. 
        $where = ( !empty($this->id) ? "id != {$this->id} AND" : '');        
        $lerCategoriaExistente = new Ler();

        $lerCategoriaExistente->executarLeitura('categorias', "WHERE {$where} nome = :t", "t={$this->dados['nome']}");
        if($lerCategoriaExistente->resultado()):
            $this->dados['nome'] = $this->dados['nome'] . '-' . $lerCategoriaExistente->getRowCount();
        endif;
    }
    */
    private function cadastrarUsuario() {
        $insere = new Inserir();
        $insere->executarInsercao(self::tabelaBanco, $this->dados);
        if($insere->tuplasInseridas()):
            $this->resultado = $insere->getResult();
            $this->erro = ["<small style=font-family: '\"Ubuntu\",sans-serif'>Usuário <b> {$this->dados['nome']}</b> cadastrado no sistema</small>!", CORPF_VERDE];
        endif;
    }
    private function editarUsuario() {
        $edita = new Editar();
        $edita->executarEdicao(self::tabelaBanco, $this->dados, "WHERE id = :id", "id={$this->id}");
        if($edita->Resultados()):
            $this->resultado = true;
            $this->erro = ["Usuário <b>{$this->dados['nome']}</b> atualizado com sucesso!", CORPF_VERDE];
        endif;
    }
}