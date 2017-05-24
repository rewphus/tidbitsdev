<?php 

class Theme extends CI_Model {

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
            // add theme to db
            $this->Theme->addTheme($gbTheme);

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
           'API_Detail' => $theme->api_detail_url
        );

        return $this->db->insert('themes', $data); 
    }

    // get theme data from Giant Bomb API
    function getTheme($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/theme/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url, "Theme");
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}