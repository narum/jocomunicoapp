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
    // Comprobación de un campo de una celda de una tabla
    public function checkSingleData($table, $columnId, $id, $column, $data){

        $this->db->select($column); // Seleccionar les columnes
        $this->db->from($table);// Seleccionem la taula
        $this->db->where($columnId, $id);// filtrem per columnes
        $this->db->where($column, $data);// filtrem per columnes
        $query = $this->db->get();// Fem la query i la guardem a la variable query
        $array = $query->result_array();

        if ($query->num_rows() == 0)
        {
            $result = "false";
        }else if($array[0][$column]==$data){
            $result = "true";
        }else{
            $result = "false";
        }
        $response = [
                "data" => $result
            ];
        return $response;
    }
    
    // Get data from table $table where content in column $column are like $data
    public function getData($table, $column, $data){
        $this->db->from($table);// Seleccionem la taula
        $this->db->where($column, $data);// filtrem per columnes
        $data = $this->db->get()->result_array();
        
        return $data[0];
    }
    
    // Guardar contenido en una tabla.
    public function saveData($table, $data){

        $saved = $this->db->insert($table, $data);

        return $saved;
    }
    // Cambiar contenido de una tabla.
    public function changeData($table, $column, $id, $data){

        $this->db->where($column, $id);
        $saved = $this->db->update($table, $data);

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
            "cfgExpansionLanguage" => $ID_ULanguage,
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

        $userExist=false;
        $validated=false;

        if($hash == $emailKey){
            $userExist=true;
        }
        if($userValidated[0] == 0){
            $this->db->set('UserValidated', '1');
            $this->db->where('ID_SU', $ID_SU);
            $validated = $this->db->update('SuperUser');
        }

        $response = [
                "validated" => $validated,
                "userExist" => $userExist
            ];
        return $response;
    }
    //Get user configuratión
    public function getConfig($ID_SU)
    {
        // Get user data and user config data
        $this->db->from('SuperUser');
        $this->db->join('Languages', 'SuperUser.cfgDefUser = Languages.ID_Language', 'right');
        $this->db->join('User', 'SuperUser.ID_SU = User.ID_USU AND SuperUser.cfgDefUser = User.ID_ULanguage');
        $this->db->where('ID_SU', $ID_SU);
        $query1 = $this->db->get()->result_array();
        $userConfig = $query1[0];

        $this->db->from('User');
        $this->db->join('Languages', 'User.ID_ULanguage = Languages.ID_Language', 'right');
        $this->db->where('ID_USU', $ID_SU);
        $query2 = $this->db->get()->result_array();

        $this->db->from('User');
        $this->db->join('Languages', 'User.cfgExpansionLanguage = Languages.ID_Language', 'right');
        $this->db->where('ID_USU', $ID_SU);
        $query3 = $this->db->get()->result_array();

        // Guardamos los datos como objeto
        $Array = [
            'userConfig' => $userConfig,
            'usersInterficieLanguages' => $query2,
            'userExpanlanguages' => $query3
        ];

        return $Array;
    }
}