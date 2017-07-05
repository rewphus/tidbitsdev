<?php

class Collection extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // add collection status (ownership and played status) and platforms to game object
    function addCollectionInfo($game, $userID)
    {
        $collection = $this->isGameIsInCollection($game->id, $userID);

        // if in collection
        if ($collection != null) {
            // list button
            $game->listID = $collection->ListID;
            $game->listLabel = $collection->ListName;
            $game->listStyle = $collection->ListStyle;

            // motivation button
            $game->motivationID = $collection->MotivationID;
            $game->motivationLabel = $collection->MotivationName;
            $game->motivationStyle = $collection->MotivationStyle;

            // status button
            $game->statusID = $collection->StatusID;
            $game->statusLabel = $collection->StatusName;
            $game->statusStyle = $collection->StatusStyle;

            // future button
            $game->futureID = $collection->FutureID;
            $game->futureLabel = $collection->FutureName;
            $game->futureStyle = $collection->FutureStyle;

            // value button
            $game->valueID = $collection->ValueID;
            $game->valueLabel = $collection->ValueName;
            $game->valueStyle = $collection->ValueStyle;

            // data
            $game->currentlyPlaying = ($collection->CurrentlyPlaying == 1) ? true : false;
            $game->dateComplete = $collection->DateComplete;
            $game->hoursPlayed = $collection->HoursPlayed;

            // get meta user has game in collection
            $platforms = $this->getGamesMetaInCollection($game->id, $userID, 'platform');
            $concepts = $this->getGamesMetaInCollection($game->id, $userID, 'concept');
            $characters = $this->getGamesMetaInCollection($game->id, $userID, 'character');
            $developers = $this->getGamesMetaInCollection($game->id, $userID, 'developer');
            $franchises = $this->getGamesMetaInCollection($game->id, $userID, 'franchise');
            $genres = $this->getGamesMetaInCollection($game->id, $userID, 'genre');
            $locations = $this->getGamesMetaInCollection($game->id, $userID, 'location');
            $publishers = $this->getGamesMetaInCollection($game->id, $userID, 'publisher');
            $themes = $this->getGamesMetaInCollection($game->id, $userID, 'theme');

        // not in collection
        } else {
            // list button
            $game->listID = 0;
            $game->listLabel = "Add to Collection";
            $game->listStyle = "default";

            // motivation button
            $game->motivationID = 0;
            $game->motivationLabel = "Set Motivation";
            $game->motivationStyle = "default";

            // status button
            $game->statusID = 0;
            $game->statusLabel = "Set Status";
            $game->statusStyle = "default";

            // future button
            $game->futureID = 0;
            $game->futureLabel = "Chance of Return...";
            $game->futureStyle = "default";

            // value button
            $game->valueID = 0;
            $game->valueLabel = "Current Value";
            $game->valueStyle = "default";

            // data
            $game->currentlyPlaying = false;
            $game->dateComplete = null;
            $game->hoursPlayed = null;

            // get meta user has game in collection
            $platforms = null;
            $concepts = null;
            $characters = null;
            $developers = null;
            $franchises = null;
            $genres = null;
            $locations = null;
            $publishers = null;
            $themes = null;
        }

        // add platforms user has game on in collection (if any)
        // if game has platforms
        if (property_exists($game, "platforms") && $game->platforms != null) {
            // loop over platforms game is on
            foreach ($game->platforms as $gbPlatform) {
                $gbPlatform->inCollection = false;
                if ($platforms != null) {
                    // loop over platforms user has in collection
                    foreach ($platforms as $platform) {
                        // if platform is on game in collection
                        if ($platform->GBID == $gbPlatform->id) {
                            $gbPlatform->inCollection = true;
                            break;
                        }
                    }
                }
            }
        }

        //I don't know why this is commented out?
        //$this->doesMetaExist($game, 'platform', $userID);
        $this->doesMetaExist($game, 'concept', $userID);
        $this->doesMetaExist($game, 'character', $userID);
        $this->doesMetaExist($game, 'developer', $userID);
        $this->doesMetaExist($game, 'franchise', $userID);
        $this->doesMetaExist($game, 'genre', $userID);
        $this->doesMetaExist($game, 'location', $userID);
        $this->doesMetaExist($game, 'theme', $userID);

        return $game;
    }

    
    function doesMetaExist($game, $meta, $userID)
    {
        $metaID = ucfirst($meta) . 'ID';
        $metas = $this->getGamesMetaInCollection($game->id, $userID, $meta);
        $metasString = $meta."s";
        
        // add meta user has identified
        // if game has meta
        if (property_exists($game, $metasString) && $game->$metasString != null) {
            // loop over meta for game
            foreach ($game->$metasString as $gbMeta) {
                $gbMeta->inCollection = false;
                if ($meta != null) {
                    // loop over meta user has in collection
                    foreach ($metas as $meta) {
                        // if meta is on game in collection
                        if ($meta->GBID == $gbMeta->id) {
                            $gbMeta->inCollection = true;
                            break;
                        }
                    }
                }
            }
        }
    }
    
    
    // is game in users collection?
    // returns collection record if in collection, null if not in collection
    function isGameIsInCollection($GBID, $userID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameMotivations', 'collections.MotivationID = gameMotivations.MotivationID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
        $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
        $this->db->where('games.GBID', $GBID);
        $this->db->where('collections.UserID', $userID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }

    // add game to users collection
    function addToCollection($gameID, $userID, $listID)
    {
        $motivationID = 99;
        $statusID = 0;
        $futureID = 0;
        $valueID = 99;
        
        if ($listID == 1) {
            $statusID = 99;
            $futureID = 99;
        } elseif ($listID == 2) {
            $statusID = 100;
            $futureID = 100;
        } else {
            $statusID = 101;
            $futureID = 101;
        }

        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'ListID' => $listID,
           'MotivationID' => $motivationID,
           'StatusID' => $statusID, // default to Set Status
             'FutureID' => $futureID, // default to Set Future
             'ValueID' => $valueID // default to Set Value
        );

        $this->db->insert('collections', $data);

        return $this->db->insert_id(); // return CollectionID
    }

    // update list game is on in collection
    function updateList($GBID, $userID, $listID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->update('collections', array('ListID' => $listID));
        }
    }

    // update played motivation of game in collection
    function updateMotivation($GBID, $userID, $motivationID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->update('collections', array('MotivationID' => $motivationID));
        }
    }
    // update played status of game in collection
    function updateStatus($GBID, $userID, $statusID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->update('collections', array('StatusID' => $statusID));
        }
    }

    // update played future of game in collection
    function updateFuture($GBID, $userID, $futureID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->update('collections', array('FutureID' => $futureID));
        }
    }

    // update played value of game in collection
    function updateValue($GBID, $userID, $valueID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->update('collections', array('ValueID' => $valueID));
        }
    }

    // remove game from users collection
    function removeFromCollection($GBID, $userID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->where('games.GBID', $GBID);
        $this->db->where('collections.UserID', $userID);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            // delete collection record
            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->delete('collections');

            // delete collectionPlatform record
            $this->db->where('CollectionID', $row->ID);
            $this->db->delete('collectionPlatform');
            $this->db->delete('collectionConcept');
            $this->db->delete('collectionCharacter');
            $this->db->delete('collectionDeveloper');
            $this->db->delete('collectionFranchise');
            $this->db->delete('collectionGenre');
            $this->db->delete('collectionLocation');
            $this->db->delete('collectionPublisher');
            $this->db->delete('collectionTheme');

            // delete userEvents records
            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->delete('userEvents');
        }
    }

    // add metadata to game in collection
    function addMeta($collectionID, $meta, $metaGBID)
    {
        $metaID = ucfirst($meta) . 'ID';

        // get metaID from GBID
        $query = $this->db->get_where($meta . 's', array('GBID' => $metaGBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $data = array(
               'CollectionID' => $collectionID,
               $metaID => $row->$metaID
            );

            return $this->db->insert('collection' . ucfirst($meta), $data);
        }
    }

    // remove metadata to game in collection
    function removeMeta($collectionID, $meta, $metaGBID)
    {

        $metaID = ucfirst($meta) . 'ID';

        // get MetaID from GBID
        $query = $this->db->get_where($meta . 's', array('GBID' => $metaGBID));
        if ($query->num_rows() == 1) {
            $row = $query->first_row();

            $this->db->where('CollectionID', $collectionID);
            $this->db->where($metaID, $row->$metaID);
            $this->db->delete('collection' . ucfirst($meta));
        }
    }

        // get metadata game is on in collection
    function getGamesMetaInCollection($GBID, $userID, $meta)
    {

        $metaID = ucfirst($meta) . 'ID';
        $metas = $meta . 's';
        if ($meta == 'platform') {
            $this->db->select('platforms.GBID, platforms.Name, platforms.Abbreviation');
            $this->db->from('collections');
            $this->db->join('games', 'collections.GameID = games.GameID');
            $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID');
            $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID');
            $this->db->where('games.GBID', $GBID);
            $this->db->where('collections.UserID', $userID);
            $query = $this->db->get();
        } else {
            $this->db->select($metas.'.GBID, '.$metas.'.Name');
            $this->db->from('collections');
            $this->db->join('games', 'collections.GameID = games.GameID');
            $this->db->join('collection'. ucfirst($meta), 'collections.ID = collection'. ucfirst($meta).'.CollectionID');
            $this->db->join($metas, 'collection'. ucfirst($meta).'.'.$metaID.' = '.$metas.'.'.$metaID);
            $this->db->where('games.GBID', $GBID);
            $this->db->where('collections.UserID', $userID);
            $query = $this->db->get();
        }
        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return null;
    }

    // check if game is on meta in collection
    function isGameOnMetaInCollection($collectionID, $meta, $metaGBID)
    {

        $metaID = ucfirst($meta) . 'ID';
        $metas = $meta . 's';

        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('collection'.ucfirst($meta), 'collections.ID = collection'.ucfirst($meta).'.CollectionID');
        $this->db->join($metas, 'collection'. ucfirst($meta).'.'.$metaID.' = '.$metas.'.'.$metaID);
        $this->db->where('collections.ID', $collectionID);
        $this->db->where($metas.'.GBID', $metaGBID);
        $query = $this->db->get();

        return $query->num_rows() > 0 ? true : false;
    }

    // update currently playing, hours played and date completed for game
    function updateProgression($collectionID, $currentlyPlaying, $hoursPlayed, $dateCompleted)
    {
        if ($hoursPlayed == '') {
            $hoursPlayed = null;
        }
        if ($dateCompleted == '') {
            $dateCompleted = null;
        }
        $currentlyPlayingBit = ($currentlyPlaying === "true");

        $this->db->where('ID', $collectionID);
        $this->db->update('collections', array('CurrentlyPlaying' => $currentlyPlayingBit, 'HoursPlayed' => $hoursPlayed, 'DateComplete' => $dateCompleted));
    }

    // get list
    function getListDetails($listID)
    {
        $this->db->select('*');
        $this->db->from('lists');
        $this->db->where('lists.ListID', $listID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }

    // get played motivation
    function getMotivationDetails($motivationID)
    {
        $this->db->select('*');
        $this->db->from('gameMotivations');
        $this->db->where('gameMotivations.MotivationID', $motivationID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }
    // get played status
    function getStatusDetails($statusID)
    {
        $this->db->select('*');
        $this->db->from('gameStatuses');
        $this->db->where('gameStatuses.StatusID', $statusID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }

    // get played future
    function getFutureDetails($futureID)
    {
        $this->db->select('*');
        $this->db->from('gameFutures');
        $this->db->where('gameFutures.FutureID', $futureID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }

    // get played value
    function getValueDetails($valueID)
    {
        $this->db->select('*');
        $this->db->from('gameValues');
        $this->db->where('gameValues.ValueID', $valueID);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->first_row();
        }

        return null;
    }

    // get users collection
    function getCollection($userID, $filters, $offset, $resultsPerPage)
    {
        // if offset is provided, get list of games
        if ($offset !== null) {
            $this->db->select('ImageSmall, GBID, Name, ListStyle, ListName, StatusStyle, StatusName, FutureStyle, FutureName, ValueStyle, ValueName');
        } // if no offset, count number of games in collection
        else {
            // collection: everything not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.ListID != 2 THEN collections.GameID END)) AS Collection');
            // completable collection: everything not uncompletable or on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END)) AS CompletableCollection');
            // completed: everything completed and not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END)) AS Completed');
            // backlog: everything unplayed or unfinished and not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN (collections.StatusID = 1 OR collections.StatusID = 2) AND collections.ListID != 2 THEN collections.GameID END)) AS Backlog');
            // want: everything on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.ListID = 2 THEN collections.GameID END)) AS Want');
        }

        $this->db->from('collections');

        if ($filters !== null) {
            $this->db->join('lists', 'collections.ListID = lists.ListID');
            $this->db->join('gameMotivations', 'collections.MotivationID = gameMotivations.MotivationID');
            $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
            $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
            $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
            $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
            // $this->db->join('collectionConcept', 'collections.ID = collectionConcept.CollectionID', 'left');
            $this->db->join('games', 'collections.GameID = games.GameID');
            $this->db->where('collections.UserID', $userID);
        
            // filter out listID's
            if (count($filters->lists) > 0) {
                $this->db->where_not_in('collections.ListID', $filters->lists);
            }

            // filter out statusID's
            if (count($filters->statuses) > 0) {
                $this->db->where_not_in('collections.StatusID', $filters->statuses);
            }

            // filter out futureID's
            // if (count($filters->futures) > 0) {
            //     $this->db->where_not_in('collections.FutureID', $filters->futures);
            // }

            // filter out valueID's
            // if (count($filters->values) > 0) {
            //     $this->db->where_not_in('collections.ValueID', $filters->values);
            // }
            
            // filter out platformID's
            if (count($filters->platforms) > 0 && !$filters->includeNoPlatforms) {
                $this->db->where_not_in('collectionPlatform.PlatformID', $filters->platforms);
            } // filter out platformID's, but include games with no platform
            elseif (count($filters->platforms) > 0 && $filters->includeNoPlatforms) {
                $this->db->where("(`collectionPlatform`.`PlatformID` NOT IN (" . implode(",", $filters->platforms) . ") OR `collectionPlatform`.`PlatformID` IS NULL)");
            } // filter out games with no platform
            elseif (!$filters->includeNoPlatforms) {
                $this->db->where("(`collectionPlatform`.`PlatformID` IS NOT NULL)");
            }
        }
        
        // only apply group by, order by and limit if getting list of games
        if ($offset !== null) {
            // group by game to remove deuplicates produced by platforms
            $this->db->group_by("collections.GameID");

            // order by
            switch ($filters->orderBy) {
                case "releaseDateAsc":
                    $this->db->order_by("games.ReleaseDate", "asc");
                    break;
                case "releaseDateDesc":
                    $this->db->order_by("games.ReleaseDate", "desc");
                    break;
                case "nameAsc":
                    $this->db->order_by("games.Name", "asc");
                    break;
                case "nameDesc":
                    $this->db->order_by("games.Name", "desc");
                    break;
                case "hoursPlayedAsc":
                    $this->db->order_by("collections.HoursPlayed", "asc");
                    break;
                case "hoursPlayedDesc":
                    $this->db->order_by("collections.HoursPlayed", "desc");
                    break;
            }
            
            // paging
            $this->db->limit($resultsPerPage, $offset);

            // get results
            $games = $this->db->get()->result();
            
            // add platforms to games
            foreach ($games as $game) {
                $game->Platforms = $this->getGamesMetaInCollection($game->GBID, $userID, 'platform');
            }

            // add concepts to games
            foreach ($games as $game) {
                $game->Concepts = $this->getGamesMetaInCollection($game->GBID, $userID, 'concept');
            }

            return $games;
        } else {
            // calculate collection stats
            $stats = $this->db->get()->first_row();
            if ($stats->Completed == null) {
                $stats->Completed = 0;
            }
            if ($stats->Backlog == null) {
                $stats->Backlog = 0;
            }
            if ($stats->Want == null) {
                $stats->Want = 0;
            }
            $stats->PercentComplete = $stats->CompletableCollection == 0 ? 0 : round((($stats->Completed/$stats->CompletableCollection) * 100), 0);
            return $stats;
        }
    }

    // get users collection by meta
    function getCollectionByMeta($userID, $meta)
    {
        $metas = $meta."s";
        $metaID = ucfirst($meta)."ID";

        $this->db->select('collection'.ucfirst($meta).".".$metaID);
        //order by timestamp
        $this->db->select_max('collection'.ucfirst($meta).".lastUpdated","ts");
        $this->db->select($metas.'.Name,');
        $this->db->select($metas.'.Image,');
        // collection: everything not on the want list
        $this->db->select('COUNT(CASE WHEN collections.ListID != 2 THEN collections.GameID END) AS Collection');
        // completable collection: everything not uncompletable or on the want list
        $this->db->select('COUNT(CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END) AS CompletableCollection');
        // completed: everything completed and not on the want list
        $this->db->select('COUNT(CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END) AS Completed');
        // backlog: everything unplayed or unfinished and not on the want list
        $this->db->select('COUNT(CASE WHEN (collections.StatusID = 1 OR collections.StatusID = 2) AND collections.ListID != 2 THEN collections.GameID END) AS Backlog');
        // want: everything on the want list
        $this->db->select('COUNT(CASE WHEN collections.ListID = 2 THEN collections.GameID END) AS Want');
        // percentage complete: (completed / completable collection) * 100
        $this->db->select('CAST((COUNT(CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END) / COUNT(CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END)) * 100 AS UNSIGNED) AS Percentage');
    
        $this->db->from('collections');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameMotivations', 'collections.MotivationID = gameMotivations.MotivationID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
        $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
        $this->db->join('collection'.ucfirst($meta), 'collections.ID = collection'.ucfirst($meta).'.CollectionID', 'left');
        $this->db->join($metas, 'collection'.ucfirst($meta).'.'.$metaID.' = '.$metas.'.'.$metaID, 'left');
        $this->db->join('games', 'collections.GameID = games.GameID');
        
        $this->db->where('collections.UserID', $userID);

        $this->db->group_by('collection'.ucfirst($meta).'.'.$metaID);
        //order by timestamp
        $this->db->order_by('ts', 'desc');

        // get results
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $metas = $query->result();

            foreach ($metas as $meta1) {
                // default profile image
                $meta1->Name = $meta1->Name == null ? "No ".ucfirst($meta) : $meta1->Name;
                $meta1->Image = $meta1->Image == null ? $this->config->item('default_profile_image') : $meta1->Image;
            }

            return $metas;
        }

        return null;
    }

    // get raw collection data to export
    function getRawCollection($userID)
    {
        $this->db->select('ID AS GWL_ID, games.GBID AS GB_ID, games.Name, ListName AS List, MotivationName AS Motivation, StatusName AS Status, FutureName AS Future, ValueName AS Value, DateComplete, HoursPlayed, CurrentlyPlaying, platforms.Name AS Platform, concepts.Name AS Concept, Abbreviation');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameMotivations', 'collections.MotivationID = gameMotivations.Motivationyou sID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
        $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
        $this->db->join('collectionConcept', 'collections.ID = collectionConcept.CollectionID', 'left');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID', 'left');
        $this->db->join('concepts', 'collectionConcept.ConceptID = concepts.ConceptID', 'left');
        $this->db->where('collections.UserID', $userID);
        $this->db->order_by("games.Name", "asc");
            
        // get results
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query;
        }

        return null;
    }

    // get users collection
    function getCurrentlyPlaying($userID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('gameMotivations', 'gameMotivations.MotivationID = collections.MotivationID');
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');
        $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
        $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
        $this->db->where('collections.UserID', $userID);
        $this->db->where('collections.CurrentlyPlaying', 1);
        $this->db->order_by("games.Name", "asc");

        // get results
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return null;
    }

    // get platforms in collection
    function getPlatformsInCollection($userID)
    {
        $this->db->select('platforms.PlatformID, platforms.Abbreviation, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID', 'left');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("platforms.PlatformID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

        // get concepts in collection
    function getMetaInCollection($userID, $meta)
    {
        $metas = $meta."s";
        $metaID = ucfirst($meta)."ID";
        
        $this->db->select($metas.'.'.$metaID.', count(*) as Games');
        $this->db->from('collections');
        $this->db->join('collection'.ucfirst($meta), 'collections.ID = collection'.ucfirst($meta).'.CollectionID', 'left');
        $this->db->join('concepts', 'collection'.ucfirst($meta).'.'.$metaID.' = '.$metas.'.'.$metaID, 'left');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("concepts.ConceptID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get lists in collection
    function getListsInCollection($userID)
    {
        $this->db->select('lists.ListID, lists.ListName, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("lists.ListID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get motivations in collection
    function getMotivationsInCollection($userID)
    {
        $this->db->select('gameMotivations.MotivationID, gameMotivations.MotivationName, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('gameMotivations', 'collections.MotivationID = gameMotivations.MotivationID');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("gameMotivations.MotivationID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get statuses in collection
    function getStatusesInCollection($userID)
    {
        $this->db->select('gameStatuses.StatusID, gameStatuses.StatusName, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("gameStatuses.StatusID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get futures in collection
    function getFuturesInCollection($userID)
    {
        $this->db->select('gameFutures.FutureID, gameFutures.FutureName, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('gameFutures', 'collections.FutureID = gameFutures.FutureID');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("gameFutures.FutureID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get values in collection
    function getValuesInCollection($userID)
    {
        $this->db->select('gameValues.ValueID, gameValues.ValueName, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('gameValues', 'collections.ValueID = gameValues.ValueID');
        $this->db->where('collections.UserID', $userID);
        $this->db->group_by("gameValues.ValueID");
        $this->db->order_by("Games", "desc");
        $query = $this->db->get();

        return $query->result();
    }

    // get users who have played game
    function getUsersWhoHavePlayedGame($GBID, $userID)
    {
        $this->db->select('users.UserID');
        $this->db->select('UserName');
        $this->db->select('ProfileImage');
        $this->db->select('MotivationNameShort');
        $this->db->select('MotivationStyle');
        $this->db->select('StatusNameShort');
        $this->db->select('StatusStyle');
        $this->db->select('FutureNameShort');
        $this->db->select('FutureStyle');
        $this->db->select('ValueNameShort');
        $this->db->select('ValueStyle');

        $this->db->from('games');
        $this->db->join('collections', 'games.GameID = collections.GameID');
        $this->db->join('users', 'collections.UserID = users.UserID');
        $this->db->join('gameMotivations', 'gameMotivations.MotivationID = collections.MotivationID');        
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');
        $this->db->join('gameFutures', 'gameFutures.FutureID = collections.FutureID');
        $this->db->join('gameValues', 'gameValues.ValueID = collections.ValueID');

        if ($userID != null) {
            $this->db->join('following', 'following.ChildUserID = collections.UserID AND following.ParentUserID = ' . $userID, 'left');
            $this->db->where('users.UserID !=', $userID); // if logged in, exclude yourself
        }

        $this->db->where('games.GBID', $GBID);

        //Error showing that order clause is ambigious
        // $this->db->order_by("Ranking", "asc");
        
        // if logged in, bump users you follow up the list
        if ($userID != null) {
            $this->db->order_by('following.ParentUserID', 'desc');
        }

        // get results
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $users = $query->result();

            foreach ($users as $user) {
                // default profile image
                $user->ProfileImage = $user->ProfileImage == null ? $this->config->item('default_profile_image') : $user->ProfileImage;
            }

            return $users;
        }

        return null;
    }
}
