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

        $this->db->where('ID_Board', $id);
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
     * Change the data of one pictogram ($cell) from the board ($idpicto)
     */
    function updateDataCell($idpicto, $cell) {
        $output = array();

        $this->db->where('ID_Cell', $cell);
        $this->db->update('Cell', array('ID_CPicto' => $idpicto));


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
     * Inserts individually each pictogram in P_StatsUserPicto.
     * If this picto already exists increment count
     */

    function addStatsX1($paraulesFrase, $iduser) {
        for ($i = 0; $i < count($paraulesFrase); $i++) {
            if ($paraulesFrase[$i] != null) {//esto se podria quitar...
                $word = $paraulesFrase[$i];
                $inputid = $word->id;
                $this->db->where('pictoid', $inputid);
                $this->db->where('ID_PSUPUser', $iduser);
                $query = $this->db->get('P_StatsUserPicto');
                if ($query->num_rows() > 0) {
                    $stat = $query->result();
                    $num = $stat[0]->countx1 + 1;

                    $this->db->where('pictoid', $inputid);
                    $this->db->where('ID_PSUPUser', $iduser);
                    $data = array(
                        'countx1' => $num
                    );
                    $query = $this->db->update('P_StatsUserPicto', $data);
                } else {
                    $data = array(
                        'countx1' => '1',
                        'pictoid' => $inputid,
                        'ID_PSUPUser' => $iduser
                    );
                    $query = $this->db->insert('P_StatsUserPicto', $data);
                }
            }
        }
    }

    /*
     * Inserts, in pairs, each pictogram in P_StatsUserPicto.
     * If this combination of pictograms already exist increment count
     */

    function addStatsX2($paraulesFrase, $iduser) {
        for ($i = 1; $i < count($paraulesFrase); $i++) {
            $word1 = $paraulesFrase[$i - 1];
            $word2 = $paraulesFrase[$i];
            $inputid1 = $word1->id;
            $inputid2 = $word2->id;
            $this->db->where('picto1id', $inputid1);
            $this->db->where('picto2id', $inputid2);
            $this->db->where('ID_PSUP2User', $iduser);
            $query = $this->db->get('P_StatsUserPictox2');
            if ($query->num_rows() > 0) {
                $stat = $query->result();
                $num = $stat[0]->countx2 + 1;

                $this->db->where('picto2id', $inputid2);
                $this->db->where('picto1id', $inputid1);
                $this->db->where('ID_PSUP2User', $iduser);
                $data = array(
                    'countx2' => $num
                );
                $query = $this->db->update('P_StatsUserPictox2', $data);
            } else {
                $data = array(
                    'countx2' => '1',
                    'picto2id' => $inputid2,
                    'picto1id' => $inputid1,
                    'ID_PSUP2User' => $iduser
                );
                $query = $this->db->insert('P_StatsUserPictox2', $data);
            }
        }
    }

    /*
     * Inserts, in t, each pictogram in P_StatsUserPicto.
     * If this combination of pictograms already exist increment count
     */

    function addStatsX3($paraulesFrase, $iduser) {
        for ($i = 2; $i < count($paraulesFrase); $i++) {
            $word1 = $paraulesFrase[$i - 2];
            $word2 = $paraulesFrase[$i - 1];
            $word3 = $paraulesFrase[$i];
            $inputid1 = $word1->id;
            $inputid2 = $word2->id;
            $inputid3 = $word3->id;
            $this->db->where('picto1id', $inputid1);
            $this->db->where('picto2id', $inputid2);
            $this->db->where('picto3id', $inputid3);
            $this->db->where('ID_PSUP3User', $iduser);
            $query = $this->db->get('P_StatsUserPictox3');
            if ($query->num_rows() > 0) {
                $stat = $query->result();
                $num = $stat[0]->countx3 + 1;

                $this->db->where('picto3id', $inputid3);
                $this->db->where('picto2id', $inputid2);
                $this->db->where('picto1id', $inputid1);
                $this->db->where('ID_PSUP3User', $iduser);
                $data = array(
                    'countx3' => $num
                );
                $query = $this->db->update('P_StatsUserPictox3', $data);
            } else {
                $data = array(
                    'countx3' => '1',
                    'picto3id' => $inputid3,
                    'picto2id' => $inputid2,
                    'picto1id' => $inputid1,
                    'ID_PSUP3User' => $iduser
                );
                $query = $this->db->insert('P_StatsUserPictox3', $data);
            }
        }
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

}
