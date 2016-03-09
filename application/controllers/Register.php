
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Register extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct('rest', TRUE);
        $this->load->model('main_model');
    }
    
    public function allContent_get()
    {
        $section = $this->query("section");
        if($section == NULL || $section == "") {
            $this->response("missing argument startswith", 400);
        }
        else {
            $languages = $this->main_model->getLanguagesAvailable();

            //miramos el numero de idiomas disponibles
            $languagesNumber = sizeof($languages);

            //Creamos un array con el content en cada idioma
            for ($i = 1; $i <= $languagesNumber; $i++) {

                $result = $this->main_model->getContent($section,$i);

                //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
                $array1 = array_column($result, 'tagString');
                $array2 = array_column($result, 'content');
                $keyValue = array_combine($array1, $array2);

                $content[$i]=$keyValue;
            }

            $response = [
                "languages" => $languages,
                "content" => $content
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    
    public function languagesAvailable_get()
    {
        $query = $this->main_model->getLanguagesAvailable();
        //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
            $array1 = array_column($query, 'ID_Language');
            $array2 = array_column($query, 'languageName');
            $languages = array_combine($array1, $array2);
        // Convertimos el array en un objeto
            $response = [
                "languages" => $languages
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function content_get()
    {
        //parametros que nos llegan del get
        $section = $this->query("section");
        $idLanguage = $this->query("idLanguage");

        //comprobación de los parametros
        if($section == NULL || $section == "" || $idLanguage == NULL || $idLanguage == "") {
            $this->response("missing argument startswith", 400);
        } 
        else {

            //Petición al modelo
            $saveResult = $this->main_model->getContent($section, $idLanguage);

            
            //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
            $array1 = array_column($saveResult, 'tagString');
            $array2 = array_column($saveResult, 'content');

            $keyValue = array_combine($array1, $array2);

            // Convertimos el array en un objeto
            $response = [
                "data" => $keyValue
            ];
            
            //respuesta
            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }

    public function checkData_get()
    {
        $table=$this->query("table");
        $column=$this->query("column");
        $data=$this->query("data");

        $exist = $this->main_model->checkData($table, $column, $data);

        $response = [
                "exist" => $exist
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    
    public function saveData_post()
    {
        $data = json_decode($this->query("data"), true); // convertimos el string json del post en array.
        $table = $this->query("table");

        $saved=$this->main_model->saveData($table, $data);

        $response = [
                "saved" => $saved
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function saveUserData_post()
    {
        $SUname = $this->query("SUname");
        $ID_ULanguage = $this->query("ID_ULanguage");
        
        $saved=$this->main_model->saveUser($SUname,$ID_ULanguage);
        
        $response = [
                "saved" => $saved
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
}
