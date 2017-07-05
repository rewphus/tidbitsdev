<?php


class Characters extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view character
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Character');
        if(!$this->Character->getCharacters($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Character->name, "Character");
        $data['character'] = $this->Character;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('characters/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>