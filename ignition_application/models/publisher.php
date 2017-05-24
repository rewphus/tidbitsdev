<?php 

class Publisher extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is publisher in database?
    function isPublisherInDB($GBID)
    {
        $query = $this->db->get_where('publishers', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get PublisherID from GBID
    function getPublisherByGBID($GBID)
    {
        $query = $this->db->get_where('publishers', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns publisher if in db, or adds and returns it if it isn't
    function getOrAddPublisher($gbPublisher)
    {
        // get publisher from db
        $publisher = $this->getPublisherByGBID($gbPublisher->id);

        // if publisher isn't in db
        if($publisher == null)
        {
            // add publisher to db
            $this->Publisher->addPublisher($gbPublisher);

            // get publisher from db
            $publisher = $this->getPublisherByGBID($gbPublisher->id);
        }

        return $publisher;
    }

    // add publisher to database
    function addPublisher($publisher)
    {
        $data = array(
           'GBID' => $publisher->id,
           'Name' => $publisher->name,
           'API_Detail' => $publisher->api_detail_url
        );

        return $this->db->insert('publishers', $data); 
    }

    // get publisher data from Giant Bomb API
    function getPublisher($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/publisher/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Publisher");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}