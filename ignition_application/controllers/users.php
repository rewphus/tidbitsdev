<?php

/*
|--------------------------------------------------------------------------
| Ignition ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class extends the functionality of Ignition. You can add your
| own custom logic here.
|
*/

require_once APPPATH.'/controllers/ignition/users.php';

class Users extends IG_Users {

    // view user
    function view($userID, $page = 1)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // paging
        $resultsPerPage = 20;
                      echo '<script>
      var x;
      x = "Page: ' . $page . '"
      console.log(x)</script>';

        // $offset = ($page-1) * $resultsPerPage;
         $offset = 1;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;

        // get event feed
        $this->load->model('Event');
        $data['events'] = $this->Event->getEvents($userID, null, null, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

        // get games currently playing
        $this->load->model('Collection');
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('user/profile/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    // view user collection
    function collection($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Collection");
        $data['user'] = $user;

        // get platforms, lists and statuses for filtering
        $this->load->model('Collection');
        $data['platforms'] = $this->Collection->getPlatformsInCollection($userID);
        $data['concepts'] = $this->Collection->getMetaInCollection($userID, 'concept');
        $data['lists'] = $this->Collection->getListsInCollection($userID);
        $data['statuses'] = $this->Collection->getStatusesInCollection($userID);

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/collection', $data);
        $this->load->view('templates/footer', $data);
    }

    // view user collection by platforms
    function platforms($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Platforms");
        $data['user'] = $user;

        // get users collections by platform
        $this->load->model('Collection');
        $data['platforms'] = $this->Collection->getCollectionByMeta($userID, 'platform');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/platforms', $data);
        $this->load->view('templates/footer', $data);
    } 
    
       // view user collection by concepts
    function concepts($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Concepts");
        $data['user'] = $user;

        // get users collections by concept
        $this->load->model('Collection');
        $data['concepts'] = $this->Collection->getCollectionByMeta($userID, 'concept');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/concepts', $data);
        $this->load->view('templates/footer', $data);
    }

    // view user collection by characters
    function characters($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Characters");
        $data['user'] = $user;

        // get users collections by character
        $this->load->model('Collection');
        $data['characters'] = $this->Collection->getCollectionByMeta($userID, 'character');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/characters', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by developers
    function developers($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Developers");
        $data['user'] = $user;

        // get users collections by character
        $this->load->model('Collection');
        $data['developers'] = $this->Collection->getCollectionByMeta($userID, 'developer');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/developers', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by franchises
    function franchises($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "franchise");
        $data['user'] = $user;

        // get users collections by franchise
        $this->load->model('Collection');
        $data['franchises'] = $this->Collection->getCollectionByMeta($userID, 'franchise');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/franchises', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by genres
    function genres($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Genres");
        $data['user'] = $user;

        // get users collections by genre
        $this->load->model('Collection');
        $data['genres'] = $this->Collection->getCollectionByMeta($userID, 'genre');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/genres', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by locations
    function locations($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Locations");
        $data['user'] = $user;

        // get users collections by location
        $this->load->model('Collection');
        $data['locations'] = $this->Collection->getCollectionByMeta($userID, 'location');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/locations', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by publishers
    function publishers($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Publishers");
        $data['user'] = $user;

        // get users collections by publisher
        $this->load->model('Collection');
        $data['publishers'] = $this->Collection->getCollectionByMeta($userID, 'publisher');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/publishers', $data);
        $this->load->view('templates/footer', $data);
    }

        // view user collection by themes
    function themes($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Themes");
        $data['user'] = $user;

        // get users collections by theme
        $this->load->model('Collection');
        $data['themes'] = $this->Collection->getCollectionByMeta($userID, 'theme');

        // get games currently playing
        $data['currentlyPlaying'] = $this->Collection->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/themes', $data);
        $this->load->view('templates/footer', $data);
    }

    function getCollection()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('userID', 'userID', 'trim|xss_clean');
        $this->form_validation->set_rules('page', 'page', 'trim|xss_clean');
        $this->form_validation->set_rules('filters', 'filters', 'xss_clean');
        $this->form_validation->run();

        $userID = $this->input->post('userID');
        $page = $this->input->post('page');
        $filters = json_decode($this->input->post('filters'));

        // check that user is VALID
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_user_invalid_id'),false,false);
            return;
        }

        // paging
        $resultsPerPage = 30;
        $offset = ($page-1) * $resultsPerPage;

        // get collection
        $this->load->model('Collection');
        $result['collection'] = $this->Collection->getCollection($userID, $filters, $offset, $resultsPerPage);
        $result['stats'] = $this->Collection->getCollection($userID, $filters, null, null);

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // add comment
    function comment()
    {
        // call Ignition comment function
        parent::comment();

        // if a comment for an event (comment type id = 2) then bump the last updated date stamp of the event
        if($this->input->post('commentTypeID') == 2) {
            $this->load->model('Event');
            $this->Event->bumpEvent($this->input->post('linkID'));
        }
    }

    // follow or unfollow a user
    function follow()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('followUserID', 'followUserID', 'trim|xss_clean');
        $this->form_validation->run();

        $followUserID = $this->input->post('followUserID');
        $userID = $this->session->userdata('UserID');

        
        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check following UserID is valid
        if($followUserID <= 0)
        {
            $this->returnError($this->lang->line('error_user_invalid_id'),false,false);
            return;
        }

        // follow or unfollow user
        $this->load->model('User');
        $result['followingUser'] = $this->User->followUser($userID, $followUserID);

        // return success
        $result['error'] = false;
        echo json_encode($result);
    }

    // export collection page
    function export()
    {
        // get logged in user
        $userID = $this->session->userdata('UserID');

        // if not logged in, 404
        if($userID == null)
            show_404();

        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);

        if($user == null)
            show_404();

        // page variables 
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Export");
        $data['user'] = $user;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/export', $data);
        $this->load->view('templates/footer', $data);
    }

    // export collection to csv file
    function exportCollection()
    {
        // get user
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // get collection data
        $this->load->model('Collection');
        $data = $this->Collection->getRawCollection($userID);

        // convert to csv
        $this->load->dbutil();
        $csv_data = $this->dbutil->csv_from_result($data);

        // create file
        $this->load->helper('download');
        force_download('gwl_export.csv', $csv_data);
    }
}