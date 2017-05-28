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

            // status button
            $game->statusID = $collection->StatusID;
            $game->statusLabel = $collection->StatusName;
            $game->statusStyle = $collection->StatusStyle;

            // data
            $game->currentlyPlaying = ($collection->CurrentlyPlaying == 1) ? true : false;
            $game->dateComplete = $collection->DateComplete;
            $game->hoursPlayed = $collection->HoursPlayed;

            // get platforms user has game in collection
            $platforms = $this->getGamesMetaInCollection($game->id, $userID, 'platform');

            // get concepts user has game in collection
            $concepts = $this->getGamesMetaInCollection($game->id, $userID, 'concept');

        // not in collection
        } else {
            // list button
            $game->listID = 0;
            $game->listLabel = "Add to Collection";
            $game->listStyle = "default";

            // status button
            $game->statusID = 0;
            $game->statusLabel = "Set Status";
            $game->statusStyle = "default";

            // data
            $game->currentlyPlaying = false;
            $game->dateComplete = null;
            $game->hoursPlayed = null;

            // get platforms user has game in collection
            $platforms = null;
            // get concepts user has game in collection
            $concepts = null;
        }

        // add platforms user has game on in collection (if any)
        // if game has platforms
        if(property_exists($game, "platforms") && $game->platforms != null)
        {
            // loop over platforms game is on
            foreach($game->platforms as $gbPlatform)
            {
                $gbPlatform->inCollection = false;
                if($platforms != null)
                {
                    // loop over platforms user has in collection
                    foreach ($platforms as $platform)
                    {
                        // if platform is on game in collection
                        if($platform->GBID == $gbPlatform->id)
                        {
                            $gbPlatform->inCollection = true;
                            break;
                        }
                    }
                }
            }
        }

        // $this->doesMetaExist($game, 'platform', $userID);
         $this->doesMetaExist($game, 'concept', $userID);

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
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
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
        $statusID = 0;
        
        if ($listID == 1) {
            $statusID = 1;
        } elseif ($listID == 2) {
            $statusID = 6;
        } else {
            $statusID = 9;
        }
        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'ListID' => $listID,
           'StatusID' => $statusID // default to Set Status
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

        // get PlatformID from GBID
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

    // get users collection
    function getCollection($userID, $filters, $offset, $resultsPerPage)
    {
        // if offset is provided, get list of games
        if ($offset !== null) {
            $this->db->select('ImageSmall, GBID, Name, ListStyle, ListName, StatusStyle, StatusName');
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
            $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
            $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
            $this->db->join('collectionConcept', 'collections.ID = collectionConcept.CollectionID', 'left');
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

            // // filter out conceptID's
            // if(count($filters->concepts) > 0 && !$filters->includeNoConcepts)
            //     $this->db->where_not_in('collectionConcept.ConceptID', $filters->concepts);
            // // filter out conceptID's, but include games with no concept
            // else if(count($filters->concepts) > 0 && $filters->includeNoConcepts)
            //     $this->db->where("(`collectionConcept`.`ConceptID` NOT IN (" . implode(",", $filters->concepts) . ") OR `collectionConcept`.`ConceptID` IS NULL)");
            // // filter out games with no concept
            // else if(!$filters->includeNoConcepts)
            //     $this->db->where("(`collectionConcept`.`ConceptID` IS NOT NULL)");
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
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->join('collection'.ucfirst($meta), 'collections.ID = collection'.ucfirst($meta).'.CollectionID', 'left');
        $this->db->join($metas, 'collection'.ucfirst($meta).'.'.$metaID.' = '.$metas.'.'.$metaID, 'left');
        $this->db->join('games', 'collections.GameID = games.GameID');
        
        $this->db->where('collections.UserID', $userID);

        $this->db->group_by('collection'.ucfirst($meta).'.'.$metaID);
        $this->db->order_by("Percentage", "desc");

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
        $this->db->select('ID AS GWL_ID, games.GBID AS GB_ID, games.Name, ListName AS List, StatusName AS Status, DateComplete, HoursPlayed, CurrentlyPlaying, platforms.Name AS Platform, concepts.Name AS Concept, Abbreviation');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
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
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');
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

    // get users who have played game
    function getUsersWhoHavePlayedGame($GBID, $userID)
    {
        $this->db->select('users.UserID');
        $this->db->select('UserName');
        $this->db->select('ProfileImage');
        $this->db->select('StatusNameShort');
        $this->db->select('StatusStyle');

        $this->db->from('games');
        $this->db->join('collections', 'games.GameID = collections.GameID');
        $this->db->join('users', 'collections.UserID = users.UserID');
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');

        if ($userID != null) {
            $this->db->join('following', 'following.ChildUserID = collections.UserID AND following.ParentUserID = ' . $userID, 'left');
            $this->db->where('users.UserID !=', $userID); // if logged in, exclude yourself
        }

        $this->db->where('games.GBID', $GBID);

        $this->db->order_by("Ranking", "asc");
        
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
