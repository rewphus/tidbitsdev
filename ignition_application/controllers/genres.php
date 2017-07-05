<?php


class Genres extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view genre
    function view($GBID, $page = 1)
    {
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Genre');
        if(!$this->Genre->getGenres($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Genre->name, "Genre");
        $data['genre'] = $this->Genre;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('genres/header', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>