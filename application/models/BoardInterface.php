<?php

class BoardInterface extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }

    /*
     * GETS THE NOUNS OF THE TYPE $type FROM THE DATABASE
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

    function updateNumCR($c, $r, $id) {
        $output = array();

        $this->db->where('ID_Board', $id);
        $query = $this->db->update('Boards', array('width' => $c, 'height' => $r));

        return $output;
    }

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

    function updatePosCell($oldPos, $newPos, $idBoard) {
        $output = array();

        $this->db->where('posInBoard', $oldPos);
        $this->db->where('ID_RBoard', $idBoard);
        $this->db->update('R_BoardCell', array('posInBoard' => $newPos));


        return $output;
    }
    
    function updateDataCell($idpicto, $cell) {
        $output = array();

        $this->db->where('ID_Cell', $cell);
        $this->db->update('Cell', array('ID_CPicto' => $idpicto));


        return $output;
    }

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

    function removeCell($id, $idBoard) {

        $this->db->where('ID_RBoard', $idBoard);
        $this->db->where('ID_RCell', $id);
        $this->db->delete('R_BoardCell');

        $this->db->where('ID_Cell', $id);
        $this->db->delete('Cell');
    }

    //Hasta aqui
    function initTrans() {
        $this->db->trans_start();
    }

    function commitTrans() {
        $this->db->trans_complete();
    }

    function statusTrans() {
        return $this->db->trans_status();
    }

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
            if ($paraulesFrase[$i] != null) {
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
        for ($i = 0; $i < count($paraulesFrase); $i++) {
            if ($paraulesFrase[$i] != null && $paraulesFrase[$i + 1] != null) {
                $word1 = $paraulesFrase[$i];
                $word2 = $paraulesFrase[$i + 1];
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
    }

    /*
     * Inserts, in t, each pictogram in P_StatsUserPicto.
     * If this combination of pictograms already exist increment count
     */

    function addStatsX3($paraulesFrase, $iduser) {
        for ($i = 0; $i < count($paraulesFrase); $i++) {
            if ($paraulesFrase[$i] != null && $paraulesFrase[$i + 1] != null && $paraulesFrase[$i + 2] != null) {
                $word1 = $paraulesFrase[$i];
                $word2 = $paraulesFrase[$i + 1];
                $word3 = $paraulesFrase[$i + 2];
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
                    $this->db->where('ID_PSUP2User', $iduser);
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
    }
  
}
