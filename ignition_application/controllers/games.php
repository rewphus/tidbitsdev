<?php


class Games extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view game
    function view($GBID, $page = 1)
    {   
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Game');
        if(!$this->Game->getGame($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Game->name, "Game");
        $data['game'] = $this->Game;

        // get event feed
        $this->load->model('Event');
        $data['events'] = $this->Event->getEvents(null, $GBID, null, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

        // get users who have game
        $this->load->model('Collection');
        $data['users'] = $this->Collection->getUsersWhoHavePlayedGame($GBID, $userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('games/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('games/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    function returnError($errorMessage,$errorProgressURL,$errorProgressCTA)
    {
        $result['error'] = true; 
        $result['errorMessage'] = $errorMessage;
        $result['errorProgressURL'] = $errorProgressURL; 
        $result['errorProgressCTA'] = $errorProgressCTA; 
        echo json_encode($result);
    }

    // add game
	function add()
	{
		// form validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('listID', 'listID', 'trim|xss_clean');
        // $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

		$GBID = $this->input->post('GBID');
        $listID = $this->input->post('listID');
        // $statusID = $this->input->post('statusID');
		$userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game id in database
        $this->load->model('Game');
        if(!$this->Game->getGame($GBID, null))
        {
            // failed to find game or add it to database
            $this->returnError($this->lang->line('error_game_cant_add'),false,false);
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
        
        // default value for auto selected platform
        $result['autoSelectPlatform'] = null;

        // load event model
        $this->load->model('Event');

        // if game isnt in collection
        if($collection == null) 
        {
            // add game to users collection
            $collectionID = $this->Collection->addToCollection($this->Game->gameID, $userID, $listID);

            // get platforms for game
            if($this->Game->getPlatforms($userID))
            {
                // if game has one platform
                if($this->Game->platforms != null && count($this->Game->platforms) == 1)
                {
                    // add game to platform in collection
                    if($this->Collection->addPlatform($collectionID, $this->Game->platforms[0]->GBID))
                    {
                        // tell UI to check platform that was auto-selected
                        $result['autoSelectPlatform'] = $this->Game->platforms[0]->GBID; 
                    }
                }
            }

            // get genres for game
            if($this->Game->getGenres($userID))
            {
                // if game has one genre
                if($this->Game->genres != null && count($this->Game->genres) == 1)
                {
                    // // add game to genre in collection

                }
            }

                        // get developers for game
            if($this->Game->getDevelopers($userID))
            {
                // if game has one developer
                if($this->Game->developers != null && count($this->Game->developers) == 1)
                {
                    // // add game to developer in collection

                }
            }

            // get publishers for game
            if($this->Game->getPublishers($userID))
            {
                // if game has one publisher
                if($this->Game->publishers != null && count($this->Game->publishers) == 1)
                {
                    // // add game to publisher in collection

                }
            }            
            // get themes for game
            if($this->Game->getThemes($userID))
            {
                // if game has one theme
                if($this->Game->themes != null && count($this->Game->themes) == 1)
                {
                    // // add game to theme in collection

                }
            } 
            // get franchises for game
            if($this->Game->getFranchises($userID))
            {
                // if game has one franchise
                if($this->Game->franchises != null && count($this->Game->franchises) == 1)
                {
                    // // add game to franchise in collection

                }
            }                       
            // record event
            $this->Event->addEvent($userID, $this->Game->gameID, $listID, null, null);
        // game is in collection, update list
        } else {
            $this->Collection->updateList($GBID, $userID, $listID);

            // record event
            $this->Event->addEvent($userID, $collection->GameID, $listID, null, null);
        }

        // get list name and style
        $listData = $this->Collection->getListDetails($listID);
        $result['listName'] = $listData->ListName;
        $result['listStyle'] = $listData->ListStyle;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
	}

    // change played status of game
    function changeStatus()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $statusID = $this->input->post('statusID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Collection->updateStatus($GBID, $userID, $statusID);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, $statusID, null);

        // get status name and style
        $statusData = $this->Collection->getStatusDetails($statusID);
        $result['statusName'] = $statusData->StatusName;
        $result['statusStyle'] = $statusData->StatusStyle;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // remove game from collection
    function remove()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $userID = $this->session->userdata('UserID');
        
        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // remove game from collection
        $this->load->model('Collection');
        $this->Collection->removeFromCollection($GBID, $userID);
       
        // return success
        $result['error'] = false;  
        echo json_encode($result);
    }
// ----------   Platform  ----------------------------------------------------------------------------------------------------------
    function addPlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on platform, add it
        if(!$this->Collection->isGameOnPlatformInCollection($collection->ID, $GBPlatformID))
        {
            // load platform model
            $this->load->model('Platform');

            // if platform isnt in db
            if(!$this->Platform->isPlatformInDB($GBPlatformID))
            {
                // get platform data 
                $platform = $this->Platform->getPlatform($GBPlatformID);

                // if API returned nothing
                if($platform == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add platform to db
                $this->Platform->addPlatform($platform);
            }

            // add game to platform in collection
            $this->Collection->addPlatform($collection->ID, $GBPlatformID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removePlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove platform from game in collection
        $this->Collection->removePlatform($collection->ID, $GBPlatformID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }
// ----------   Genre ----------------------------------------------------------------------------------------------------------
    function addGenre()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBGenreID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on genre, add it
        if(!$this->Collection->isGameOnGenreInCollection($collection->ID, $GBGenreID))
        {
            // load genre model
            $this->load->model('Genre');

            // if genre isnt in db
            if(!$this->Genre->isGenreInDB($GBGenreID))
            {
                // get genre data 
                $genre = $this->Genre->getGenre($GBGenreID);

                // if API returned nothing
                if($genre == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add genre to db
                $this->Genre->addGenre($genre);
            }

            // add game to genre in collection
            $this->Collection->addGenre($collection->ID, $GBGenreID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeGenre()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBGenreID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove genre from game in collection
        $this->Collection->removeGenre($collection->ID, $GBGenreID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }
// ----------   Developers   ----------------------------------------------------------------------------------------------------------
    function addDeveloper()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBDeveloperID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on developer, add it
        if(!$this->Collection->isGameOnDeveloperInCollection($collection->ID, $GBDeveloperID))
        {
            // load developer model
            $this->load->model('Developer');

            // if developer isnt in db
            if(!$this->Developer->isDeveloperInDB($GBDeveloperID))
            {
                // get developer data 
                $developer = $this->Developer->getDeveloper($GBDeveloperID);

                // if API returned nothing
                if($developer == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add developer to db
                $this->Developer->addDeveloper($developer);
            }

            // add game to developer in collection
            $this->Collection->addDeveloper($collection->ID, $GBDeveloperID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeDeveloper()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBDeveloperID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove developer from game in collection
        $this->Collection->removeDeveloper($collection->ID, $GBDeveloperID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }
// ----------   Publisher ----------------------------------------------------------------------------------------------------------
    function addPublisher()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPublisherID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on publisher, add it
        if(!$this->Collection->isGameOnPublisherInCollection($collection->ID, $GBPublisherID))
        {
            // load publisher model
            $this->load->model('Publisher');

            // if publisher isnt in db
            if(!$this->Publisher->isPublisherInDB($GBPublisherID))
            {
                // get publisher data 
                $publisher = $this->Publisher->getPublisher($GBPublisherID);

                // if API returned nothing
                if($publisher == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add publisher to db
                $this->Publisher->addPublisher($publisher);
            }

            // add game to publisher in collection
            $this->Collection->addPublisher($collection->ID, $GBPublisherID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removePublisher()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPublisherID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove publisher from game in collection
        $this->Collection->removePublisher($collection->ID, $GBPublisherID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }
// ----------   Theme ----------------------------------------------------------------------------------------------------------
    function addTheme()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBThemeID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on theme, add it
        if(!$this->Collection->isGameOnThemeInCollection($collection->ID, $GBThemeID))
        {
            // load theme model
            $this->load->model('Theme');

            // if theme isnt in db
            if(!$this->Theme->isThemeInDB($GBThemeID))
            {
                // get theme data 
                $theme = $this->Theme->getTheme($GBThemeID);

                // if API returned nothing
                if($theme == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add theme to db
                $this->Theme->addTheme($theme);
            }

            // add game to theme in collection
            $this->Collection->addTheme($collection->ID, $GBThemeID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeTheme()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBThemeID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove theme from game in collection
        $this->Collection->removeTheme($collection->ID, $GBThemeID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }
// ----------   Franchises  ----------------------------------------------------------------------------------------------------------
    function addFranchise()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBFranchiseID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on franchise, add it
        if(!$this->Collection->isGameOnFranchiseInCollection($collection->ID, $GBFranchiseID))
        {
            // load franchise model
            $this->load->model('Franchise');

            // if franchise isnt in db
            if(!$this->Franchise->isFranchiseInDB($GBFranchiseID))
            {
                // get franchise data 
                $franchise = $this->Franchise->getFranchise($GBFranchiseID);

                // if API returned nothing
                if($franchise == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add franchise to db
                $this->Franchise->addFranchise($franchise);
            }

            // add game to franchise in collection
            $this->Collection->addFranchise($collection->ID, $GBFranchiseID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeFranchise()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBFranchiseID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove franchise from game in collection
        $this->Collection->removeFranchise($collection->ID, $GBFranchiseID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }



    // change played status of game
    function saveProgression()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('currentlyPlaying', 'currentlyPlaying', 'trim|xss_clean');
        $this->form_validation->set_rules('hoursPlayed', 'hoursPlayed', 'trim|xss_clean');
        $this->form_validation->set_rules('dateCompleted', 'dateCompleted', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $currentlyPlaying = $this->input->post('currentlyPlaying');
        $hoursPlayed = $this->input->post('hoursPlayed');
        $dateCompleted = $this->input->post('dateCompleted');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Collection->updateProgression($collection->ID, $currentlyPlaying, $hoursPlayed, $dateCompleted);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'),false,false);
            return;
        }

        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, null, $currentlyPlaying);
       
        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}
?>