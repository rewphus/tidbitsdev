<?php

class Game extends CI_Model
{

    // game data
    var $gameID;
    var $GBID;
    var $GBLink;
    var $name;
    var $image;
    var $imageSmall;
    var $deck;
    var $platforms;
    var $genres;
    var $developers;
    var $publishers;
    var $themes;
    var $franchises;
    var $concepts;
    var $locations;
    var $characters;


    // list button
    var $listID = 0;
    var $listLabel = "Add to Collection";
    var $listStyle = "default";

    // status button
    var $statusID = 0;
    var $statusLabel = "Set Status";
    var $statusStyle = "default";

    // played data
    var $currentlyPlaying = false;
    var $dateComplete;
    var $hoursPlayed;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // get game by GBID
    public function getGame($GBID, $userID)
    {
        // check if game is in database
        if ($this->isGameInDB($GBID)) {
            // get game from database
            if ($this->getGameFromDatabase($GBID, $userID)) {
                // game found
                return true;
            }
        }

        // game was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getGame($GBID);

        // if game was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add game to database
            $this->addGame($result->results);

            // get game from database
            return $this->getGameFromDatabase($GBID, $userID);
        } else {
            // game was not found
            return false;
        }
    }

    // is game in database?
    function isGameInDB($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get game from database
    public function getGameFromDatabase($GBID, $userID)
    {
        // get game from db
        $this->db->select('games.GameID, games.GBID, games.GBLink, games.Name, games.Image, games.ImageSmall, games.Deck, lists.ListID, lists.ListName, lists.ListStyle');
        $this->db->select('gameStatuses.StatusID, gameStatuses.StatusName, gameStatuses.StatusStyle, collections.CurrentlyPlaying, collections.DateComplete, collections.HoursPlayed');
        $this->db->from('games');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        $this->db->join('collections', 'collections.GameID = games.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID', 'left');

        $this->db->where('games.GBID', $GBID);
        $query = $this->db->get();

        // if game returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->gameID = $result->GameID;
            $this->GBID = $result->GBID;
            $this->GBLink = $result->GBLink;
            $this->name = $result->Name;
            $this->image = $result->Image;
            $this->imageSmall = $result->ImageSmall;
            $this->deck = $result->Deck;

            // if game is in collection
            if ($result->ListID != null) {
                // list button
                $this->listID = $result->ListID;
                $this->listLabel = $result->ListName;
                $this->listStyle = $result->ListStyle;

                // status button
                $this->statusID = $result->StatusID;
                $this->statusLabel = $result->StatusName;
                $this->statusStyle = $result->StatusStyle;

                // played data
                $this->currentlyPlaying = ($result->CurrentlyPlaying == 1) ? true : false;
                $this->dateComplete = $result->DateComplete;
                $this->hoursPlayed = $result->HoursPlayed;
            }

            $this->getPlatforms($userID);
            $this->getGenres($userID);
            $this->getDevelopers($userID);
            $this->getPublishers($userID);
            $this->getThemes($userID);
            $this->getFranchises($userID);
            $this->getConcepts($userID);
            $this->getLocations($userID);
            $this->getCharacters($userID);

            return true;
        }
        
        return false;
    }

/*----------------------    // get platforms for game  -------------------------------------------------------------------------*/
    function getPlatforms($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('platforms.platformID, platforms.GBID, platforms.name, platforms.abbreviation');
        $this->db->select('(CASE WHEN collectionPlatform.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gamePlatforms', 'games.GameID = gamePlatforms.GameID');
        $this->db->join('platforms', 'gamePlatforms.PlatformID = platforms.PlatformID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID AND collectionPlatform.PlatformID = platforms.PlatformID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->platforms = $query->result();

            return true;
        }

        return false;
    }

/*----------------------    // get genres for game  -------------------------------------------------------------------------*/
    function getGenres($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('genres.genreID, genres.GBID, genres.name');
        $this->db->select('(CASE WHEN collectionGenre.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameGenres', 'games.GameID = gameGenres.GameID');
        $this->db->join('genres', 'gameGenres.GenreID = genres.GenreID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionGenre', 'collections.ID = collectionGenre.CollectionID AND collectionGenre.GenreID = genres.GenreID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->genres = $query->result();

            return true;
        }

        return false;
    }

/*----------------------    // get developers for game  -------------------------------------------------------------------------*/
    function getDevelopers($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('developers.developerID, developers.GBID, developers.name');
        $this->db->select('(CASE WHEN collectionDeveloper.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameDevelopers', 'games.GameID = gameDevelopers.GameID');
        $this->db->join('developers', 'gameDevelopers.DeveloperID = developers.DeveloperID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionDeveloper', 'collections.ID = collectionDeveloper.CollectionID AND collectionDeveloper.DeveloperID = developers.DeveloperID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->developers = $query->result();

            return true;
        }

        return false;
    }

/*----------------------    // get publishers for game  -------------------------------------------------------------------------*/
    function getPublishers($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('publishers.publisherID, publishers.GBID, publishers.name');
        $this->db->select('(CASE WHEN collectionPublisher.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gamePublishers', 'games.GameID = gamePublishers.GameID');
        $this->db->join('publishers', 'gamePublishers.PublisherID = publishers.PublisherID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionPublisher', 'collections.ID = collectionPublisher.CollectionID AND collectionPublisher.PublisherID = publishers.PublisherID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->publishers = $query->result();

            return true;
        }

        return false;
    }

/*----------------------    // get themes for game  -------------------------------------------------------------------------*/
    function getThemes($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('themes.themeID, themes.GBID, themes.name');
        $this->db->select('(CASE WHEN collectionTheme.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameThemes', 'games.GameID = gameThemes.GameID');
        $this->db->join('themes', 'gameThemes.ThemeID = themes.ThemeID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionTheme', 'collections.ID = collectionTheme.CollectionID AND collectionTheme.ThemeID = themes.ThemeID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->themes = $query->result();

            return true;
        }

        return false;
    }

/*----------------------    // get franchises for game  -------------------------------------------------------------------------*/
    function getFranchises($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('franchises.franchiseID, franchises.GBID, franchises.name');
        $this->db->select('(CASE WHEN collectionFranchise.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameFranchises', 'games.GameID = gameFranchises.GameID');
        $this->db->join('franchises', 'gameFranchises.FranchiseID = franchises.FranchiseID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionFranchise', 'collections.ID = collectionFranchise.CollectionID AND collectionFranchise.FranchiseID = franchises.FranchiseID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->franchises = $query->result();

            return true;
        }

        return false;
    }

    /*----------------------    // get concepts for game  -------------------------------------------------------------------------*/
    function getConcepts($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('concepts.conceptID, concepts.GBID, concepts.name');
        $this->db->select('(CASE WHEN collectionConcept.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameConcepts', 'games.GameID = gameConcepts.GameID');
        $this->db->join('concepts', 'gameConcepts.ConceptID = concepts.ConceptID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionConcept', 'collections.ID = collectionConcept.CollectionID AND collectionConcept.ConceptID = concepts.ConceptID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->concepts = $query->result();

            return true;
        }

        return false;
    }

        /*----------------------    // get locations for game  -------------------------------------------------------------------------*/
    function getLocations($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('locations.locationID, locations.GBID, locations.name');
        $this->db->select('(CASE WHEN collectionLocation.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameLocations', 'games.GameID = gameLocations.GameID');
        $this->db->join('locations', 'gameLocations.LocationID = locations.LocationID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionLocation', 'collections.ID = collectionLocation.CollectionID AND collectionLocation.LocationID = locations.LocationID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->locations = $query->result();

            return true;
        }

        return false;
    }

            /*----------------------    // get characters for game  -------------------------------------------------------------------------*/
    function getCharacters($userID)
    {
        // error if no GameID
        if ($this->gameID == null) {
            return false;
        }

        // prevents joining on UserID causing an error
        if ($userID == null) {
            $userID = 0;
        }

        $this->db->select('characters.characterID, characters.GBID, characters.name');
        $this->db->select('(CASE WHEN collectionCharacter.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gameCharacters', 'games.GameID = gameCharacters.GameID');
        $this->db->join('characters', 'gameCharacters.CharacterID = characters.CharacterID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionCharacter', 'collections.ID = collectionCharacter.CollectionID AND collectionCharacter.CharacterID = characters.CharacterID', 'left');
        $this->db->where('games.GameID', $this->gameID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->characters = $query->result();

            return true;
        }

        return false;
    }

    // get GameID from GBID
    function getGameIDFromGiantBombID($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        if ($query->num_rows() == 1) {
            return $query->first_row()->GameID;
        } else {
            return null;
        }
    }

    // add game to database
    function addGame($game)
    {
        $this->load->model('GiantBomb');
        $releaseDate = $this->GiantBomb->convertReleaseDate($game);

        $data = array(
           'GBID' => $game->id,
           'GBLink' => $game->site_detail_url,
           'Name' => $game->name,
           'Image' => is_object($game->image) ? $game->image->small_url : null,
           'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
           'Deck' => $game->deck,
           'ReleaseDate' => $releaseDate,
           'LastUpdated' => date('Y-m-d')
        );

        if ($this->db->insert('games', $data)) {
            $this->gameID = $this->db->insert_id();
            
            $this->addPlatforms($game);
            $this->addGenres($game);
            $this->addDevelopers($game);
            $this->addPublishers($game);
            $this->addThemes($game);
            $this->addFranchises($game);
            $this->addConcepts($game);
            $this->addLocations($game);
            $this->addCharacters($game);

            return true;
        } else {
            return false;
        }
    }

    // update game cache
    function updateGame($game)
    {
        // get GameID
        $this->gameID = $this->getGameIDFromGiantBombID($game->id);

        // if game exists
        if ($this->gameID != null) {
            // get release date
            $this->load->model('GiantBomb');
            $releaseDate = $this->GiantBomb->convertReleaseDate($game);

            $data = array(
               'Name' => $game->name,
               'GBLink' => $game->site_detail_url,
               'Image' => is_object($game->image) ? $game->image->small_url : null,
               'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
               'Deck' => $game->deck,
               'ReleaseDate' => $releaseDate,
               'LastUpdated' => date('Y-m-d')
            );

            // update game data
            $this->db->where('GameID', $this->gameID);
            $this->db->update('games', $data);

            $this->addPlatforms($game);
            $this->addGenres($game);
            $this->addDevelopers($game);
            $this->addPublishers($game);
            $this->addThemes($game);
            $this->addFranchises($game);
            $this->addConcepts($game);
            $this->addLocations($game);
            $this->addCharacters($game);
        }
    }

/*---------------------------    // update platform game cache    ----------------------------------------------------------------*/
    function addPlatforms($game)
    {
        // add platforms to game
        if (property_exists($game, "platforms") && $game->platforms != null) {
            // load platforms model
            $this->load->model('Platform');

            // get platforms game already has
            $this->getPlatforms(null);

            // loop over platforms returned by GB
            $platformsToAdd = [];
            foreach ($game->platforms as $gbPlatform) {
                // loop over platforms for game already in db
                $gameHasPlatform = false;
                if ($this->platforms != null) {
                    foreach ($this->platforms as $platform) {
                        // if game has platform
                        if ($platform->GBID == $gbPlatform->id) {
                            $gameHasPlatform = true;
                            break;
                        }
                    }
                }

                // if game doesnt have platform
                if (!$gameHasPlatform) {
                    // get or add platform to db
                    $platform = $this->Platform->getOrAddPlatform($gbPlatform);

                    // add to list of platforms to add to game
                    array_push($platformsToAdd, array(
                      'GameID' => $this->gameID,
                      'PlatformID' => $platform->PlatformID
                    ));
                }
            }

            // if there are platforms to add to game
            if (count($platformsToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gamePlatforms', $platformsToAdd);
            }
        }
    }

/*---------------------------    // update genre game cache    ----------------------------------------------------------------*/
    function addGenres($game)
    {
        // add genres to game
        if (property_exists($game, "genres") && $game->genres != null) {
            // load genres model
            $this->load->model('Genre');

            // get genres game already has
            $this->getGenres(null);

            // loop over genres returned by GB
            $genresToAdd = [];
            foreach ($game->genres as $gbGenre) {
                // loop over genres for game already in db
                $gameHasGenre = false;
                if ($this->genres != null) {
                    foreach ($this->genres as $genre) {
                        // if game has genre
                        if ($genre->GBID == $gbGenre->id) {
                            $gameHasGenre = true;
                            break;
                        }
                    }
                }

                // if game doesnt have genre
                if (!$gameHasGenre) {
                    // get or add genre to db
                    $genre = $this->Genre->getOrAddGenre($gbGenre);

                    // add to list of genres to add to game
                    array_push($genresToAdd, array(
                      'GameID' => $this->gameID,
                      'GenreID' => $genre->GenreID
                    ));
                }
            }

            // if there are genres to add to game
            if (count($genresToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameGenres', $genresToAdd);
            }
        }
    }

/*---------------------------    // update developer game cache    ----------------------------------------------------------------*/
    function addDevelopers($game)
    {
        // add developers to game
        if (property_exists($game, "developers") && $game->developers != null) {
            // load developers model
            $this->load->model('Developer');

            // get developers game already has
            $this->getDevelopers(null);

            // loop over developers returned by GB
            $developersToAdd = [];
            foreach ($game->developers as $gbDeveloper) {
                // loop over developers for game already in db
                $gameHasDeveloper = false;
                if ($this->developers != null) {
                    foreach ($this->developers as $developer) {
                        // if game has developer
                        if ($developer->GBID == $gbDeveloper->id) {
                            $gameHasDeveloper = true;
                            break;
                        }
                    }
                }

                // if game doesnt have developer
                if (!$gameHasDeveloper) {
                    // get or add developer to db
                    $developer = $this->Developer->getOrAddDeveloper($gbDeveloper);

                    // add to list of developers to add to game
                    array_push($developersToAdd, array(
                      'GameID' => $this->gameID,
                      'DeveloperID' => $developer->DeveloperID
                    ));
                }
            }

            // if there are developers to add to game
            if (count($developersToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameDevelopers', $developersToAdd);
            }
        }
    }

/*---------------------------    // update publisher game cache    ----------------------------------------------------------------*/
    function addPublishers($game)
    {
        // add publishers to game
        if (property_exists($game, "publishers") && $game->publishers != null) {
            // load publishers model
            $this->load->model('Publisher');

            // get publishers game already has
            $this->getPublishers(null);

            // loop over publishers returned by GB
            $publishersToAdd = [];
            foreach ($game->publishers as $gbPublisher) {
                // loop over publishers for game already in db
                $gameHasPublisher = false;
                if ($this->publishers != null) {
                    foreach ($this->publishers as $publisher) {
                        // if game has publisher
                        if ($publisher->GBID == $gbPublisher->id) {
                            $gameHasPublisher = true;
                            break;
                        }
                    }
                }

                // if game doesnt have publisher
                if (!$gameHasPublisher) {
                    // get or add publisher to db
                    $publisher = $this->Publisher->getOrAddPublisher($gbPublisher);

                    // add to list of publishers to add to game
                    array_push($publishersToAdd, array(
                      'GameID' => $this->gameID,
                      'PublisherID' => $publisher->PublisherID
                    ));
                }
            }

            // if there are publishers to add to game
            if (count($publishersToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gamePublishers', $publishersToAdd);
            }
        }
    }

/*---------------------------    // update theme game cache    ----------------------------------------------------------------*/
    function addThemes($game)
    {
        // add themes to game
        if (property_exists($game, "themes") && $game->themes != null) {
            // load themes model
            $this->load->model('Theme');

            // get themes game already has
            $this->getThemes(null);

            // loop over themes returned by GB
            $themesToAdd = [];
            foreach ($game->themes as $gbTheme) {
                // loop over themes for game already in db
                $gameHasTheme = false;
                if ($this->themes != null) {
                    foreach ($this->themes as $theme) {
                        // if game has theme
                        if ($theme->GBID == $gbTheme->id) {
                            $gameHasTheme = true;
                            break;
                        }
                    }
                }

                // if game doesnt have theme
                if (!$gameHasTheme) {
                    // get or add theme to db
                    $theme = $this->Theme->getOrAddTheme($gbTheme);

                    // add to list of themes to add to game
                    array_push($themesToAdd, array(
                      'GameID' => $this->gameID,
                      'ThemeID' => $theme->ThemeID
                    ));
                }
            }

            // if there are themes to add to game
            if (count($themesToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameThemes', $themesToAdd);
            }
        }
    }

/*---------------------------    // update franchise game cache    ----------------------------------------------------------------*/
    function addFranchises($game)
    {
        // add franchises to game
        if (property_exists($game, "franchises") && $game->franchises != null) {
            // load franchises model
            $this->load->model('Franchise');

            // get franchises game already has
            $this->getFranchises(null);

            // loop over franchises returned by GB
            $franchisesToAdd = [];
            foreach ($game->franchises as $gbFranchise) {
                // loop over franchises for game already in db
                $gameHasFranchise = false;
                if ($this->franchises != null) {
                    foreach ($this->franchises as $franchise) {
                        // if game has franchise
                        if ($franchise->GBID == $gbFranchise->id) {
                            $gameHasFranchise = true;
                            break;
                        }
                    }
                }

                // if game doesnt have franchise
                if (!$gameHasFranchise) {
                    // get or add franchise to db
                    $franchise = $this->Franchise->getOrAddFranchise($gbFranchise);

                    // add to list of franchises to add to game
                    array_push($franchisesToAdd, array(
                      'GameID' => $this->gameID,
                      'FranchiseID' => $franchise->FranchiseID
                    ));
                }
            }

            // if there are franchises to add to game
            if (count($franchisesToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameFranchises', $franchisesToAdd);
            }
        }
    }


    /*---------------------------    // update concept game cache    ----------------------------------------------------------------*/
    function addConcepts($game)
    {
        // add concepts to game
        if (property_exists($game, "concepts") && $game->concepts != null) {
            // load concepts model
            $this->load->model('Concept');

            // get concepts game already has
            $this->getConcepts(null);

            // loop over concepts returned by GB
            $conceptsToAdd = [];
            foreach ($game->concepts as $gbConcept) {
                // loop over concepts for game already in db
                $gameHasConcept = false;
                if ($this->concepts != null) {
                    foreach ($this->concepts as $concept) {
                        // if game has concept
                        if ($concept->GBID == $gbConcept->id) {
                            $gameHasConcept = true;
                            break;
                        }
                    }
                }

                // if game doesnt have concept
                if (!$gameHasConcept) {
                    // get or add concept to db
                    $concept = $this->Concept->getOrAddConcept($gbConcept);

                    // add to list of concepts to add to game
                    array_push($conceptsToAdd, array(
                      'GameID' => $this->gameID,
                      'ConceptID' => $concept->ConceptID
                    ));
                }
            }

            // if there are concepts to add to game
            if (count($conceptsToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameConcepts', $conceptsToAdd);
            }
        }
    }

/*---------------------------    // update location game cache    ----------------------------------------------------------------*/
    function addLocations($game)
    {
        // add locations to game
        if (property_exists($game, "locations") && $game->locations != null) {
            // load locations model
            $this->load->model('Location');

            // get locations game already has
            $this->getLocations(null);

            // loop over locations returned by GB
            $locationsToAdd = [];
            foreach ($game->locations as $gbLocation) {
                // loop over locations for game already in db
                $gameHasLocation = false;
                if ($this->locations != null) {
                    foreach ($this->locations as $location) {
                        // if game has location
                        if ($location->GBID == $gbLocation->id) {
                            $gameHasLocation = true;
                            break;
                        }
                    }
                }

                // if game doesnt have location
                if (!$gameHasLocation) {
                    // get or add location to db
                    $location = $this->Location->getOrAddLocation($gbLocation);

                    // add to list of locations to add to game
                    array_push($locationsToAdd, array(
                      'GameID' => $this->gameID,
                      'LocationID' => $location->LocationID
                    ));
                }
            }

            // if there are locations to add to game
            if (count($locationsToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameLocations', $locationsToAdd);
            }
        }
    }


/*---------------------------    // update character game cache    ----------------------------------------------------------------*/
    function addCharacters($game)
    {
        // add characters to game
        if (property_exists($game, "characters") && $game->characters != null) {
            // load characters model
            $this->load->model('Character');

            // get characters game already has
            $this->getCharacters(null);

            // loop over characters returned by GB
            $charactersToAdd = [];
            foreach ($game->characters as $gbCharacter) {
                // loop over characters for game already in db
                $gameHasCharacter = false;
                if ($this->characters != null) {
                    foreach ($this->characters as $character) {
                        // if game has character
                        if ($character->GBID == $gbCharacter->id) {
                            $gameHasCharacter = true;
                            break;
                        }
                    }
                }

                // if game doesnt have character
                if (!$gameHasCharacter) {
                    // get or add character to db
                    $character = $this->Character->getOrAddCharacter($gbCharacter);

                    // add to list of characters to add to game
                    array_push($charactersToAdd, array(
                      'GameID' => $this->gameID,
                      'CharacterID' => $character->CharacterID
                    ));
                }
            }

            // if there are characters to add to game
            if (count($charactersToAdd) > 0) {
                // add to game in db
                $this->db->insert_batch('gameCharacters', $charactersToAdd);
            }
        }
    }

    // destroy game
    function destroy()
    {
        // game data
        $this->gameID = null;
        $this->GBID = null;
        $this->GBLink = null;
        $this->name = null;
        $this->image = null;
        $this->imageSmall = null;
        $this->deck = null;
        $this->platforms = null;
        $this->genres = null;
        $this->developers = null;
        $this->publishers = null;
        $this->themes = null;
        $this->franchises = null;
        $this->concepts = null;
        $this->locations = null;
        $this->characters = null;


        // list button
        $this->listID = 0;
        $this->listLabel = "Add to Collection";
        $this->listStyle = "default";

        // status button
        $this->statusID = 0;
        $this->statusLabel = "Set Status";
        $this->statusStyle = "default";

        // played data
        $this->currentlyPlaying = false;
        $this->dateComplete = null;
        $this->hoursPlayed = null;
    }
}
