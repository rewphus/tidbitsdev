<?php 

class Concept extends CI_Model {

    // concept data
    var $conceptID;
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
            // concept was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbConcept->id, "concept");

            // if concept was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add cconcept to database
                $this->addConcept($result->results);

            } else {
                // concept was not found
                return false;
            }

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
           'API_Detail' => $concept->api_detail_url,
            'GBLink' => $concept->site_detail_url,
           'Image' => is_object($concept->image) ? $concept->image->small_url : null,
           'ImageSmall' => is_object($concept->image) ? $concept->image->icon_url : null,
           'Deck' => $concept->deck
        );

        return $this->db->insert('concepts', $data); 
    }

    // get concept by GBID
    public function getConcepts($GBID, $userID)
    {
        // check if concept is in database
        if ($this->isConceptInDB($GBID)) {
            // get concept from database
            if ($this->getConceptFromDatabase($GBID, $userID)) {
                // concept found
                return true;
            }
        }

        // concept was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "concept");

        // if concept was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add concept to database
            $this->addConcept($result->results);

            // get concept from database
            return $this->getConceptFromDatabase($GBID, $userID);
        } else {
            // concept was not found
            return false;
        }
    }

    // get concept from database
    public function getConceptFromDatabase($GBID, $userID)
    {
        // get concept from db
        $this->db->select('concepts.ConceptID, concepts.GBID, concepts.GBLink, concepts.Name, concepts.Image, concepts.ImageSmall, concepts.Deck');
        $this->db->from('concepts');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.ConceptID = concepts.ConceptID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('concepts.GBID', $GBID);
        $query = $this->db->get();

        // if concept returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->conceptID = $result->ConceptID;
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