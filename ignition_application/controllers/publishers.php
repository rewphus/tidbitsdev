<?php


class Publishers extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view publisher
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Publisher');
        if(!$this->Publisher->getPublishers($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Publisher->name, "Publisher");
        $data['publisher'] = $this->Publisher;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('publishers/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>