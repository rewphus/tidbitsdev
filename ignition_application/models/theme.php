<?php 

class Theme extends CI_Model {

        // theme data
    var $themeID;
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

    // is theme in database?
    function isThemeInDB($GBID)
    {
        $query = $this->db->get_where('themes', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get ThemeID from GBID
    function getThemeByGBID($GBID)
    {
        $query = $this->db->get_where('themes', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns theme if in db, or adds and returns it if it isn't
    function getOrAddTheme($gbTheme)
    {
        // get theme from db
        $theme = $this->getThemeByGBID($gbTheme->id);

        // if theme isn't in db
        if($theme == null)
        {
            // theme was not found, get from Giant Bomb
            $this->load->model('GiantBomb');
            $result = $this->GiantBomb->getMeta($gbTheme->id, "theme");

            // if theme was returned
            if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
                // add theme to database
                $this->addTheme($result->results);

            } else {
                // theme was not found
                return false;
            }

            // get theme from db
            $theme = $this->getThemeByGBID($gbTheme->id);
        }

        return $theme;
    }

    // add theme to database
    function addTheme($theme)
    {
        $data = array(
           'GBID' => $theme->id,
           'Name' => $theme->name,
           'API_Detail' => $theme->api_detail_url,
            'GBLink' => $theme->site_detail_url,
           'Image' => is_object($theme->image) ? $theme->image: null,
           'Deck' => $theme->deck           
        );

        return $this->db->insert('themes', $data); 
    }

    // get theme by GBID
    public function getThemes($GBID, $userID)
    {
        // check if theme is in database
        if ($this->isThemeInDB($GBID)) {
            // get theme from database
            if ($this->getThemeFromDatabase($GBID, $userID)) {
                // theme found
                return true;
            }
        }

        // theme was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getMeta($GBID, "theme");

        // if theme was returned
        if ($result != null && $result->error == "OK" && $result->number_of_total_results > 0) {
            // add theme to database
            $this->addTheme($result->results);

            // get theme from database
            return $this->getThemeFromDatabase($GBID, $userID);
        } else {
            // theme was not found
            return false;
        }
    }

    // get theme from database
    public function getThemeFromDatabase($GBID, $userID)
    {
        // get theme from db
        $this->db->select('themes.ThemeID, themes.GBID, themes.GBLink, themes.Name, themes.Image, themes.ImageSmall, themes.Deck');
        $this->db->from('themes');

        if ($userID == null) {
            $userID = 0; // prevents joining on UserID causing an error
        }

        // $this->db->join('collections', 'collections.ThemeID = themes.ThemeID AND collections.UserID = ' . $userID, 'left');
        // $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');

        $this->db->where('themes.GBID', $GBID);
        $query = $this->db->get();

        // if theme returned
        if ($query->num_rows() == 1) {
            $result = $query->first_row();

            $this->themeID = $result->ThemeID;
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