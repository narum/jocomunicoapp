<?php

class BoardInterface extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }

    /*
     * Load the user config
     */

    function loadCFG($user) {

        $newdata = array(
            'cfguser' => 1,
            'cfgExpansionOnOff' => 1,
            'cfgPredOnOff' => 1,
            'cfgPredBarVertHor' => 0,
            'cfgSentenceBarUpDown' => 1
        );

        $this->session->set_userdata($newdata);
    }

    /*
     * Get the board struct (columns, rows, name...) 
     */

    function getBoardStruct($id) {
        $output = array();

        $idusu = $this->session->userdata('idusu');
        $this->db->where('ID_GBUser', $idusu);
        $this->db->where('ID_Board', $id);
        $this->db->join('GroupBoards', 'GroupBoards.ID_GB = Boards.ID_GBBoard');
        $query = $this->db->get('Boards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Change the board struct (columns and rows) 
     */

    function updateNumCR($c, $r, $id) {
        $output = array();

        $this->db->where('ID_Board', $id);
        $query = $this->db->update('Boards', array('width' => $c, 'height' => $r));

        return $output;
    }

    function updateName($Name, $id) {
        $output = array();

        $this->db->where('ID_Board', $id);
        $query = $this->db->update('Boards', array('Bname' => $Name));

        return $output;
    }

    /*
     * Return all pictograms from board 
     */

    function getCellsBoard($id) {
        $output = array();

        $this->db->where('R_BoardCell.ID_RBoard', $id);
        $this->db->order_by('R_BoardCell.posInBoard', 'asc');
        $this->db->join('Cell', 'R_BoardCell.ID_RCell = Cell.ID_Cell');
        //Este tiene que ser left, si pictograms.picto id = null significa que esta vacia
        $this->db->join('Pictograms', 'Cell.ID_CPicto = Pictograms.pictoid', 'left');

        $query = $this->db->get('R_BoardCell');
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Return one pictogram from the board with the given position in this board 
     */

    function getCell($pos, $idboard) {
        $output = array();

        $this->db->where('R_BoardCell.ID_RBoard', $idboard);
        $this->db->where('R_BoardCell.posInBoard', $pos);
        $this->db->join('Cell', 'R_BoardCell.ID_RCell = Cell.ID_Cell');
        //Este tiene que ser left, si pictograms.picto id = null significa que esta vacia
        $this->db->join('Pictograms', 'Cell.ID_CPicto = Pictograms.pictoid', 'left');

        $query = $this->db->get('R_BoardCell');
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Change one pictogram from the board to another position 
     */

    function updatePosCell($oldPos, $newPos, $idBoard) {
        $output = array();

        $this->db->where('posInBoard', $oldPos);
        $this->db->where('ID_RBoard', $idBoard);
        $this->db->update('R_BoardCell', array('posInBoard' => $newPos));


        return $output;
    }

    /*
     * 
     */

    function updateMetaCell($id, $visible, $textInCell, $isFixed, $idFunc, $idboard, $idpicto, $idSentence, $idSFolder, $cellType) {
        $output = array();

        $data = array(
            'activeCell' => $visible,
            'textInCell' => $textInCell,
            'isFixedInGroupBoards' => $isFixed,
            'ID_CFunction' => $idFunc,
            'boardLink' => $idboard,
            'ID_CPicto' => $idpicto,
            'ID_CSentence' => $idSentence,
            'sentenceFolder' => $idSFolder,
            'cellType' => $cellType
        );
        $this->db->where('ID_Cell', $id);
        $this->db->update('Cell', $data);


        return $output;
    }

    /*
     * 
     */

    function updateScanCell($id, $num1, $text1, $num2, $text2) {
        $output = array();

        $data = array('customScanBlock1' => $num1,
            'customScanBlockText1' => $text1,
            'customScanBlock2' => $num2,
            'customScanBlockText2' => $text2);
        $this->db->where('ID_RCell', $id);
        $this->db->update('R_BoardCell', $data);


        return $output;
    }

    /*
     * 
     */

    function updatePictoCell($id, $idPicto) {
        $output = array();

        $this->db->where('ID_Cell', $id);
        $this->db->update('Cell', array('ID_CPicto' => $idPicto));


        return $output;
    }

    /*
     * Create a NULL cell (blank cell) in the position ($Pos) 
     * and add the cell to the board ($idBoard)
     */

    function newCell($Pos, $idBoard) {
        $output = array();

        $data = array(
            'ID_Cell' => 'NULL'
        );

        $this->db->insert('Cell', $data);

        $id = $this->db->insert_id();

        $data = array(
            'ID_RBoard' => $idBoard,
            'ID_RCell' => $id,
            'posInBoard' => $Pos
        );

        $this->db->insert('R_BoardCell', $data);
    }

    /*
     * Return the cell ID in position ($Pos) from the board ($idBoard)
     */

    function getIDCell($Pos, $idBoard) {
        $output = array();

        $this->db->where('posInBoard', $Pos);
        $this->db->where('ID_RBoard', $idBoard);
        $query = $this->db->get('R_BoardCell');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Change the data of one pictogram ($cell) from the board ($idpicto)    
     */

    function updateDataCell($idpicto, $cell) {
        $output = array();

        $this->db->where('ID_Cell', $cell);
        $this->db->update('Cell', array('ID_CPicto' => $idpicto));


        return $output;
    }

    /*
     * Remove the cell ($id) from the board ($idBoard). Remove the link too
     */

    function removeCell($id, $idBoard) {

        $this->db->where('ID_RBoard', $idBoard);
        $this->db->where('ID_RCell', $id);
        $this->db->delete('R_BoardCell');

        $this->db->where('ID_Cell', $id);
        $this->db->delete('Cell');
    }

    /*
     * Init a DB transaction 
     */

    function initTrans() {
        $this->db->trans_start();
    }

    /*
     * Ends a DB transaction. Commit change if nothing gone worng. Otherwise
     * makes a rollback 
     */

    function commitTrans() {
        $this->db->trans_complete();
    }

    /*
     * Return true if the last end transaction was a commit, else return false
     */

    function statusTrans() {
        return $this->db->trans_status();
    }

    /*
     * Return the last word added to the sentence
     */

    function getLastWord($idusu) {
        $output = array();

        $this->db->where('ID_RSTPUser', $idusu);
        $this->db->order_by('ID_RSTPSentencePicto', 'desc');

        $query = $this->db->get('R_S_TempPictograms');
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output[0];
    }

    /*
     * Remove the sentence from the tabla temp
     */

    function removeSentence($idusu) {
        $this->db->where('ID_RSTPUser', $idusu);
        $this->db->delete('R_S_TempPictograms');
    }

    /*
     * Return the function information
     */

    function getFunction($id) {

        $this->db->where('ID_Function', $id);
        $query = $this->db->get('Function');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Return all functions
     */

    function getFunctions() {


        $query = $this->db->get('Function');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Return all user boards in the same group
     */

    function getIDGroupBoards($idboard) {

        $this->db->where('ID_Board', $idboard);
        $query = $this->db->get('Boards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = "null";

        return $output;
    }

    /*
     * Return all user boards in the same group
     */

    function getBoards($idgroup) {

        $this->db->where('ID_GBBoard', $idgroup);
        $query = $this->db->get('Boards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }
    
    /*
     * Return primaryboard from a board group
     */

    function getPrimaryBoard($idgroup) {

        $this->db->where('primaryBoard', '1');
        $this->db->where('ID_GBBoard', $idgroup);
        $query = $this->db->get('Boards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * Return all user boards in the same group
     */

    function getAllBoards() {

        $idusu = $this->session->userdata('idusu');
        $this->db->where('ID_GBUser', $idusu);
        $this->db->join('GroupBoards', 'ID_GB = ID_GBBoard');
        $query = $this->db->get('Boards');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * 
     */

    function getSentences($idusu, $idsearch) {

        $this->db->like('generatorString', $idsearch);
        $this->db->where('ID_SSUser', $idusu);
        $query = $this->db->get('S_Sentence');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * 
     */

    function getSentence($id) {

        $this->db->where('ID_SSentence', $id);
        $query = $this->db->get('S_Sentence');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output[0];
    }

    /*
     * 
     */

    function getSFolders($idusu, $idsearch) {

        $this->db->like('folderName', $idsearch);
        $this->db->where('ID_SFUser', $idusu);
        $query = $this->db->get('S_Folder');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    /*
     * 
     */

    function getSFolder($id) {

        $this->db->where('ID_Folder', $id);
        $query = $this->db->get('S_Folder');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output[0];
    }

    /*
     * ADD MODIFIER TO A NOUN THAT WAS JUST ENTERED
     */

    function afegirModifNom($modif) {

        $idusu = $this->session->userdata('idusu');

        $this->db->where('ID_RSTPUser', $idusu);
        $query = $this->db->get('R_S_TempPictograms');

        if ($query->num_rows() > 0) {
            $aux = $query->result();
            $nrows = $query->num_rows();
            $identry = $aux[$nrows - 1]->ID_RSTPSentencePicto;

            if ($modif == 'pl') {
                $data = array(
                    'isplural' => '1',
                );
            }
            if ($modif == 'fem') {
                $data = array(
                    'isfem' => '1',
                );
            }
            if ($modif == 'i') {
                $data = array(
                    'coordinated' => '1',
                );
            }

            $this->db->where('ID_RSTPSentencePicto', $identry);
            $this->db->update('R_S_TempPictograms', $data);
        }
    }

    /*
     * ADD MODIFIER TO A NOUN THAT WAS JUST ENTERED
     */

    function changePrimaryBoard($id, $idboard) {
        $this->db->where('ID_GBBoard', $idboard);
        $this->db->update('Boards', array(
            'primaryBoard' => '0',
        ));

        $this->db->where('ID_Board', $id);
        $this->db->update('Boards', array(
            'primaryBoard' => '1',
        ));
    }

    function changeAutoReturn($id, $value) {
        $this->db->where('ID_Board', $id);
        $this->db->update('Boards', array(
            'autoReturn' => $value,
        ));
    }
    
    function getAutoReturn($id) {
        $this->db->where('ID_Board', $id);
        $this->db->get('Boards');
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output[0];
    }
   
    
}
