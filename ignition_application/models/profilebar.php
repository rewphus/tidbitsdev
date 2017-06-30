<?php

class Collection extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

        // get PlatformID from GBID
    function getPlatformByGBID($GBID)
    {
        $query = $this->db->get_where('platforms', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }
}