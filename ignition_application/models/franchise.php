<?php 

class Franchise extends CI_Model {

    // franchise data
    var $franchiseID;
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
            // franchise was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbFranchise->id, "franchise");

            // if franchise was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add franchise to database
                $this->addFranchise($result->results);

            } else {
                // franchise was not found
                return false;
            }

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
           'API_Detail' => $franchise->api_detail_url,
            'GBLink' => $franchise->site_detail_url,
           'Image' => is_object($franchise->image) ? $franchise->image->small_url : null,
           'ImageSmall' => is_object($franchise->image) ? $franchise->image->icon_url : null,
           'Deck' => $franchise->deck           
        );

        return $this->db->insert('franchises', $data); 
    }

    // get franchise by GBID
    public function getFranchises($GBID, $userID)
    {
        // check if franchise is in database
        if ($this->isFranchiseInDB($GBID)) {
            // get franchise from database
            if ($this->getFranchiseFromDatabase($GBID, $userID)) {
                // franchise found
                return true;
            }
        }

        // franchise was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "franchise");

        // if franchise was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add franchise to database
            $this->addFranchise($result->results);

            // get franchise from database
            return $this->getFranchiseFromDatabase($GBID, $userID);
        } else {
            // franchise was not found
            return false;
        }
    }

    // get franchise from database
    public function getFranchiseFromDatabase($GBID, $userID)
    {
        // get franchise from db
        $this->db->select('franchises.FranchiseID, franchises.GBID, franchises.GBLink, franchises.Name, franchises.Image, franchises.ImageSmall, franchises.Deck');
        $this->db->from('franchises');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.FranchiseID = franchises.FranchiseID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('franchises.GBID', $GBID);
        $query = $this->db->get();

        // if franchise returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->franchiseID = $result->FranchiseID;
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