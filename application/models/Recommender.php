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
    
    private function getTypesElem($pictoid){
        $output = array();
        $output = null;
        
        $this->db->select('pictoType');
        $this->db->from('pictograms');
        $this->db->where('pictoid', $pictoid);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getSubj() {     
        $output = array();
        $output = null;
        
        $subjList = array("jo", "yo", "tu");
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('pictogramslanguage');
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left');                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));
        $this->db->where_in('pictogramslanguage.pictotext', $subjList);
        $this->db->limit(5); 
        $query = $this->db->get();     
                
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }        
        return $output;
    }
    
    private function getfreqIdiomaType($pictoType) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('pictogramslanguage');
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left');                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));
        $this->db->where('pictograms.pictoType', $pictoType);               
        $this->db->limit(5);
        $this->db->order_by('pictogramslanguage.pictofreq', 'desc');   
        $query = $this->db->get();   
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;
    } 
    
    private function getfreqIdioma() {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('pictogramslanguage');
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left');                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));
        $this->db->limit(5);
        $this->db->order_by('pictogramslanguage.pictofreq', 'desc');   
        $query = $this->db->get();   
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;
    }    

    private function getfreqUsuariX2($inputid1) {                            
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');              
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                   
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->limit(5);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }    
    
    private function getDbSearchX2($inputid1, $fits) {
        $output = array();
        $output = null;
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->join('nameclass'.$this->session->userdata('ulangabbr'), 'p_statsuserpictox2.picto2id = nameclass'.$this->session->userdata('ulangabbr').'.nameid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->where_in('nameclass'.$this->session->userdata('ulangabbr').'.class', $fits);
        $this->db->limit(5);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getContext() {                            
        $output = array();
        $output = null;
        
        $date = array(date("Y-m-d"), date("Y-m-d", strtotime("yesterday")));     
        
        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        //$this->db->where('s_historic.sentenceDate', '2016-04-20');               
        $this->db->where_in('s_historic.sentenceDate', $date);
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->limit(5);
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }  
    
    private function getContextType($pictoType) {                            
        $output = array();
        $output = null;
        
        $date = array(date("Y-m-d"), date("Y-m-d", strtotime("yesterday")));
        
        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        //$this->db->where('s_historic.sentenceDate', '2016-04-20');               
        $this->db->where_in('s_historic.sentenceDate', $date);
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('pictograms.pictoType', $pictoType);               
        $this->db->limit(5);
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }   
   
    private function getMMFits($tipus, $case){      
        $output = array();
        $output = null;
        $caseTipus = $case."tipus";
        // puede haber locfrom opt sin locfromtipus
        if($tipus[0]->$caseTipus != null) {   
            $matching = new Mymatching();
            $key = $matching->nounsFitKeys[$tipus[0]->$caseTipus];        
            $keyw = array_keys($matching->nounsFit[$key], 0);
            for ($i = 0; $i < sizeof($keyw); $i++) {
                $output[] = array_keys($matching->nounsFitKeys, $keyw[$i])[0];
            }
        }
        return $output;
    }
        
    private function getCaseTipus($picto1id, $case, $b) {
        $output = array();
        $output = null;
        $this->db->select($case.'tipus');    
        $this->db->from('pattern'.$this->session->userdata('ulangabbr'));        
        $this->db->where('verbid', $picto1id);
        $this->db->where($case, $b);     
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;                   
    }
    
    private function getFits($inputid1, $case) {
        $fits = null;
        $tipus = $this->getCaseTipus($inputid1, $case, 1);        
        $caseTipus = $case."tipus";
        if ($tipus != null && $tipus[0]->$caseTipus != 'adj' && $tipus[0]->$caseTipus != 'adv' && $tipus[0]->$caseTipus != 'modif' && $tipus[0]->$caseTipus != 'verb') {
            $fits = $this->getMMFits($tipus, $case);
        }
        else if ($tipus == null) {
            $tipus = $this->getCaseTipus($inputid1, $case, 'opt');
            if ($tipus != null && $tipus[0]->$caseTipus != 'adj' && $tipus[0]->$caseTipus != 'adv' && $tipus[0]->$caseTipus != 'modif' && $tipus[0]->$caseTipus != 'verb') {
                $fits = $this->getMMFits($tipus, $case); 
            }
        }
        else if ($tipus != null && $tipus[0]->$caseTipus == 'verb') {
            
        }
        return $fits;
    }    
    
    private function getfreqUsuariX1() {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpicto');
        $this->db->join('pictogramslanguage', 'p_statsuserpicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpicto.ID_PSUPUser', $this->session->userdata('idusu'));                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->limit(5);
        $this->db->order_by('countx1', 'desc');        
        $query = $this->db->get();     
                
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }        
        return $output;
    }
    
    private function getfreqUsuariX3($inputid1, $inputid2) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left');
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                              
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
    
    function getDbSearchX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, p_statsuserpictox3.picto3id as `pictoid`, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('nameclass'.$this->session->userdata('ulangabbr'), 'p_statsuserpictox3.picto3id = nameclass'.$this->session->userdata('ulangabbr').'.nameid', 'left'); 
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
        $this->db->where_in('nameclass'.$this->session->userdata('ulangabbr').'.class', $fits);
        $this->db->limit(5);
        $this->db->order_by('countx3', 'desc');                                            
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
        
        $freqIdioma = $this->getfreqIdioma();
        //return $freqIdioma; // Algorisme V1 - Predictor freqüència I (de llenguatge)
        
        $freqUsuari = $this->getfreqUsuariX1();
        return $freqUsuari; // Algorisme V2 - Predictor freqüència II (d'usuari)   
                
        $subjs = $this->getSubj();
        //return $subjs; // Algorisme V5 - Predictor inicial (cas 00 no hi ha res (fix jo i tu))
        
        $verbs = $this->getfreqIdiomaType('verb');
        //return $verbs; // Algorisme V5 - Predictor inicial (verbs)                                                        
        
        $context = $this->getContext();
        //return $context; // Algorisme V6 - Predictor de context (total)
        
        $contextTypeName = $this->getContextType('name');
        //return $contextTypeName; // Algorisme V6 - Predictor de context (name)
        
        $contextTypeVerb = $this->getContextType('verb');
        //return $contextTypeVerb; // Algorisme V6 - Predictor de context (verb)
        
        return null;
    }
        
    function getRecommenderX2() {
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;        
        $inputType = $this->getTypesElem($inputid1);
        
        if ($inputType[0]->pictoType == 'verb') {            
                        
            $case = "theme";
            $fits = $this->getFits($inputid1, $case);
            $res = $this->getDbSearchX2($inputid1, $fits);
            return $res; // Algorismes V3 i V4 - Predictor verbs I i II 
            // (case: theme, manera, locto, locfrom)
            // cuando $fits es false, getDbSearchX2() se comporta como getfreqUsuariX2()            
        }
        else if ($inputType[0]->pictoType != 'verb' && $inputType[0]->pictoType != 'name') {
            
            $subjs = $this->getSubj();
            //return $subjs; // Algorisme V5 - Predictor inicial (cas 00 no hi ha res (fix jo i tu))   
            
            $contextTypeName = $this->getContextType('name');
            //return $contextTypeName; // Algorisme V6 - Predictor de context (name)
            
            $verbs = $this->getfreqIdiomaType('verb');
            //return $verbs; // Algorisme V5 - Predictor inicial (verbs) 
            
            $contextTypeVerb = $this->getContextType('verb');
            //return $contextTypeVerb; // Algorisme V6 - Predictor de context (verb)                        
        }
                    
        $freqIdioma = $this->getfreqIdioma();
        //return $freqIdioma; // Algorisme V1 - Predictor freqüència I (de llenguatge)
        
        $freqUsuari = $this->getfreqUsuariX2($inputid1);
        return $freqUsuari; // Algorisme V2 - Predictor freqüència II (d'usuari)        
              
        $context = $this->getContext();
        //return $context; // Algorisme V6 - Predictor de context (total)
                
        return null;
    }   
   
    function getRecommenderX3() {   
        $paraulesFrase = $this->getIdsElem();        
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-2]->pictoid;
        $inputid2 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
        
        //$verbs = $this->getfreqIdiomaType('verb');
        //return $verbs; // Algorisme V5 - Predictor inicial (verbs)                
                    
        //$freqIdioma = $this->getfreqIdioma();
        //return $freqIdioma; // Algorisme V1 - Predictor freqüència I (de llenguatge)
        
        $freqUsuari = $this->getfreqUsuariX3($inputid1, $inputid2);
        return $freqUsuari; // Algorisme V2 - Predictor freqüència II (d'usuari)        
                
//        $case = "theme";
//        $fits = $this->getFits($inputid1, $case);
//        $res = $this->getDbSearchX3($inputid1, $inputid2, $fits);
//        return $res; // Algorismes V3 i V4 - Predictor verbs I i II 
        // (case: theme, manera, locto, locfrom)
        // cuando $fits es false, getDbSearchX3() se comporta como getfreqUsuariX3()
        
        $context = $this->getContext();
        //return $context; // Algorisme V6 - Predictor de context (total)
        
        $contextType = $this->getContextType('verb');
        //return $contextType; // Algorisme V6 - Predictor de context (verb || name)
        
        return null;                                         
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
