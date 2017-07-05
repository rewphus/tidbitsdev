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
        $this->form_validation->set_rules('motivationID', 'motivationID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');
        $this->form_validation->set_rules('futureID', 'futureID', 'trim|xss_clean');
        $this->form_validation->set_rules('valueID', 'valueID', 'trim|xss_clean');

		$GBID = $this->input->post('GBID');
        $listID = $this->input->post('listID');
        $motivationID = $this->input->post('motivationID');
        $statusID = $this->input->post('statusID');
        $futureID = $this->input->post('futureID');
        $valueID = $this->input->post('valueID');
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

                    // add game to meta in collection
                    if($this->Collection->addMeta($collectionID, 'platform', $this->Game->platforms[0]->GBID))
                    {
                        // tell UI to check platform that was auto-selected
                        $result['autoSelectPlatform'] = $this->Game->platforms[0]->GBID; 
                    }

                }
            }

            // get concepts for game
            if($this->Game->getConcepts($userID))
            {
                foreach($this->Game->concepts as $concept)
				{
                    //add concepts to collection
                    $this->Collection->addMeta($collectionID, 'concept', $concept->GBID);
                }
            }

            // get developers for game
            if($this->Game->getDevelopers($userID))
            {
                foreach($this->Game->developers as $developer)
				{
                    //add developers to collection
                    $this->Collection->addMeta($collectionID, 'developer', $developer->GBID);
                }
            }

            // get publishers for game
            if($this->Game->getPublishers($userID))
            {
                foreach($this->Game->publishers as $publisher)
				{
                    // // add game to publisher in collection
                    $this->Collection->addMeta($collectionID, 'publisher', $publisher->GBID);
                }
            } 

            // get themes for game
            if($this->Game->getThemes($userID))
            {
                foreach($this->Game->themes as $theme)
				{
                    // // add game to theme in collection
                    $this->Collection->addMeta($collectionID, 'theme', $theme->GBID);
                }

            } 

            // get franchises for game
            if($this->Game->getFranchises($userID))
            {
                foreach($this->Game->franchises as $franchise)
				{
                    $this->Collection->addMeta($collectionID, 'franchise', $franchise->GBID);
                }
            } 
            // get genres for game
            if($this->Game->getGenres($userID))
            {
                foreach($this->Game->genres as $genre)
				{
                    $this->Collection->addMeta($collectionID, 'genre', $genre->GBID);
                }
            }   
                        // get locations for game
            if($this->Game->getLocations($userID))
            {
                foreach($this->Game->locations as $location)
				{
                    $this->Collection->addMeta($collectionID, 'location', $location->GBID);
                }
            }   
                        // get characters for game
            if($this->Game->getCharacters($userID))
            {
                foreach($this->Game->characters as $character)
				{
                    $this->Collection->addMeta($collectionID, 'character', $character->GBID);
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
	}

    // change played motivation of game
    function changeMotivation()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('motivationID', 'motivationID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $motivationID = $this->input->post('motivationID');
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
            // update played motivation
            $this->Collection->updateMotivation($GBID, $userID, $motivationID);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, $motivationID, null);

        // get motivation name and style
        $motivationData = $this->Collection->getMotivationDetails($motivationID);
        $result['motivationName'] = $motivationData->MotivationName;
        $result['motivationStyle'] = $motivationData->MotivationStyle;

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

    // change played Future of game
    function changeFuture()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('futureID', 'futureID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $futureID = $this->input->post('futureID');
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
            // update played future
            $this->Collection->updateFuture($GBID, $userID, $futureID);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, $futureID, null);

        // get future name and style
        $futureData = $this->Collection->getFutureDetails($futureID);
        $result['futureName'] = $futureData->FutureName;
        $result['futureStyle'] = $futureData->FutureStyle;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // change played Value of game
    function changeValue()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('valueID', 'valueID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $valueID = $this->input->post('valueID');
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
            // update played value
            $this->Collection->updateValue($GBID, $userID, $valueID);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, $valueID, null);

        // get value name and style
        $valueData = $this->Collection->getValueDetails($valueID);
        $result['valueName'] = $valueData->ValueName;
        $result['valueStyle'] = $valueData->ValueStyle;

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
// ----------   Meta  ----------------------------------------------------------------------------------------------------------
    function addMeta($meta)
    {
        $metaID = ucfirst($meta)."ID";
        $GBMetaID = "$GB".ucfirst($meta)."ID";
        $isMetaInDB = "is".ucfirst($meta)."InDB";
        $getMeta = "get".ucfirst($meta);
        $addMeta = "add".ucfirst($meta);
        
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules($metaID, $metaID, 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPlatformID = $this->input->post($metaID);
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
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kidos." + $collection, false, false);
            return;
        }
        
        // if game has meta, add it
        if(!$this->Collection->isGameOnMetaInCollection($collection->ID, $metaID, $GBMetaID))
        {
            // load meta model
            $this->load->model(ucfirst($meta));

            // if meta isnt in db
            if(!$this->ucfirst($meta)->$isMetaInDB($GBMetaID))
            {
                // get meta data 
                $meta = $this->ucfirst($meta)->$getMeta($GBMetaID);

                // if API returned nothing
                if($meta == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add meta to db
                $this->ucfirst($meta)->$addMeta($meta);
            }

            // add game to meta in collection
            $this->Collection->addMeta($collection->ID, $meta, $GBMetaID);


        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeMeta($meta)
    {
        
        $metaID = ucfirst($meta)."ID";
        
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules($metaID, $metaID, 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBMetaID = $this->input->post($metaID);
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
        $this->Collection->removeMeta($collection->ID, $meta, $GBMetaID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
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
        if(!$this->Collection->isGameOnMetaInCollection($collection->ID, 'platform', $GBPlatformID))
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
            $this->Collection->addMeta($collection->ID, 'platform', $GBPlatformID);


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
        $this->Collection->removeMeta($collection->ID, 'platform', $GBPlatformID);
        
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
        $this->form_validation->set_rules('genreID', 'genreID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBGenreID = $this->input->post('genreID');
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
            if(!$this->Genre->isGenreInDB($GBID))
            {
                // get genre data 
                $genre = $this->Genre->getGenres($GBID);

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
        $this->form_validation->set_rules('genreID', 'genreID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBGenreID = $this->input->post('genreID');
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
        $this->form_validation->set_rules('developerID', 'developerID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBDeveloperID = $this->input->post('developerID');
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
            if(!$this->Developer->isDeveloperInDB($GBID))
            {
                // get developer data 
                $developer = $this->Developer->getDevelopers($GBID);

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
        $this->form_validation->set_rules('developerID', 'developerID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBDeveloperID = $this->input->post('developerID');
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
        $this->form_validation->set_rules('publisherID', 'publisherID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPublisherID = $this->input->post('publisherID');
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
            if(!$this->Publisher->isPublisherInDB($GBID))
            {
                // get publisher data 
                $publisher = $this->Publisher->getPublishers($GBID);

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
        $this->form_validation->set_rules('publisherID', 'publisherID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPublisherID = $this->input->post('publisherID');
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
        $this->form_validation->set_rules('themeID', 'themeID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBThemeID = $this->input->post('themeID');
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
            if(!$this->Theme->isThemeInDB($GBID))
            {
                // get theme data 
                $theme = $this->Theme->getThemes($GBID);

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
        $this->form_validation->set_rules('themeID', 'themeID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBThemeID = $this->input->post('themeID');
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
        $this->form_validation->set_rules('franchiseID', 'franchiseID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBFranchiseID = $this->input->post('franchiseID');
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
            if(!$this->Franchise->isFranchiseInDB($GBID))
            {
                // get franchise data 
                $franchise = $this->Franchise->getFranchises($GBID);

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
        $this->form_validation->set_rules('franchiseID', 'franchiseID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBFranchiseID = $this->input->post('franchiseID');
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

// ----------   Concepts  ----------------------------------------------------------------------------------------------------------
    function addConcept()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('conceptID', 'conceptID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBConceptID = $this->input->post('conceptID');
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

       // $this->returnError("Collection:" + $collection, false, false);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on concept, add it
        if(!$this->Collection->isGameOnMetaInCollection($collection->ID, 'concept', $GBConceptID))
        {
            // load concept model
            $this->load->model('Concept');

            // if concept isnt in db
            if(!$this->Concept->isConceptInDB($GBID))
            {
                // get concept data 
                $concept = $this->Concept->getConcepts($GBID);

                // if API returned nothing
                if($concept == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add concept to db
                $this->Concept->addConcept($concept);
            }

            // add game to concept in collection
            $this->Collection->addMeta($collection->ID, 'concept', $GBConceptID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeConcept()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('conceptID', 'conceptID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBConceptID = $this->input->post('conceptID');
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
        
        // remove concept from game in collection
        $this->Collection->removeMeta($collection->ID, 'concept', $GBConceptID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

// ----------   Locations  ----------------------------------------------------------------------------------------------------------
    function addLocation()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('locationID', 'locationID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBLocationID = $this->input->post('locationID');
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
        
        // if game is not on location, add it
        if(!$this->Collection->isGameOnLocationInCollection($collection->ID, $GBLocationID))
        {
            // load location model
            $this->load->model('Location');

            // if location isnt in db
            if(!$this->Location->isLocationInDB($GBID))
            {
                // get location data 
                $location = $this->Location->getLocations($GBID);

                // if API returned nothing
                if($location == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add location to db
                $this->Location->addLocation($location);
            }

            // add game to location in collection
            $this->Collection->addLocation($collection->ID, $GBLocationID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeLocation()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('locationID', 'locationID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBLocationID = $this->input->post('locationID');
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
        
        // remove location from game in collection
        $this->Collection->removeLocation($collection->ID, $GBLocationID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

// ----------   Characters  ----------------------------------------------------------------------------------------------------------
    function addCharacter()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('characterID', 'characterID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBCharacterID = $this->input->post('characterID');
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
        
        // if game is not on character, add it
        if(!$this->Collection->isGameOnCharacterInCollection($collection->ID, $GBCharacterID))
        {
            // load character model
            $this->load->model('Character');

            // if character isnt in db
            if(!$this->Character->isCharacterInDB($GBID))
            {
                // get character data 
                $character = $this->Character->getCharacters($GBID, $userID);

                // if API returned nothing
                if($character == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add character to db
                $this->Character->addCharacter($character);
            }

            // add game to character in collection
            $this->Collection->addCharacter($collection->ID, $GBCharacterID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removeCharacter()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('characterID', 'characterID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBCharacterID = $this->input->post('characterID');
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
        
        // remove character from game in collection
        $this->Collection->removeCharacter($collection->ID, $GBCharacterID);
        
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