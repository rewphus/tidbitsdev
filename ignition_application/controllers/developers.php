<?php


class Developers extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view developer
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Developer');
        if(!$this->Developer->getDevelopers($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Developer->name, "Developer");
        $data['developer'] = $this->Developer;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('developers/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>