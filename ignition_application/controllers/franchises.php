<?php


class Franchises extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view franchise
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Franchise');
        if(!$this->Franchise->getFranchises($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Franchise->name, "Franchise");
        $data['franchise'] = $this->Franchise;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('franchises/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>