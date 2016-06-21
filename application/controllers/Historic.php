
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Historic extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('HistoricInterface');
    }

    public function index_get() {

    }

    public function getSFolder_post() {
        $idusu = $this->session->userdata('idusu');
        $sFolder = $this->HistoricInterface->getSFolders($idusu);

        $response = [
            'sFolder' => $sFolder
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
