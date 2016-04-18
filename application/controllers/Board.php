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
        $this->load->library('Myprediction');
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
        $luid = $request->lusuid;

        $data = array(
            'idusu' => $iu, // Id user
            'ulangabbr' => $lu, // ES, CA...
            'ulangid' => $luid // Id language
                );

        $this->session->set_userdata($data);
    }

    /*
     * Get the cell's info
     */

    public function getCell_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $pos = $request->pos;
        $idboard = $request->idboard;

        $info = $this->BoardInterface->getCell($pos, $idboard);


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

        $output = $this->BoardInterface->getBoardStruct($idboard);
        $columns = $output[0]->width;
        $rows = $output[0]->height;
        $name = $output[0]->Bname;
        $primaryBoard = $output[0]->primaryBoard;
        $autoReturn = $output[0]->autoReturn;


        $response = [
            'col' => $columns,
            'row' => $rows,
            'name' => $name,
            'primaryBoard' => $primaryBoard,
            'autoReturn' => $autoReturn,
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function showCellboard_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idboard = $request->idboard;

        $array = array();

        $output = $this->BoardInterface->getBoardStruct($idboard);
        if ($output != null) {
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
    }

    public function modifyNameboard_post() {
        $this->BoardInterface->initTrans();

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $Name = $request->Name;
        $IDnumboard = $request->ID;

        $this->BoardInterface->updateName($Name, $IDnumboard);
        $this->BoardInterface->commitTrans();
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

        $this->BoardInterface->commitTrans();
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

        $idusu = $this->session->userdata('idusu');
        $this->Lexicon->afegirParaula($idusu, $id, null);

        $data = $this->Lexicon->recuperarFrase($idusu);

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

        $idusu = $this->session->userdata('idusu');
        $id = $this->BoardInterface->getLastWord($idusu);

        $this->Lexicon->eliminarParaula($id->ID_RSTPSentencePicto);

        $data = $this->Lexicon->recuperarFrase($idusu);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * Remove the entire phrase (pictograms) in the S_Temp database table.
     */

    public function deleteAllWords_post() {

        $idusu = $this->session->userdata('idusu');
        $this->BoardInterface->removeSentence($idusu);

        $data = $this->Lexicon->recuperarFrase($idusu);

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

        $this->BoardInterface->initTrans();

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $tense = $request->tense;
        $tipusfrase = $request->tipusfrase;
        $negativa = $request->negativa;
        $idusu = $this->session->userdata('idusu');
        $this->Lexicon->insertarFrase($idusu, $tipusfrase, $tense, $negativa);


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
     * Get the primary user board (the primary board in her/his primary group board)
     */
    public function getPrimaryUserBoard_post() {

        $board = $this->BoardInterface->getPrimaryGroupBoard();
        $primaryBoard = $this->BoardInterface->getPrimaryBoard($board[0]->ID_GB);

        $response = [
            'idboard' => $primaryBoard[0]->ID_Board
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    /*
     * Get the user boards in a list to create the dropdown menu
     */

    public function getBoards_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idboard = $request->idboard;

        $board = $this->BoardInterface->getIDGroupBoards($idboard);
        $boards = $this->BoardInterface->getBoards($board[0]->ID_GBBoard);
        $primaryBoard = $this->BoardInterface->getPrimaryBoard($board[0]->ID_GBBoard);

        $response = [
            'boards' => $boards,
            'primaryBoard' => $primaryBoard[0]
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
        $idusu = $this->session->userdata('idusu');
        $data = $this->Lexicon->recuperarFrase($idusu);
        
        $response = [
            'tense' => $tense,
            'tipusfrase' => $tipusfrase,
            'negativa' => $negativa,
            'control' => $control,
            'data' => $data
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
        $cell = $this->BoardInterface->getIDCell($pos, $idboard);
        $this->BoardInterface->removeDataCell($cell[0]->ID_RCell);

        $data = $this->BoardInterface->getCellsBoard($idboard);

        $response = [
            'data' => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * 
     */

    public function searchSentence_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $search = $request->search;

        $idusu = $this->session->userdata('idusu');
        $sentence = $this->BoardInterface->getSentences($idusu, $search);

        $response = [
            'sentence' => $sentence
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getSentence_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;

        $sentence = $this->BoardInterface->getSentence($id);

        $response = [
            'sentence' => $sentence
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /*
     * 
     */

    public function searchSFolder_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $search = $request->search;

        $idusu = $this->session->userdata('idusu');
        $sFolder = $this->BoardInterface->getSFolders($idusu, $search);

        $response = [
            'sfolder' => $sFolder
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getSFolder_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;

        $sFolder = $this->BoardInterface->getSFolder($id);

        $response = [
            'sFolder' => $sFolder
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function editCell_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $boardLink = $request->boardLink;
        $idFunct = $request->idFunct;
        $textInCell = $request->textInCell;
        $visible = $request->visible;
        $isFixed = $request->isFixed;
        $idPicto = $request->idPicto;
        $idSentence = $request->idSentence;
        $idSFolder = $request->idSFolder;
        $numScanBlockText1 = $request->numScanBlockText1;
        $textInScanBlockText1 = $request->textInScanBlockText1;
        $numScanBlockText2 = $request->numScanBlockText2;
        $textInScanBlockText2 = $request->textInScanBlockText2;
        $cellType = $request->cellType;
        $color = $request->color;

        $this->BoardInterface->updateMetaCell($id, $visible, $textInCell, $isFixed, $idFunct, $boardLink, $idPicto, $idSentence, $idSFolder, $cellType, $color);
        $this->BoardInterface->updateScanCell($id, $numScanBlockText1, $textInScanBlockText1, $numScanBlockText2, $textInScanBlockText2);
    }

    public function changePrimaryBoard_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $idBoard = $request->idBoard;

        $this->BoardInterface->changePrimaryBoard($id, $idBoard);
    }

    public function changeAutoReturn_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $value = ($request->value == true ? '1' : '0');
        $id = $request->id;


        $this->BoardInterface->changeAutoReturn($id, $value);
    }

    public function changeAutoReadSentence_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $value = ($request->value == true ? '1' : '0');
        $id = $request->id;


        $this->BoardInterface->changeAutoReadSentence($id, $value);
    }

    public function autoReturn_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;


        $board = $this->BoardInterface->getBoardStruct($id);

        $idPrimaryBoard = null;

        if ($board[0]->autoReturn === "1") {
            $primaryBoard = $this->BoardInterface->getPrimaryBoard($board[0]->ID_GBBoard);
            $idPrimaryBoard = $primaryBoard[0]->ID_Board;
        }

        $response = [
            'idPrimaryBoard' => $idPrimaryBoard
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function autoReadSentence_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;


        $board = $this->BoardInterface->getBoardStruct($id);

        $idPrimaryBoard = null;

        if ($board[0]->autoReadSentence === "1") {
            $primaryBoard = $this->BoardInterface->getPrimaryBoard($board[0]->ID_GBBoard);
            $idPrimaryBoard = $primaryBoard[0]->ID_Board;
        }

        $response = [
            'idPrimaryBoard' => $idPrimaryBoard
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function newBoard_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $IDGboard = $request->idGroupBoard;
        $name = $request->CreateBoardName;
        $width = $request->width;
        $height = $request->height;

        $idBoard = $this->BoardInterface->createBoard($IDGboard, $name, $width, $height);
        $this->addColumns(0, 0, $idBoard, $width);
        $this->addRows($width, 0, $idBoard, $height);
        $this->BoardInterface->commitTrans();
        $response = [
            'idBoard' => $idBoard
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function removeBoard_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;


        $cell = $this->BoardInterface->getCellsBoard($id);
        for ($i = 0; $i < count($cell); $i++) {
            $this->BoardInterface->removeCell($cell[$i]->ID_RCell, $id);
        }
        $this->BoardInterface->removeBoardLinks($id);

        $this->BoardInterface->removeBoard($id);
        $this->BoardInterface->commitTrans();
    }

    public function getIDGroupBoards_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;

        $idBoard = $this->BoardInterface->getIDGroupBoards($id);
        $response = [
            'idGroupBoard' => $idBoard[0]->ID_GBBoard
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getMaxScanBlock1_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->idboard;

        $max = $this->BoardInterface->getMaxScanBlock1($id);
        $response = [
            'max' => $max[0]->customScanBlock1
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getMaxScanBlock2_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->idboard;
        $scanGroup = $request->scanGroup;

        $max = $this->BoardInterface->getMaxScanBlock2($id, $scanGroup);
        if ($max != null) {
            $response = [
                'max' => $max[0]->customScanBlock2
            ];
        }else{
            $response = [
                'max' => "No group found"
            ];
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getScannedCells_post() {
        $this->BoardInterface->initTrans();
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->idboard;
        $csb1 = $request->numCustomScanBlock1;
        $csb2 = $request->numCustomScanBlock2;

        $array = $this->BoardInterface->getScannedCells($id, $csb1, $csb2);
        $response = [
            'array' => $array
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function getAudioSentence_post() {
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $sentence = $request->sentence;
        $voice = $request->voice;

        
        $md5 = MD5(strval($voice) . $sentence);
        $array = $this->BoardInterface->getAudioSentence($md5);
        if ($array != null) {
            $response = [
                'data' => $array[0]->mp3Path
            ];
        }else{
            $response = [
                //MODIF: NO ESTA EL MP3, DESCARREGAR MP3 VOCALWARE
                'data' => MD5(strval($voice) . $sentence)
            ];
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function getPrediction_post() {
        // CARGA recommenderArray                 
        $prediction = new Myprediction();  
        $recommenderArray = $prediction->getPrediction(); 
        
        $response = [ 'recommenderArray' => $recommenderArray ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
