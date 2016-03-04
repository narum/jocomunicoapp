
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Register extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct('rest', TRUE);
        $this->load->model('register_model');
    }

    public function content_get()
    {
        //parametros que nos llegan del get
        $section = $this->query("section");

        //comprobación de los parametros
        if($section == NULL || $section == "") {
            $this->response("missing argument startswith", 400);
        } 
        else {

            //Petición al modelo
            $response = $this->register_model->getStrings($section);

            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }

    public function checkData_get()
    {
        $table=$this->query("table");
        $column=$this->query("column");
        $data=$this->query("data");

        $exist = $this->register_model->checkData($table, $column, $data);

        $response = [
                "exist" => $exist
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
}
