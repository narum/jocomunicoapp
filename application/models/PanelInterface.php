<?php

class PanelInterface extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }

    function getUserPanels($idusu) {
        $output = array();

        $this->db->where('ID_GBUser', $idusu);
        $query = $this->db->get('GroupBoards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

}
