<?php 

class Location extends CI_Model {

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
            // add location to db
            $this->Location->addLocation($gbLocation);

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
           'Abbreviation' => $location->abbreviation,
           'API_Detail' => $location->api_detail_url
        );

        return $this->db->insert('locations', $data); 
    }

    // get location data from Giant Bomb API
    function getLocation($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/location/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Location");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}