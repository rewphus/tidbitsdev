<?php

class Search extends CI_Controller {
	
	// search page
	function index($query = '', $page = 1)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('query', 'Query', 'trim|xss_clean');

		// page variables
		$this->load->model('Page');
        $data = $this->Page->create("Search", "Search");
		$data['searchQuery'] = $query = $query == '' ? $this->input->post('query') : str_replace("%20", " ", $query);
		$data['searchPage'] = $page;

		// search for game
		if($query != '') {
			$this->load->model('GiantBomb');
			$resultsPerPage = 10;
			$result = $this->GiantBomb->searchForGame($query, $page, $resultsPerPage, $this->session->userdata('UserID'));
			$data['searchResults'] = $result;
		}

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('search', $data);

		if($this->session->userdata('UserID') != null) {
			
			// get user data
			$this->load->model('User');
			$user = $this->User->getUserByIdWithFollowingStatus($this->session->userdata('UserID'), $this->session->userdata('UserID'));

			// page variables
			$data['user'] = $user;

			// get users collections by platform
			$this->load->model('Collection');
			$data['platforms'] = $this->Collection->getCollectionByMeta($this->session->userdata('UserID'), 'platform');
			$data['concepts'] = $this->Collection->getCollectionByMeta($this->session->userdata('UserID'), 'concept');

			if($user == null)
				show_404();
			
				$this->load->view('templates/profilebar', $data);
		}

				$this->load->view('templates/footer', $data);
	}

	
}
?>