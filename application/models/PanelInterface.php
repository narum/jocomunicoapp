<?php

class PanelInterface extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }
    
    /*
     * Get all group panels owned by a user (idusu)
     */

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

    /*
     * Set the group board ($ID_GB) primary in the group ($idusu, the user)
     */
    
    function setPrimaryGroupBoard($ID_GB, $idusu) {
        $this->db->where('ID_GBUser', $idusu);
        $this->db->update('GroupBoards', array(
            'primaryGroupBoard' => '0',
        ));

        $this->db->where('ID_GB', $ID_GB);
        $this->db->update('GroupBoards', array(
            'primaryGroupBoard' => '1',
        ));
    }
    
    /*
     * Set the group board ($ID_GB) primary in the group ($idusu, the user)
     */
    
    function newGroupPanel($GBName, $idusu, $defW, $defH, $imgGB) {
        $data = array(
            'ID_GBUser' => $idusu,
            'GBName' => $GBName,
            'primaryGroupBoard' => '0',
            'defWidth' => $defW,
            'defHeight' => $defH,
            'imgGB' => $imgGB
        );
        
        $this->db->insert('GroupBoards', $data);
        
        $id = $this->db->insert_id();

        return $id;
    }
    
    /*
     * Change the group board Name
     */
    
    function changeGroupName($ID_GB, $name, $idusu) {
        $this->db->where('ID_GBUser', $idusu);
        $this->db->where('ID_GB', $ID_GB);
        $this->db->update('GroupBoards', array(
            'GBname' => $name,
        ));
    }
    
}
