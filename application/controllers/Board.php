
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Board extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('BoardInterface');
        $this->load->model('Lexicon');
        $this->load->library('Myword');
        $this->load->library('Myslot');
        $this->load->library('Mypattern');
        $this->load->library('Myexpander');
        $this->load->library('session');
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

    public function loadCFG_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $iu = $request->idusu;
        $lu = $request->lusu;

        $data = array(
            'idusu' => $iu,
            'ulangabbr' => $lu);

        $this->session->set_userdata($data);
    }

    /*
     * Get the cell's info
     */

    public function getCell_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $idboard = $request->idboard;

        // "1" es el numero de id de la "board"
        $info = $this->BoardInterface->getCell($id, $idboard);


        $response = [
            'info' => $info[0]
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Get the cells of the boards that will be displayed and the 
     * number of rows and columns in order to set the proportion
     */

    public function getCellboard_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idboard = $request->idboard;

        // "1" es el numero de id de la "board"
        $output = $this->BoardInterface->getBoardStruct($idboard);
        $columns = $output[0]->width;
        $rows = $output[0]->height;
        $name = $output[0]->Bname;


        $response = [
            'col' => $columns,
            'row' => $rows,
            'name' => $name
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function showCellboard_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idboard = $request->idboard;

        $array = array();

        // "1" es el numero de id de la "board"
        $output = $this->BoardInterface->getBoardStruct($idboard);
        $columns = $output[0]->width;
        $rows = $output[0]->height;

        $array = $this->BoardInterface->getCellsBoard($idboard);


        $response = [
            'col' => $columns,
            'row' => $rows,
            'data' => $array
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Estos van en otro controlador que seria el de edicion, pero aun no estan hechos
     */
    /*
     * Returns de cells of the boards that will be displayed and the 
     * number of rows and columns in order to set the proportion
     * Modify the number of rows and columns and add or remove cells.
     */

    public function modifyCellboard_post() {
        $this->BoardInterface->initTrans();

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $c = $request->c;
        $r = $request->r;
        $idboard = $request->idboard;

        // "1" es el numero de id de la "board"
//        $output = $this->BoardInterface->getBoardStruct(1);
//        $columns = $output[0]->width + $c;
//        $rows = $output[0]->height + $r;
//        $this->BoardInterface->updateNumCR($columns, $rows, 1);
        //MODIF: cuando pasemos el total se cambia lo de arriba por:

        $output = $this->BoardInterface->getBoardStruct($idboard);
        $this->BoardInterface->updateNumCR($c, $r, $idboard);
        $columnsDiff = $c - $output[0]->width;
        $rowsDiff = $r - $output[0]->height;




        if ($columnsDiff > 0) {
            $this->addColumns($output[0]->width, $output[0]->height, $idboard, $columnsDiff);
        } elseif ($columnsDiff < 0) {
            $this->removeColumns($output[0]->width, $output[0]->height, $idboard, -$columnsDiff);
        } elseif ($rowsDiff > 0) {
            $this->addRows($output[0]->width, $output[0]->height, $idboard, $rowsDiff);
        } elseif ($rowsDiff < 0) {
            $this->removeRows($output[0]->width, $output[0]->height, $idboard, -$rowsDiff);
        }

        //$array = $this->BoardInterface->getCellsBoard(1);



        $this->BoardInterface->commitTrans();

        $output = $this->BoardInterface->getBoardStruct($idboard);
        $response = [
            'col' => '1',
            'row' => '1'
        ];
//        if ($this->BoardInterface->statusTrans() === FALSE) {
//            $response = [
//                'error' => "errorText"
//            ];
//            $this->response($response, 500);
//        } else {
        $this->response($response, REST_Controller::HTTP_OK);
//        }
    }

    /*
     * Add one or more columns to the board. Each cell keeps his physical position
     * currentPos: Cell position in the new "array"
     * oldCurrentPos: Cell position in the old "array"
     * For each row: We create one cell for each column to add
     *             : We move up the other cells in that row
     * We go backwards through the array
     */

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

    /*
     * Remove one or more columns in the board. Each cell keeps his physical position
     * The same than adding columns. We move down and remove instead.
     */

    public function removeColumns($columns, $rows, $idBoard, $columnsToSub) {
        $currentPos = 1;
        $oldCurrentPos = 1;
        //We can add a start trans and commit at the end?
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns - $columnsToSub; $column++) {
                $this->BoardInterface->updatePosCell($oldCurrentPos, $currentPos, $idBoard);
                $oldCurrentPos++;
                $currentPos++;
            }
            for ($i = $columns - $columnsToSub; $i < $columns; $i++) {
                $cell = $this->BoardInterface->getIDCell($oldCurrentPos, $idBoard);
                $this->BoardInterface->removeCell($cell[0]->ID_RCell, $idBoard);
                $oldCurrentPos++;
            }
        }
    }

    /*
     * Add one or more rows to the board. Each cell keeps his physical position
     * currentPos: The last position + 1 (the position where the cell will be added)
     * For each row we add one cell for each column the board has
     */

    public function addRows($columns, $rows, $idBoard, $rowsToAdd) {
        $currentPos = $columns * $rows + 1;
        for ($row = 0; $row < $rowsToAdd; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $this->BoardInterface->newCell($currentPos, $idBoard);
                $currentPos++;
            }
        }
    }

    /*
     * Remove one or more rows in the board. Each cell keeps his physical position
     * The same than adding rows. We remove instead.
     */

    public function removeRows($columns, $rows, $idBoard, $rowsToSub) {
        $currentPos = $columns * $rows;
        for ($row = 0; $row < $rowsToSub; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $cell = $this->BoardInterface->getIDCell($currentPos, $idBoard);
                $this->BoardInterface->removeCell($cell[0]->ID_RCell, $idBoard);
                $currentPos--;
            }
        }
    }

    /*
     * Add the clicked word (pictogram) in the S_Temp database table.
     * Then, get the entire sentence from this table.
     */

    public function addWord_post() {
        //To get the parameters
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;

        $this->Lexicon->afegirParaula(1, $id, null);

        $data = $this->Lexicon->recuperarFrase(1);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Remove the last word (pictogram) added in the S_Temp database table.
     * Then, get the entire sentence from this table.
     */

    public function deleteLastWord_post() {

        $id = $this->BoardInterface->getLastWord(1);

        $this->Lexicon->eliminarParaula($id->ID_RSTPSentencePicto);

        $data = $this->Lexicon->recuperarFrase(1);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Remove the entire phrase (pictograms) in the S_Temp database table.
     */

    public function deleteAllWords_post() {

        //1 es usuario
        $this->BoardInterface->removeSentence(1);

        $data = $this->Lexicon->recuperarFrase(1);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Copy the S_Temp table to the S_Historic table and all this dependecies. 
     * Also remove the entire phrase (pictograms) in the S_Temp database table.
     */

    public function generate_post() {
        //1 user
        $this->BoardInterface->initTrans();
        $paraules = $this->Lexicon->recuperarFrase(1);
        $this->BoardInterface->addStatsX1($paraules, 1);
        $this->BoardInterface->addStatsX2($paraules, 1);
        $this->BoardInterface->addStatsX3($paraules, 1);

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $tense = $request->tense;
        $tipusfrase = $request->tipusfrase;
        $negativa = $request->negativa;
        $this->Lexicon->insertarFrase(1, $tipusfrase, $tense, $negativa);


        $this->BoardInterface->commitTrans();

        if ($this->BoardInterface->statusTrans() === FALSE) {
            $response = [
                'error' => "errorText"
            ];
            $this->response($response, 500);
        } else {
            $expander = new Myexpander();
            $expander->expand();
            $info = $expander->info;
//            if ($info['frasefinal'] == ""){
//                $response = null;
//            }else{
            $response = [
                'info' => $info
            ];
//            }
            //redirect(base_url().'resultatsBoard', 'location');
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    /*
     * Get the functions in a list to create the dropdown menu
     */

    public function getFunctions_post() {

        $functions = $this->BoardInterface->getFunctions();

        $response = [
            'functions' => $functions
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Get the function
     */

    public function getFunction_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $tense = $request->tense;
        $tipusfrase = $request->tipusfrase;
        $negativa = $request->negativa;

        $control = "";
        $function = $this->BoardInterface->getFunction($id);
        $value = $function[0]->functValue;
        $type = $function[0]->functType;

        switch ($type) {
            case "modif":
                $this->BoardInterface->afegirModifNom($value);
                break;
            case "tense":
                $tense = $value;
                break;
            case "tipusfrase":
                $tipusfrase = $value;
                break;
            case "negativa":
                $negativa = $value;
                break;
            case "control":
                $control = $value;
                break;
        }
        $response = [
            'tense' => $tense,
            'tipusfrase' => $tipusfrase,
            'negativa' => $negativa,
            'control' => $control
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Add the selected pictogram to the board 
     */

    public function addPicto_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $pos = $request->pos;
        $idboard = $request->idboard;
        //$boardid = $request->boardid;
        //MODIF: 1 es la board
        $cell = $this->BoardInterface->getIDCell($pos, $idboard);
        $this->BoardInterface->updateDataCell($id, $cell[0]->ID_RCell);

        $data = $this->BoardInterface->getCellsBoard($idboard);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Swap the two selected pictograms in the board
     */

    public function swapPicto_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $pos1 = $request->pos1;
        $pos2 = $request->pos2;
        $idboard = $request->idboard;
        //$boardid = $request->boardid;
        //1 es la board
        $this->BoardInterface->updatePosCell($pos1, -1, $idboard);
        $this->BoardInterface->updatePosCell($pos2, $pos1, $idboard);
        $this->BoardInterface->updatePosCell(-1, $pos2, $idboard);

        $data = $this->BoardInterface->getCellsBoard($idboard);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Remove the selected pictogram to the board
     */

    public function removePicto_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $pos = $request->pos;
        $idboard = $request->idboard;
        //$boardid = $request->boardid;
        //1 es la board
        $cell = $this->BoardInterface->getIDCell($pos, $idboard);
        $this->BoardInterface->updateDataCell(NULL, $cell[0]->ID_RCell);

        $data = $this->BoardInterface->getCellsBoard($idboard);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
