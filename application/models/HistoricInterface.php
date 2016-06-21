<?php

class HistoricInterface extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getSFolders($idusu) {

        $this->db->where('ID_SFUser', $idusu);
        $query = $this->db->get('S_Folder');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

}
