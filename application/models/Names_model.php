<?php 

class Names_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getNoms($startswith, $language)
    {
        
        $this->db->select('nameid, nomtext, imgPicto');// seleccionem els camps que ens interessa retornar
        $this->db->from('Name'. $language);// Seleccionem la taula nameca o namees
        $this->db->join('Pictograms', 'Name' . $language . '.nameid = Pictograms.pictoid', 'left'); // ajuntem les columnes de les dos taules
        $this->db->like('nomtext', $startswith, 'after');// Seleccionem els noms de la taula que comencen per $startswith
        $this->db->order_by('Name' . $language . '.nomtext', 'asc'); // ordenem de manera ascendent tota la taula en funció del nomtext
        $query = $this->db->get();// Fem la query i la guardem a la variable query
        
        return $query->result_array();// retornem l'array query amb els resultats
    
    }
        
}