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
    public function getPanelGroupInfo_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GBoard = $request->idGroupBoard;

        $primaryBoard = $this->BoardInterface->getInfoGroupBoard($ID_GBoard);

        $response = [
            'ID_GB' => $primaryBoard[0]->ID_GB, 
            'ID_GBUser' => $primaryBoard[0]->ID_GBUser, 
            'GBname' => $primaryBoard[0]->GBname, 
            'primaryGroupBoard' => $primaryBoard[0]->primaryGroupBoard, 
            'defWidth' => $primaryBoard[0]->defWidth, 
            'defHeight' => $primaryBoard[0]->defHeight, 
            'imgGB' => $primaryBoard[0]->imgGB
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function getUserPanelGroups_post() {
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

    public function setPrimaryGroupBoard_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GBoard = $request->ID_GB;
        $idusu = $this->session->userdata('idusu');

        $this->panelInterface->setPrimaryGroupBoard($ID_GBoard, $idusu);

        $response = [
            'id' => $primaryBoard[0]->ID_Board
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function newGroupPanel_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $GBName = $request->GBName;
        $defW = $request->defW;
        $defH = $request->defH;
        $imgGB = $request->imgGB;
        $idusu = $this->session->userdata('idusu');
        $this->BoardInterface->initTrans();
        $id = $this->panelInterface->newGroupPanel($GBName, $idusu, $defW, $defH, $imgGB);

        $idBoard = $this->BoardInterface->createBoard($id, "default", $defW, $defH);
        $this->addColumns(0, 0, $idBoard, $defW);
        $this->addRows($defW, 0, $idBoard, $defH);
        $this->BoardInterface->setPrimaryBoard($idBoard);
        $this->BoardInterface->commitTrans();
        $response = [
            'idBoard' => $idBoard
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    //MODIF: Esta repetida, mirar que se puede hacer
    public function addColumns($columns, $rows, $idBoard, $columnsToAdd) {
        $currentPos = ($columns + $columnsToAdd) * $rows;
        $oldCurrentPos = $columns * $rows;
        for ($row = 0; $row < $rows; $row++) {
            for ($i = $columns; $i < $columns + $columnsToAdd; $i++) {
                $this->BoardInterface->newCell($currentPos, $idBoard);
                $currentPos--;
            }
            for ($column = 0; $column < $columns; $column++) {
                $this->BoardInterface->updatePosCell($oldCurrentPos, $currentPos, $idBoard);
                $currentPos--;
                $oldCurrentPos--;
            }
        }
    }
    //MODIF: Esta repetida, mirar que se puede hacer
    public function addRows($columns, $rows, $idBoard, $rowsToAdd) {
        $currentPos = $columns * $rows + 1;
        for ($row = 0; $row < $rowsToAdd; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $this->BoardInterface->newCell($currentPos, $idBoard);
                $currentPos++;
            }
        }
    }
    
    public function modifyGroupBoardName_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GB = $request->ID;
        $name = $request->Name;
        $idusu = $this->session->userdata('idusu');

        $this->panelInterface->changeGroupName($ID_GB, $name, $idusu);

        $response = [
            'id' => $primaryBoard[0]->ID_Board
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }


}
