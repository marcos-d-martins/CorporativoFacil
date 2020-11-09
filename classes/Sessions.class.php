<?php
/**
 * Description of Sessions
 *
 * @author Marcos Daniel
 */
class Sessions {
    private $date;
    private $cache;
    private $traff;
    private $browser;
    
    function __construct($cache=null) {
        //session_start();
        $this->checkSession($cache);
    }
    /* 
        Visualizacoes só serão alteradas quando iniciar a sessão;
        Usuarios só serão alteradas quando iniciar cookies;
        Paginas só serão alteradas quando a página for recarregada.
     */
    //Verifica e executa todsos os métodos da classe
    public function checkSession( $cache = null ) {
        $this->date = date('Y-m-d');
        $this->cache = (  (int) $cache ? $cache : 20  );
        
        if(empty($_SESSION['onlineuser'])):
            $this->checkBrowser();
            $this->setTraffic();
            $this->setSession();
            $this->setUsuario();
            
            $this->browserUpdate();
        else:
            $this->setSession();
            $this->updateUser();
            $this->checkBrowser();
            $this->trafficUpdate();
            $this->sessionUpdate();            
            $this->setUsuario();
        endif;
        
        $this->date = null;
    }
    private function setSession() {
        $_SESSION['onlineuser'] = [
                    "online_session" => session_id(),
                    "viewstart_moment" => date('Y-m-d H:i:s'),
                    "viewend_moment" => date('Y-m-d H:i:s', strtotime("+{$this->cache}minutes")),
                    "online_ip" => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
                    "online_url" => filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT),
                    "online_agent" => filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT)
        ];
    }
    private function sessionUpdate() {
        $_SESSION['onlineuser']['viewend_moment'] = date('Y-m-d H:i:s', strtotime("+{$this->cache}minutes"));
        $_SESSION['onlineuser']['online_url'] = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT);
    }
    
    //CRIA A SESSÃO.
    private function setTraffic() {
        $this->getTraffic();
        /* Entro nesse bloco caso a tabela de TRAFEGO esteja vazia; 
             assim, crio o primeiro resultado do dia.    */
        if(!$this->traff):
            $viewSet = [    'data_visualizacao' => $this->date,
                            'usuarios' => 1,
                            'visualizacoes' => 1,
                            'paginas' => 1
                        ];
            $inserir = new Inserir();
            $inserir->executarInsercao('trafego', $viewSet);
        else:
            if(!$this->getCookies()):
                $viewSet = ['usuarios' => $this->traff['usuarios'] + 1,
                            'visualizacoes' => $this->traff['visualizacoes'] + 1,
                            'paginas' => $this->traff['paginas'] + 1  ];
            else:
                $viewSet = ['visualizacoes' => $this->traff['visualizacoes'] + 1,
                            'paginas' => $this->traff['paginas'] + 1  ];             
            endif;
            $atualizarVisualizacoes = new Editar();
            $atualizarVisualizacoes->executarEdicao("trafego", $viewSet, "WHERE data_visualizacao = :date", "date={$this->date}");       
        endif;
    }
    
    
    //verifica e atualiza visualizações por página.
    private function trafficUpdate() {
        $this->getTraffic();
        $viewSet = ['paginas' => $this->traff['paginas'] + 1  ];
        $atualizarVisualizacoesPag = new Editar();
        $atualizarVisualizacoesPag->executarEdicao("trafego", $viewSet, "WHERE data_visualizacao = :date", "date={$this->date}");       
        
        $this->traff = null;
    }
    
    

    private function getTraffic() {
        $getViews = new Ler();
        $getViews->executarLeitura("trafego", "WHERE data_visualizacao = :date", "date={$this->date}");
        if($getViews->getRowCount()):
            $this->traff = $getViews->resultado()[0];
        else:
            echo "erro";
        endif;
    }
    
    private function getCookies() {
        $cookies = filter_input(INPUT_COOKIE,'onlineuser', FILTER_DEFAULT);
        setcookie("onlineuser",  base64_encode("Marcos"),  time() + 86400);
        
        if(!$cookies):
            return false; 
        else:
            return true;
        endif;        
    }
    private function checkBrowser() {
        $this->browser = $_SESSION['onlineuser']['online_agent'];
        if(strpos($this->browser,'Chrome')):
            $this->browser = 'Chrome';
        elseif(strpos($this->browser,'Firefox')):
            $this->browser = 'Firefox';
        elseif(strpos($this->browser,'Trident') || strpos($this->browser,'MSIE')):
            $this->browser = 'IE';
        else:
            $this->browser = 'Outros navegadores';
        endif;
    }
    
    
    //Atualiza tabela com dados de navegadores.
    private function browserUpdate() {
        $lerNavegadores = new Ler();
        $lerNavegadores->executarLeitura('acesso_por_navegador', "WHERE online_agent = :name", "name={$this->browser}");
        if(!$lerNavegadores->resultado()):
            $arr = ['online_agent' => $this->browser, 'agent_views' => 1 ];
            $insereNavegador = new Inserir();
            $insereNavegador->executarInsercao('acesso_por_navegador', $arr);
        else:
            $arr = ['agent_views' => $lerNavegadores->resultado()[0]['agent_views'] + 1 ];
            $atualiza = new Editar();
            $atualiza->executarEdicao('acesso_por_navegador', $arr, "WHERE online_agent = :name", "name={$this->browser}");
        endif;
    }    
    
    /**                         ///
            USUÁRIOS ONLINE
     */                        // //
    
    /**
      Cadastra usuário na tabela.
     */
    private function setUsuario() {
        $online = $_SESSION['onlineuser'];
        $online['online_agent'] = $this->browser;
        
        $novoUsuario = new Inserir();
        $novoUsuario->executarInsercao('acessos', $online);
        //var_dump($novoUsuario);
    }
    
    private function updateUser(){
        
    }
    
}
