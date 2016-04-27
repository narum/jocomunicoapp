<?php

class ImgUploader_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

    }

    /*
     * Get the board struct (columns, rows, name...) 
     */

    function getCountIdImgUsu($idusu) {
        $query = 0;

        
        $this->db->where('ID_ISU',$idusu);
        $this->db->from('Images');
        $query = $this->db->count_all_results();

        return $query;
    }

}
