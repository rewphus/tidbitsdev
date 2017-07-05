<?php 

class Character extends CI_Model {
    
    // character data
    var $characterID;
    var $GBID;
    var $GBLink;
    var $name;
    var $image;
    var $imageSmall;
    var $deck;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is character in database?
    function isCharacterInDB($GBID)
    {
        $query = $this->db->get_where('characters', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get CharacterID from GBID
    function getCharacterByGBID($GBID)
    {
        $query = $this->db->get_where('characters', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns character if in db, or adds and returns it if it isn't
    function getOrAddCharacter($gbCharacter)
    {
        // get character from db
        $character = $this->getCharacterByGBID($gbCharacter->id);

        // if character isn't in db
        if($character == null)
        {
        
            // character was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getCharacter($gbCharacter->id);

            // if character was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add character to database
                $this->addCharacter($result->results);

            } else {
                // character was not found
                return false;
            }

            // get character from db
            $character = $this->getCharacterByGBID($gbCharacter->id);
        }

        return $character;
    }

    // add character to database
    function addCharacter($character)
    {
        $data = array(
           'GBID' => $character->id,
           'Name' => $character->name,
           'API_Detail' => $character->api_detail_url,
            'GBLink' => $character->site_detail_url,
           'Image' => is_object($character->image) ? $character->image->small_url : null,
           'ImageSmall' => is_object($character->image) ? $character->image->icon_url : null,
           'Deck' => $character->deck
        );

        return $this->db->insert('characters', $data); 
    }

    // get character by GBID
    public function getCharacters($GBID, $userID)
    {
                              echo '<script>
      var x;
      x = "GBID: ' . $GBID . '"
      console.log(x)</script>';

        // check if character is in database
        if ($this->isCharacterInDB($GBID)) {
            // get character from database
            if ($this->getCharacterFromDatabase($GBID, $userID)) {
                // character found
                return true;
            }
        }

        // character was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getCharacter($GBID);

        // if character was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add character to database
            $this->addCharacter($result->results);

            // get character from database
            return $this->getCharacterFromDatabase($GBID, $userID);
        } else {
            // character was not found
            return false;
        }
    }

    // get character from database
    public function getCharacterFromDatabase($GBID, $userID)
    {
        // get character from db
        $this->db->select('characters.CharacterID, characters.GBID, characters.GBLink, characters.Name, characters.Image, characters.ImageSmall, characters.Deck');
        $this->db->from('characters');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.CharacterID = characters.CharacterID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('characters.GBID', $GBID);
        $query = $this->db->get();

        // if character returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->characterID = $result->CharacterID;
            $this->GBID = $result->GBID;
            $this->GBLink = $result->GBLink;
            $this->name = $result->Name;
            $this->image = $result->Image;
            $this->imageSmall = $result->ImageSmall;
            $this->deck = $result->Deck;

            // // if character is in collection
            // if ($result->ListID != null) {
            //     // list button
            //     $this->listID = $result->ListID;
            //     $this->listLabel = $result->ListName;
            //     $this->listStyle = $result->ListStyle;

            //     // motivation button
            //     $this->motivationID = $result->MotivationID;
            //     $this->motivationLabel = $result->MotivationName;
            //     $this->motivationStyle = $result->MotivationStyle;

            //     // status button
            //     $this->statusID = $result->StatusID;
            //     $this->statusLabel = $result->StatusName;
            //     $this->statusStyle = $result->StatusStyle;

            //                     // future button
            //     $this->futureID = $result->FutureID;
            //     $this->futureLabel = $result->FutureName;
            //     $this->futureStyle = $result->FutureStyle;

            //                     // value button
            //     $this->valueID = $result->ValueID;
            //     $this->valueLabel = $result->ValueName;
            //     $this->valueStyle = $result->ValueStyle;

            //     // played data
            //     $this->currentlyPlaying = ($result->CurrentlyPlaying == 1) ? true : false;
            //     $this->dateComplete = $result->DateComplete;
            //     $this->hoursPlayed = $result->HoursPlayed;
            // }

            return true;
        }
        
        return false;
    }
}