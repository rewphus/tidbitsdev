<?php 

class Location extends CI_Model {

        // location data
    var $locationID;
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

    // is location in database?
    function isLocationInDB($GBID)
    {
        $query = $this->db->get_where('locations', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get LocationID from GBID
    function getLocationByGBID($GBID)
    {
        $query = $this->db->get_where('locations', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns location if in db, or adds and returns it if it isn't
    function getOrAddLocation($gbLocation)
    {
        // get location from db
        $location = $this->getLocationByGBID($gbLocation->id);

        // if location isn't in db
        if($location == null)
        {
            // location was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbLocation->id, "location");

            // if location was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add location to database
                $this->addLocation($result->results);

            } else {
                // location was not found
                return false;
            }

            // get location from db
            $location = $this->getLocationByGBID($gbLocation->id);
        }

        return $location;
    }

    // add location to database
    function addLocation($location)
    {
        $data = array(
           'GBID' => $location->id,
           'Name' => $location->name,
           'API_Detail' => $location->api_detail_url,
             'GBLink' => $location->site_detail_url,
           'Image' => is_object($location->image) ? $location->image->small_url : null,
           'ImageSmall' => is_object($location->image) ? $location->image->icon_url : null,
           'Deck' => $location->deck          
        );

        return $this->db->insert('locations', $data); 
    }

    // get location by GBID
    public function getLocations($GBID, $userID)
    {
        // check if location is in database
        if ($this->isLocationInDB($GBID)) {
            // get location from database
            if ($this->getLocationFromDatabase($GBID, $userID)) {
                // location found
                return true;
            }
        }

        // location was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "location");

        // if location was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add location to database
            $this->addLocation($result->results);

            // get location from database
            return $this->getLocationFromDatabase($GBID, $userID);
        } else {
            // location was not found
            return false;
        }
    }

    // get location from database
    public function getLocationFromDatabase($GBID, $userID)
    {
        // get location from db
        $this->db->select('locations.LocationID, locations.GBID, locations.GBLink, locations.Name, locations.Image, locations.ImageSmall, locations.Deck');
        $this->db->from('locations');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.LocationID = locations.LocationID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('locations.GBID', $GBID);
        $query = $this->db->get();

        // if location returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->locationID = $result->LocationID;
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