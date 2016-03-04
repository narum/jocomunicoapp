<?php 

class Register_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getStrings($section)
    {
        //Query del content en Catalan
            $this->db->select('tagString, content'); // Seleccionar les columnes
            $this->db->from('Content');// Seleccionem la taula
            $this->db->where('section', $section);// filtrem per columnes
            $this->db->where('ID_CLanguage', '1');// filtrem per columnes
            $this->db->order_by('Content.tagString', 'asc');
            $query1 = $this->db->get()->result_array();// Fem la query i la guardem a la variable query

        //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
            $array1 = array_column($query1, 'tagString');
            $array2 = array_column($query1, 'content');

            $catalan = array_combine($array1, $array2);

        //Query del content en Castellano
            $this->db->select('tagString, content'); // Seleccionar les columnes
            $this->db->from('Content');// Seleccionem la taula
            $this->db->where('section', $section);// filtrem per columnes
            $this->db->where('ID_CLanguage', '2');// filtrem per columnes
            $this->db->order_by('Content.tagString', 'asc');
            $query2 = $this->db->get()->result_array();// Fem la query i la guardem a la variable query

         //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
            $array3 = array_column($query2, 'tagString');
            $array4 = array_column($query2, 'content');

            $spanish = array_combine($array3, $array4);

        // Convertimos el array en un objeto
            $response = [
                "catalan" => $catalan,
                "spanish" => $spanish
            ];

        return $response;// retornem l'array query amb els resultats
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
}