<?php 

class Main_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getContent($section, $idLanguage)
    {
        $this->db->select('tagString, content'); // Seleccionar les columnes
        $this->db->from('Content');// Seleccionem la taula
        $this->db->where('section', $section);// filtrem per columnes
        $this->db->where('ID_CLanguage', $idLanguage);// filtrem per columnes
        $this->db->order_by('Content.tagString', 'asc');
        $query = $this->db->get();// Fem la query i la guardem a la variable query

        return $query->result_array();// retornem l'array query amb els resultats
    }
    
    public function getLanguagesAvailable(){
        //Peticion a base de datos
            $this->db->select('ID_Language, languageName'); // Seleccionar les columnes
            $this->db->from('Languages');// Seleccionem la taula
            $query = $this->db->get();

            return $query->result_array();// retornamos el array
    }
    //De momento no se usa!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    public function getAllContent($section)
    {
        $this->db->select('ID_CLanguage, tagString, content'); // Seleccionar les columnes
        $this->db->from('Content');// Seleccionem la taula
        $this->db->where('section', $section);// filtrem per columnes
        $this->db->order_by('Content.ID_CLanguage', 'asc');
        $query = $this->db->get();// Fem la query i la guardem a la variable query

        return $query->result_array();// retornem l'array query amb els resultats
    }
    
    public function checkData($table, $column, $data){

        $this->db->where($column, $data);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
        {
           $result = "true";
        } else{
           $result = "false";
        }
        
        return $result;
    }
    public function saveData($table, $data){
        
        $saved = $this->db->insert($table, $data);
        
        return $saved;
    }
    public function saveUser($SUname, $ID_ULanguage){
        
        $this->db->select('ID_SU'); // Seleccionar les columnes
        $this->db->from('SuperUser');// Seleccionem la taula
        $this->db->where('SUname', $SUname);// filtrem per columnes
        $ID_SU = $this->db->get()->result_array();
        
        $id = array_column($ID_SU, 'ID_SU');
        
        $data = [
            "ID_USU" => $id[0],
            "ID_ULanguage" => $ID_ULanguage,
        ];
        
        $saved = $this->db->insert('User', $data);
        
        return $saved;
    }
        
}