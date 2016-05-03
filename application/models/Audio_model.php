<?php

class Audio_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 
     * @param bool/int $id if set to false, all voices are returned,
     * else, the voice with the set $id is returned
     * @return array $output a row for each returned voice with all the fields
     * from the database
     */
    public function getOnlineVoices($id) 
    {
        if ($id) $this->db->where('ID_Voice', $id);
        
        $output = array();
        $query = $this->db->get('Voices');
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        
        return $output;
    }
    
    /**
     * 
     * @param int $idusu
     * @return array $output a row with all the fields from the database
     */
    public function getUserInfo($idusu) 
    {
        $this->db->where('ID_User', $idusu);
        
        $output = array();
        $query = $this->db->get('User');
        
        if ($query->num_rows() > 0) {
            $aux = $query->result();
            $output = $aux[0];
        }
        
        return $output;
    }
    
    
}
?>