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
        
        $this->db->select('ID_User'); // Seleccionar les columnes
        $this->db->from('User');// Seleccionem la taula
        $this->db->where('ID_USU', $id[0]);// filtrem per columnes
        $ID_User = $this->db->get()->result_array();

        $idU = array_column($ID_User, 'ID_User');

        //Retornamos el ID_SUser y el ID_User
        $dataSaved = [
            "ID_SU" => $id[0],
            "ID_U" => $idU[0],
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
        $this->db->join('User', 'SuperUser.cfgDefUser = User.ID_User');
        $this->db->join('Languages', 'SuperUser.cfgDefUser = User.ID_User AND User.ID_ULanguage = Languages.ID_Language', 'right');
        $this->db->where('ID_USU', $ID_SU);
        $query1 = $this->db->get()->result_array();
        $userConfig = $query1[0];

        //Get Users
        $this->db->select('ID_User, ID_ULanguage, cfgExpansionLanguage');
        $this->db->from('User');
        $this->db->where('ID_USU', $ID_SU);
        $this->db->order_by('User.ID_ULanguage', 'asc');
        $query2 = $this->db->get()->result_array();

        //Get Languages
        $this->db->select('ID_Language, languageName');
        $this->db->from('Languages');
        $this->db->order_by('Languages.ID_Language', 'asc');
        $query3 = $this->db->get()->result_array();

        
        // Guardamos los datos como objeto
        $Array = [
            'userConfig' => $userConfig,
            'users' => $query2,
            'languages' => $query3,
        ];
        
        // Save user config data in the COOKIES
        $this->session->set_userdata('idusu', $userConfig["ID_User"]);
        $this->session->set_userdata('uname', $userConfig["SUname"]);
        $this->session->set_userdata('ulanguage', $userConfig["cfgExpansionLanguage"]);
        $this->session->set_userdata('uinterfacelangauge', $userConfig["ID_ULanguage"]);
        $this->session->set_userdata('uinterfacelangtype', $userConfig["type"]);
        $this->session->set_userdata('uinterfacelangnadjorder', $userConfig["nounAdjOrder"]);
        $this->session->set_userdata('uinterfacelangncorder', $userConfig["nounComplementOrder"]);
        $this->session->set_userdata('uinterfacelangabbr', $userConfig["languageabbr"]);
        $this->session->set_userdata('autoEraseSentenceBar', $userConfig["cfgAutoEraseSentenceBar"]);
        $this->session->set_userdata('isfem', $userConfig["cfgIsFem"]);

        // Save Expansion language in the COOKIES
        $this->db->select('canExpand');
        $this->db->where('ID_Language', $userConfig["cfgExpansionLanguage"]);
        $canExpand = $this->db->get('Languages');

        if ($canExpand == '1'){
            $this->session->set_userdata('ulangabbr', $userConfig["languageabbr"]);
        }else{
            $this->session->set_userdata('ulangabbr', 'ES');
        }

        return $Array;
    }
}