<?php 

class Genre extends CI_Model {

        // genre data
    var $genreID;
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

    // is genre in database?
    function isGenreInDB($GBID)
    {
        $query = $this->db->get_where('genres', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get GenreID from GBID
    function getGenreByGBID($GBID)
    {
        $query = $this->db->get_where('genres', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns genre if in db, or adds and returns it if it isn't
    function getOrAddGenre($gbGenre)
    {
        // get genre from db
        $genre = $this->getGenreByGBID($gbGenre->id);

        // if genre isn't in db
        if($genre == null)
        {
            // genre was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbGenre->id, "genre");

            // if genre was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add genre to database
                $this->addGenre($result->results);

            } else {
                // genre was not found
                return false;
            }

            // get genre from db
            $genre = $this->getGenreByGBID($gbGenre->id);
        }

        return $genre;
    }

    // add genre to database
    function addGenre($genre)
    {
        $data = array(
           'GBID' => $genre->id,
           'Name' => $genre->name,
           'API_Detail' => $genre->api_detail_url,
            'GBLink' => $genre->site_detail_url,
           'Image' => is_object($genre->image) ? $genre->image : null,
           'Deck' => $genre->deck           
        );

        return $this->db->insert('genres', $data); 
    }

    // get genre by GBID
    public function getGenres($GBID, $userID)
    {
        // check if genre is in database
        if ($this->isGenreInDB($GBID)) {
            // get genre from database
            if ($this->getGenreFromDatabase($GBID, $userID)) {
                // genre found
                return true;
            }
        }

        // genre was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "genre");

        // if genre was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add genre to database
            $this->addGenre($result->results);

            // get genre from database
            return $this->getGenreFromDatabase($GBID, $userID);
        } else {
            // genre was not found
            return false;
        }
    }

    // get genre from database
    public function getGenreFromDatabase($GBID, $userID)
    {
        // get genre from db
        $this->db->select('genres.GenreID, genres.GBID, genres.GBLink, genres.Name, genres.Image, genres.ImageSmall, genres.Deck');
        $this->db->from('genres');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.GenreID = genres.GenreID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('genres.GBID', $GBID);
        $query = $this->db->get();

        // if genre returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->genreID = $result->GenreID;
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