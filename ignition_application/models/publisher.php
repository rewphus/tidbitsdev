<?php 

class Publisher extends CI_Model {

        // publisher data
    var $publisherID;
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
            // publisher was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbPublisher->id, "company");

            // if publisher was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add publisher to database
                $this->addPublisher($result->results);

            } else {
                // publisher was not found
                return false;
            }

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
           'API_Detail' => $publisher->api_detail_url,
            'GBLink' => $publisher->site_detail_url,
           'Image' => is_object($publisher->image) ? $publisher->image->small_url : null,
           'ImageSmall' => is_object($publisher->image) ? $publisher->image->icon_url : null,
           'Deck' => $publisher->deck           
        );

        return $this->db->insert('publishers', $data); 
    }

    // get publisher by GBID
    public function getPublishers($GBID, $userID)
    {
        // check if publisher is in database
        if ($this->isPublisherInDB($GBID)) {
            // get publisher from database
            if ($this->getPublisherFromDatabase($GBID, $userID)) {
                // publisher found
                return true;
            }
        }

        // publisher was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "company");

        // if publisher was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add publisher to database
            $this->addPublisher($result->results);

            // get publisher from database
            return $this->getPublisherFromDatabase($GBID, $userID);
        } else {
            // publisher was not found
            return false;
        }
    }

    // get publisher from database
    public function getPublisherFromDatabase($GBID, $userID)
    {
        // get publisher from db
        $this->db->select('publishers.PublisherID, publishers.GBID, publishers.GBLink, publishers.Name, publishers.Image, publishers.ImageSmall, publishers.Deck');
        $this->db->from('publishers');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.PublisherID = publishers.PublisherID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('publishers.GBID', $GBID);
        $query = $this->db->get();

        // if publisher returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->publisherID = $result->PublisherID;
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