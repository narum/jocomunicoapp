<?php 

class Main_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Petición del contenido para mostrar en las vistas (textos)
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
    
    // Idiomas disponibles en la tabla Languages.
    public function getLanguagesAvailable(){
        //Peticion a base de datos
            $this->db->select('ID_Language, languageName'); // Seleccionar les columnes
            $this->db->from('Languages');// Seleccionem la taula
            $query = $this->db->get();

            return $query->result_array();// retornamos el array
    }
    
    // Comprobación de un campo de una columna de una tabla
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
    
    // Guardar contenido en una tabla.
    public function saveData($table, $data){

        $saved = $this->db->insert($table, $data);

        return $saved;
    }
    
    // Escrivir en la tabla Usuario
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

        //Retornamos el ID_USU
        $dataSaved = [
            "ID_SU" => $id[0],
            "saved" => $saved,
        ];

        return $dataSaved;
    }
    
    // Validar usuario al registrarse
    public function userValidation($emailKey, $ID_SU){

        $this->db->select('pswd, UserValidated'); // Seleccionar les columnes
        $this->db->from('SuperUser');// Seleccionem la taula
        $this->db->where('ID_SU', $ID_SU);// filtrem per columnes
        $query = $this->db->get()->result_array();

        $pass = array_column($query, 'pswd');
        $userValidated = array_column($query, 'UserValidated');

        $hash = md5($pass[0] . $ID_SU);

        if($userValidated[0] == 1){
            return false;
        }else if($hash == $emailKey){

            $this->db->set('UserValidated', '1');
            $this->db->where('ID_SU', $ID_SU);
            $saved = $this->db->update('SuperUser');

            return $saved;
        }else{
            return false;
        }
    }
}