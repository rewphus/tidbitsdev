<?php 

class Genre extends CI_Model {

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
            // add genre to db
            $this->Genre->addGenre($gbGenre);

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
           'API_Detail' => $genre->api_detail_url
        );

        return $this->db->insert('genres', $data); 
    }

    // get genre data from Giant Bomb API
    function getGenre($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/genre/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Genre");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}