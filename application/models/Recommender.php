<?php

class Recommender extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
        $this->load->library('Myword');
        $this->load->library('Mymatching');
    }
    
    private function getIdsElem(){
        $output = array();
        $output = null;
        
        $this->db->select('pictoid');
        $this->db->from('r_s_temppictograms');
        $this->db->where('ID_RSTPUser', $this->session->userdata('idusu'));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }   
 
    private function getTheme1OptFits($theme){      
        $output = array();
        
        $matching = new Mymatching();
        $key = $matching->nounsFitKeys[$theme[0]->themetipus];
                
        for ($i = 0; $i <= 24; $i++) { // sizeof($matching->nounsFit)
            if($matching->nounsFit[$key][$i] == 0){
                $keyw = array_search($i, $matching->nounsFitKeys);
                array_push($output, $keyw);
            }
        }
        return $output;
    }
        
    private function getTheme1Opt($picto1id, $theme1Opt) {
        $output = array();
        $output = null;
        $this->db->select('themetipus');
        $this->db->from('pattern'.$this->session->userdata('ulangabbr'));        
        $this->db->where('verbid', $picto1id);    
        $this->db->where('theme', $theme1Opt);    
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }                
        return $output;                   
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

    function getRecommenderX1() {     
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpicto');
        $this->db->join('pictogramslanguage', 'p_statsuserpicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpicto.ID_PSUPUser', $this->session->userdata('idusu'));                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulangid'));                             
        $this->db->limit(5);
        $this->db->order_by('countx1', 'desc');        
        $query = $this->db->get();     
                
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }        
        return $output;
    }
        
//    function getRecommenderX2() {   CON FITS
//        $paraulesFrase = $this->getIdsElem();
//        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
//        $theme1 = $this->getTheme1Opt($inputid1, 1);       
//        $themeOpt = $this->getTheme1Opt($inputid1, opt);       
//        
//        $fits1 = null;
//        if ($theme1 != null && $theme1[0]->themetipus != 'verb') {
//            $fits1 = $this->getTheme1OptFits($theme1);
//        }
//        $fitsOpt = null;
//        if ($themeOpt != null && $themeOpt[0]->themetipus != 'verb') {
//            $fits1 = $this->getTheme1OptFits($themeOpt);
//        }
//        //else if ($theme[0]->themetipus == 'verb')
//                         
//        $output = array();
//        $output = null;
//        
//        $this->db->select('p_statsuserpictox2.picto2id as `pictoid`, pictogramslanguage.pictotext');
//        $this->db->from('p_statsuserpictox2');       
//        $this->db->join('nameclassca', 'p_statsuserpictox2.picto2id = nameclassca.nameid', 'left'); 
//        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left'); 
//        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));               
//        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulangid'));                             
//        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
//        $this->db->where_in('nameclassca.class', $fits1);
//        $this->db->limit(5);
//        $this->db->order_by('countx2', 'desc');        
//        $query = $this->db->get();
//        
//        if ($query->num_rows() > 0) {
//            $output = $query->result();
//        }
//        return $output;   
//    }
    
    function getRecommenderX2() {   
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
                         
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');              
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulangid'));                                                   
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->limit(5);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }

//    function getRecommenderX3($paraulesFrase) {   CON FITS
//        $inputid1 = $paraulesFrase[0]->id;
//        $inputid2 = $paraulesFrase[1]->id;
//        
//        $theme1Opt = 1; // = opt
//        $theme = $this->getTheme1Opt($inputid2, $theme1Opt);       
//        
//        $fits = null;
//        if ($theme != null && $theme[0]->themetipus != 'verb') {
//            $fits = $this->getTheme1OptFits($theme);
//        }
//        //else if ($theme[0]->themetipus == 'verb')
//                         
//        $output = array();
//        $output = null;
//        
//        $this->db->select('p_statsuserpictox3.picto3id as `pictoid`, pictogramslanguage.pictotext');
//        $this->db->from('p_statsuserpictox3');       
//        $this->db->join('nameclassca', 'p_statsuserpictox3.picto3id = nameclassca.nameid', 'left'); 
//        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left'); 
//        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));               
//        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulangid'));                             
//        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
//        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
//        $this->db->where_in('nameclassca.class', $fits);
//        $this->db->limit(5);
//        $this->db->order_by('countx3', 'desc');        
//        $query = $this->db->get();
//        
//        if ($query->num_rows() > 0) {
//            $output = $query->result();
//        }
//        return $output;   
//    }
    
    function getRecommenderX3() {   
        $paraulesFrase = $this->getIdsElem();        
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-2]->pictoid;
        $inputid2 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
                         
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left');
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulangid'));                                              
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
        $this->db->limit(5);
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }
    
    function getcountElem(){
        $output = 0;
        $this->db->where('ID_RSTPUser', $this->session->userdata('idusu'));        
        $query = $this->db->get('r_s_temppictograms');
        
        if ($query->num_rows() > 0) {
            $output = $query->num_rows();
        }
        return $output; 
    }       
}

?>
