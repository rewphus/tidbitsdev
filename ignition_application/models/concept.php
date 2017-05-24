<?php 

class Concept extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is concept in database?
    function isConceptInDB($GBID)
    {
        $query = $this->db->get_where('concepts', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get ConceptID from GBID
    function getConceptByGBID($GBID)
    {
        $query = $this->db->get_where('concepts', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns concept if in db, or adds and returns it if it isn't
    function getOrAddConcept($gbConcept)
    {
        // get concept from db
        $concept = $this->getConceptByGBID($gbConcept->id);

        // if concept isn't in db
        if($concept == null)
        {
            // add concept to db
            $this->Concept->addConcept($gbConcept);

            // get concept from db
            $concept = $this->getConceptByGBID($gbConcept->id);
        }

        return $concept;
    }

    // add concept to database
    function addConcept($concept)
    {
        $data = array(
           'GBID' => $concept->id,
           'Name' => $concept->name,
           'API_Detail' => $concept->api_detail_url
        );

        return $this->db->insert('concepts', $data); 
    }

    // get concept data from Giant Bomb API
    function getConcept($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/concept/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Concept");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}