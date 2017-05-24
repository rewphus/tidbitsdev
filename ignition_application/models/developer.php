<?php 

class Developer extends CI_Model {

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
            // add developer to db
            $this->Developer->addDeveloper($gbDeveloper);

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
           'API_Detail' => $developer->api_detail_url
        );

        return $this->db->insert('developers', $data); 
    }

    // get developer data from Giant Bomb API
    function getDeveloper($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/developer/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Developer");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}