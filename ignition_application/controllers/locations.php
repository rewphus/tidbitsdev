<?php


class Locations extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view location
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Location');
        if(!$this->Location->getLocations($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Location->name, "Location");
        $data['location'] = $this->Location;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('locations/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>