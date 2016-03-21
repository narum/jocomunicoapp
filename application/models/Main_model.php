<?php 

class Main_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getStrings($section, $idLanguage)
    {
        $this->db->select('tagString, content'); // Seleccionar les columnes
        $this->db->from('Content');// Seleccionem la taula
        $this->db->where('section', $section);// filtrem per columnes
        $this->db->where('ID_CLanguage', $idLanguage);// filtrem per columnes
        $this->db->order_by('Content.tagString', 'asc');
        $query = $this->db->get();// Fem la query i la guardem a la variable query

        return $query->result_array();// retornem l'array query amb els resultats
    }
        
}