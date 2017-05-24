<?php 

class Character extends CI_Model {

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
            // add character to db
            $this->Character->addCharacter($gbCharacter);

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
           'API_Detail' => $character->api_detail_url
        );

        return $this->db->insert('characters', $data); 
    }

    // get character data from Giant Bomb API
    function getCharacter($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/character/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Character");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}