<?php 

class Franchise extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is franchise in database?
    function isFranchiseInDB($GBID)
    {
        $query = $this->db->get_where('franchises', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get FranchiseID from GBID
    function getFranchiseByGBID($GBID)
    {
        $query = $this->db->get_where('franchises', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns franchise if in db, or adds and returns it if it isn't
    function getOrAddFranchise($gbFranchise)
    {
        // get franchise from db
        $franchise = $this->getFranchiseByGBID($gbFranchise->id);

        // if franchise isn't in db
        if($franchise == null)
        {
            // add franchise to db
            $this->Franchise->addFranchise($gbFranchise);

            // get franchise from db
            $franchise = $this->getFranchiseByGBID($gbFranchise->id);
        }

        return $franchise;
    }

    // add franchise to database
    function addFranchise($franchise)
    {
        $data = array(
           'GBID' => $franchise->id,
           'Name' => $franchise->name,
           'API_Detail' => $franchise->api_detail_url
        );

        return $this->db->insert('franchises', $data); 
    }

    // get franchise data from Giant Bomb API
    function getFranchise($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/franchise/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Franchise");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}