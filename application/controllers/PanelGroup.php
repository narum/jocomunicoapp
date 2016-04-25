<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class PanelGroup extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('panelInterface');
        $this->load->model('BoardInterface');
    }
    
    public function index_get() {
        // CHECK COOKIES
        if (!$this->session->userdata('uname')) {
            redirect(base_url(), 'location');
        } else {
            if (!$this->session->userdata('cfguser')) {
                $this->BoardInterface->loadCFG($this->session->userdata('uname'));
                $this->load->view('MainBoard', true);
            } else {
                $this->load->view('MainBoard', true);
            }
        }
    }
    

    public function getUserPanels_post() {
        $idusu = $this->session->userdata('idusu');
        $panels = $this->panelInterface->getUserPanels($idusu);

        $response = [
            'panels' => $panels
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function getPanelToEdit_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GBoard = $request->ID_GB;
        
        $primaryBoard = $this->BoardInterface->getPrimaryBoard($ID_GBoard);

        $response = [
            'id' => $primaryBoard[0]->ID_Board
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

}