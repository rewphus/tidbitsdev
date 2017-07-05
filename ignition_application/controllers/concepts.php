<?php


class Concepts extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view concept
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Concept');
        if(!$this->Concept->getConcepts($GBID, $userID, false)){
            show_404();
        }

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Concept->name, "Concept");
        $data['concept'] = $this->Concept;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('concepts/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>