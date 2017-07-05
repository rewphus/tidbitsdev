<?php 

class Developer extends CI_Model {

    // developer data
    var $developerID;
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

    // is developer in database?
    function isDeveloperInDB($GBID)
    {
        $query = $this->db->get_where('developers', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get DeveloperID from GBID
    function getDeveloperByGBID($GBID)
    {
        $query = $this->db->get_where('developers', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns developer if in db, or adds and returns it if it isn't
    function getOrAddDeveloper($gbDeveloper)
    {
        // get developer from db
        $developer = $this->getDeveloperByGBID($gbDeveloper->id);

        // if developer isn't in db
        if($developer == null)
        {
            // developer was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbDeveloper->id, "company");

            // if developer was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add developer to database
                $this->addDeveloper($result->results);

            } else {
                // developer was not found
                return false;
            }

            // get developer from db
            $developer = $this->getDeveloperByGBID($gbDeveloper->id);
        }

        return $developer;
    }

    // add developer to database
    function addDeveloper($developer)
    {
        $data = array(
           'GBID' => $developer->id,
           'Name' => $developer->name,
           'API_Detail' => $developer->api_detail_url,
            'GBLink' => $developer->site_detail_url,
           'Image' => is_object($developer->image) ? $developer->image->small_url : null,
           'ImageSmall' => is_object($developer->image) ? $developer->image->icon_url : null,
           'Deck' => $developer->deck
        );

        return $this->db->insert('developers', $data); 
    }

    // get developer by GBID
    public function getDevelopers($GBID, $userID)
    {
        // check if developer is in database
        if ($this->isDeveloperInDB($GBID)) {
            // get developer from database
            if ($this->getDeveloperFromDatabase($GBID, $userID)) {
                // developer found
                return true;
            }
        }

        // developer was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "developer");

        // if developer was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add developer to database
            $this->addDeveloper($result->results);

            // get developer from database
            return $this->getDeveloperFromDatabase($GBID, $userID);
        } else {
            // developer was not found
            return false;
        }
    }

    // get developer from database
    public function getDeveloperFromDatabase($GBID, $userID)
    {
        // get developer from db
        $this->db->select('developers.DeveloperID, developers.GBID, developers.GBLink, developers.Name, developers.Image, developers.ImageSmall, developers.Deck');
        $this->db->from('developers');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.DeveloperID = developers.DeveloperID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('developers.GBID', $GBID);
        $query = $this->db->get();

        // if developer returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->developerID = $result->DeveloperID;
            $this->GBID = $result->GBID;
            $this->GBLink = $result->GBLink;
            $this->name = $result->Name;
            $this->image = $result->Image;
            $this->imageSmall = $result->ImageSmall;
            $this->deck = $result->Deck;

            return true;
        }
        
        return false;
    }
}