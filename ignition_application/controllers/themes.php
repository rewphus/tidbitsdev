<?php


class Themes extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view theme
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Theme');
        if(!$this->Theme->getThemes($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Theme->name, "Theme");
        $data['theme'] = $this->Theme;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('themes/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>