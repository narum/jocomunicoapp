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
        $this->db->limit(3);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }    
    
    private function getfreqUsuariNameX2($inputid1, $fits) {
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
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getDbSearchQuant($pictoType) {
        $output = array();
        $output = null;           
        
        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $pictoType);               
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;        
    }    
    
    private function getfreqUsuariQuantX2($inputid1, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left');
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $fits);  
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariQuantX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left');
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto12d', $inputid2);  
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $fits);  
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariAdvManeraX2($inputid1, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left');
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $fits);  
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariAdvManeraX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left');
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $fits);  
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariAdjAdvX2($inputid1, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');              
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                   
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->where('pictograms.pictoType', $fits);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariAdjAdvX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');              
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox3.picto2id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                   
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto12d', $inputid2);  
        $this->db->where('pictograms.pictoType', $fits);
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariOrdinalX2($inputid1, $fits) {
        $output = array();
        $output = null;
                
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->join('adjclass'.$this->session->userdata('ulangabbr'), 'p_statsuserpictox2.picto2id = adjclass'.$this->session->userdata('ulangabbr').'.adjid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
        $this->db->where('adjclass'.$this->session->userdata('ulangabbr').'.class', $fits);
        $this->db->order_by('countx2', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getfreqUsuariNameX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left'); 
        $this->db->join('nameclass'.$this->session->userdata('ulangabbr'), 'p_statsuserpictox3.picto3id = nameclass'.$this->session->userdata('ulangabbr').'.nameid', 'left'); 
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
        $this->db->where_in('nameclass'.$this->session->userdata('ulangabbr').'.class', $fits);
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();        
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
        
    private function getfreqUsuariOrdinalX3($inputid1, $inputid2, $fits) {
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox3');       
        $this->db->join('pictogramslanguage', 'p_statsuserpictox3.picto3id = pictogramslanguage.pictoid', 'left');
        $this->db->join('pictograms', 'p_statsuserpictox3.picto3id = pictograms.pictoid', 'left'); 
        $this->db->join('adjclass'.$this->session->userdata('ulangabbr'), 'p_statsuserpictox3.picto3id = adjclass'.$this->session->userdata('ulangabbr').'.adjid', 'left'); 
        $this->db->where('p_statsuserpictox3.ID_PSUP3User', $this->session->userdata('idusu'));        
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                             
        $this->db->where('p_statsuserpictox3.picto1id', $inputid1);  
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
        $this->db->where('adjclass'.$this->session->userdata('ulangabbr').'.class', $fits);
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getDbSearchOrdinal($pictoType) {
        $output = array();
        $output = null;

        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('adjclass'.$this->session->userdata('ulangabbr'), 'pictogramslanguage.pictoid = adjclass'.$this->session->userdata('ulangabbr').'.adjid', 'left');
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('adjclass'.$this->session->userdata('ulangabbr').'.class', $pictoType);               
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getContextType2Days($pictoType) {                            
        $output = array();
        $output = null;
        $date = array(date("Y-m-d"), date("Y-m-d", strtotime("yesterday")));

        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        $this->db->where_in('s_historic.sentenceDate', $date);
        //$this->db->where('s_historic.sentenceDate', '2020-04-04');
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('pictograms.pictoType', $pictoType);               
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    } 
    
    private function getContextTypeAdvManeraAll($pictoType) {       
        $output = array();
        $output = null;

        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $pictoType);               
        $this->db->group_by('r_s_historicpictograms.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();                

        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    } 
    
    private function getContextTypeAll($pictoType) {                            
        $output = array();
        $output = null;

        $this->db->select('r_s_historicpictograms.pictoid, COUNT(r_s_historicpictograms.pictoid) as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('r_s_historicpictograms');              
        $this->db->join('s_historic', 'r_s_historicpictograms.ID_RSHPSentence = s_historic.ID_SHistoric', 'left'); 
        $this->db->join('pictogramslanguage', 'r_s_historicpictograms.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('s_historic.ID_SHUser', $this->session->userdata('idusu'));
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('pictograms.pictoType', $pictoType);               
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
        if ($tipus != null && $tipus[0]->$caseTipus != 'adj' && $tipus[0]->$caseTipus != 'adv' && $tipus[0]->$caseTipus != 'modif' && $tipus[0]->$caseTipus != 'quant' && $tipus[0]->$caseTipus != 'verb' && $tipus[0]->$caseTipus != 'ordinal') {
            $fits = $this->getMMFits($tipus, $case);            
        }
        else if ($tipus == null) {
            $tipus = $this->getCaseTipus($inputid1, $case, 'opt');
            if ($tipus != null && $tipus[0]->$caseTipus != 'adj' && $tipus[0]->$caseTipus != 'adv' && $tipus[0]->$caseTipus != 'modif' && $tipus[0]->$caseTipus != 'quant' && $tipus[0]->$caseTipus != 'verb' && $tipus[0]->$caseTipus != 'ordinal') {
                $fits = $this->getMMFits($tipus, $case); 
            }
        }
        if ($tipus != null && $tipus[0]->$caseTipus == 'verb') {
            $fits = 'verb';
        }
        else if ($tipus != null && $tipus[0]->$caseTipus == 'adv') {
            $fits = 'adv';
        }   
        else if ($tipus != null && $tipus[0]->$caseTipus == 'quant') {
            $fits = 'quant';
        }  
        else if ($tipus != null && $tipus[0]->$caseTipus == 'ordinal') {
            $fits = 'ordinal';
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
        $this->db->limit(3);
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }
    
    function getfreqUsuariFilterX3($inputid1, $inputid2, $fits) {
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
    
    private function insertFloorVF($VF, $Prediction, $FSize) {
        $k = 0;
        foreach($Prediction as $value) {
            if (sizeof($VF) == 0) {
                $VF = array();
                array_push($VF,$value);
                $FSize = 7;
            }
            for ($i = 0; $i < sizeof($VF); $i++) {
                if($k == floor($FSize/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                    $k++;
                }
            }
        }
        return $VF;
    }
    
    private function insertCeilVF($VF, $Prediction, $FSize) {
        $k = 0;
        foreach($Prediction as $value) {
            if (sizeof($VF) == 0) {
                $VF = array();
                array_push($VF,$value);
                $FSize = 7;
            }
            for ($i = 0; $i < sizeof($VF); $i++) {
                if($k == ceil($FSize/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                    $k++;
                }
            }
        }
        return $VF;
    }
    
    private function rellenaVFX2X3($VF, $Prediction, $TSize) {
        foreach($Prediction as $value) {
            if (sizeof($VF) == 0) {
                $VF = array();
                array_push($VF,$value);
            }
            for ($i = 0; sizeof($VF) < $TSize && $i < sizeof($VF); $i++) {
                if ($value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                }
            }
        }
        return $VF;
    }
    
    private function rellenaVFX1($VF, $Prediction, $TSize) {
        foreach($Prediction as $value) {
            for ($i = 0; sizeof($VF) < $TSize &&  $i < sizeof($VF); $i++) {
                if ($value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                }
            }
        }
        return $VF;
    }
    
    function getRecommenderX1() {
        //$this->session->userdata('cfgPredBarNumPred');
        $TSize = 7;

        // Algorisme V5 - Predictor inicial (cas 00 no hi ha res (fix jo i tu)  
        $VF = $this->getSubj();        

        // Algorisme V6 - Predictor de context (name) últims 2 dies
        $contextTypeName2Days = $this->getContextType2Days('name');
        $k = 0;
        foreach($contextTypeName2Days as $value) {
            for ($i = 0; $i < sizeof($VF); $i++) {
                if($k == ceil($TSize-2/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                    $k++;
                }
            }
        }

        // Algorisme V2 - Predictor freqüència II (d'usuari)                   
        $freqUsuari = $this->getfreqUsuariX1();
        $k = 0;
        foreach($freqUsuari as $value) {
            for ($i = 0; $i < sizeof($VF); $i++) {
                if($k == floor($TSize-2/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                    array_push($VF,$value);
                    $k++;
                }
            }
        }

        // rellena
        if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX1($VF, $contextTypeName2Days, $TSize);
        if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX1($VF, $freqUsuari, $TSize);           
 
        return $VF;
    }
        
    function getRecommenderX2() {
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;        
        $inputType = $this->getTypesElem($inputid1);

        // Algorisme V2 - Predictor freqüència II (d'usuari)
        $VF = array();
        $VF = array_merge($VF,$this->getfreqUsuariX2($inputid1));
        $TSize = 7;
        $FSize = $TSize - sizeof($VF);
        
        if ($inputType[0]->pictoType == 'name') {
            // Algorisme V6 - Predictor de context (verb) últims 2 dies
            $contextTypeVerb2Day = $this->getContextType2Days('verb');
            $VF = $this->insertFloorVF($VF, $contextTypeVerb2Day, $FSize);

            // Algorisme V6 - Predictor de context (verb) total
            $contextTypeVerbAll = $this->getContextTypeAll('verb');
            $VF = $this->insertCeilVF($VF, $contextTypeVerbAll, $FSize);
            
            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbAll, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerb2Day, $TSize);
        }
        else if ($inputType[0]->pictoType == 'verb') {           
            $caseList = array("theme", "manera", "locto", "locfrom");            
            foreach ($caseList as $case) {
                $fits = $this->getFits($inputid1, $case);        
                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdjAdvX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariOrdinalX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $getContextType2Days = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $getContextType2Days, $TSize);
                    }
                }
                else if ($case == "manera") {
                    if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariQuantX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits = 'adv') {  // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdvManeraX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX2($inputid1, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $getContextType2Days, $TSize);
                    }
                } 
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                    $res = $this->getfreqUsuariNameX2($inputid1, $fits);
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) últims 2 days
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            

                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorisme V6 - Predictor de context (adj i adv) total    
                        $res = $this->getContextTypeAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') { // (case: theme)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchOrdinal($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    } 
                }
                else if ($case == "manera") {
                   if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchQuant($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $res;
                    }
                    if ($fits = 'adv') {  // (case: manera)
                        // Algorisme V6 - Predictor de context (adv manera) total    
                        $res = $this->getContextTypeAdvManeraAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $contextTypeAdvManeraAll;
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }  
                }                       
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorisme V6 - Predictor de context (name) últims 2 dies
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) total              
                    $res = $this->getContextTypeAll('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            
            }
        }
        else if ($inputType[0]->pictoType != 'verb' && $inputType[0]->pictoType != 'name') {
            // Algorisme V6 - Predictor de context (name) últims 2 dies                                
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);                   

            // Algorisme V6 - Predictor de context (verb) total                      
            $contextTypeVerbsAll = $this->getContextTypeAll('verb');
            $VF = $this->insertFloorVF($VF, $contextTypeVerbsAll, $FSize);                  

            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbsAll, $TSize);
        }
        
        // rellena
        if (sizeof($VF) < $TSize) {
            $freqX1 = $this->getRecommenderX1();
            unset($freqX1[0]);
            unset($freqX1[1]);
            $VF = $this->rellenaVFX2X3($VF, $freqX1, $TSize);
        }
        
        return $VF;
    }   
   
    function getRecommenderX3() {   
        $paraulesFrase = $this->getIdsElem();        
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-2]->pictoid;
        $inputid2 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
        
        $inputType1 = $this->getTypesElem($inputid1);
        $inputType2 = $this->getTypesElem($inputid2);
        
        // Algorisme V2 - Predictor freqüència II (d'usuari)
        $VF = array();
        $VF = array_merge($VF,$this->getfreqUsuariX3($inputid1, $inputid2));        
        $TSize = 7;
        $FSize = $TSize - sizeof($VF);
        
        if ($inputType1[0]->pictoType != 'verb' && $inputType2[0]->pictoType == 'name' ) {          
            
            // Algorisme V6 - Predictor de context (verb) últims 2 dies                       
            $contextTypeVerbs2Days = $this->getContextType2Days('verb');
            $VF = $this->insertCeilVF($VF, $contextTypeVerbs2Days, $FSize);
            
            $freqX2 = $this->getRecommenderX2();
            $VF = $this->insertFloorVF($VF, $freqX2, $FSize);
            
            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbs2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $freqX2, $TSize);
        }
        else if ($inputType2[0]->pictoType == 'verb') {         
            $caseList = array("theme", "manera", "locto", "locfrom");            
            foreach ($caseList as $case) {
                $fits = $this->getFits($inputid1, $case);        
                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdjAdvX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariOrdinalX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                }
                else if ($case == "manera") {
                    if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariQuantX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits = 'adv') {  // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdvManeraX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                } 
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                    $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) últims 2 days
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            

                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorisme V6 - Predictor de context (adj i adv) total    
                        $res = $this->getContextTypeAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') { // (case: theme)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchOrdinal($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    } 
                }
                else if ($case == "manera") {
                   if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchQuant($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $res;
                    }
                    if ($fits = 'adv') {  // (case: manera)
                        // Algorisme V6 - Predictor de context (adv manera) total    
                        $res = $this->getContextTypeAdvManeraAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $contextTypeAdvManeraAll;
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }  
                }                       
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorisme V6 - Predictor de context (name) últims 2 dies
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) total              
                    $res = $this->getContextTypeAll('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            
            }
        }
        else if ($inputType[0]->pictoType != 'verb' && $inputType[0]->pictoType != 'name') {
            // Algorisme V6 - Predictor de context (name) últims 2 dies                                
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);                   

            // Algorisme V6 - Predictor de context (verb) total                      
            $contextTypeVerbsAll = $this->getContextTypeAll('verb');
            $VF = $this->insertFloorVF($VF, $contextTypeVerbsAll, $FSize);                  

            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbsAll, $TSize);
        }                        
        else if ($inputType1[0]->pictoType == 'verb') {            
            $caseList = array("theme", "manera", "locto", "locfrom");            
            foreach ($caseList as $case) {
                $fits = $this->getFits($inputid2, $case);        
                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdjAdvX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariOrdinalX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                }
                else if ($case == "manera") {
                    if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariQuantX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits = 'adv') {  // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariAdvManeraX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                        $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) últims 2 days
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                } 
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                    $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) últims 2 days
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            

                if ($case == "theme") {
                    if ($fits == 'adj' || $fits == 'adv') {
                        // Algorisme V6 - Predictor de context (adj i adv) total    
                        $res = $this->getContextTypeAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits == 'ordinal') { // (case: theme)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchOrdinal($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }
                    else if ($fits != 'modif' && $fits != 'quant') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    } 
                }
                else if ($case == "manera") {
                   if ($fits == 'quant') { // (case: manera)
                        // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. context)
                        $res = $this->getDbSearchQuant($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $res;
                    }
                    if ($fits = 'adv') {  // (case: manera)
                        // Algorisme V6 - Predictor de context (adv manera) total    
                        $res = $this->getContextTypeAdvManeraAll($fits);
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                        //return $contextTypeAdvManeraAll;
                    }
                    else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                        // Algorisme V6 - Predictor de context (name) últims 2 dies
                        $res = $this->getContextType2Days('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                        // Algorisme V6 - Predictor de context (name) total              
                        $res = $this->getContextTypeAll('name');
                        $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                    }  
                }                       
                else if ($fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
                    // Algorisme V6 - Predictor de context (name) últims 2 dies
                    $res = $this->getContextType2Days('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                    // Algorisme V6 - Predictor de context (name) total              
                    $res = $this->getContextTypeAll('name');
                    $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
                }            
            }
        }
        else if ($inputType[0]->pictoType != 'verb' && $inputType[0]->pictoType != 'name') {
            // Algorisme V6 - Predictor de context (name) últims 2 dies                                
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);                   

            // Algorisme V6 - Predictor de context (verb) total                      
            $contextTypeVerbsAll = $this->getContextTypeAll('verb');
            $VF = $this->insertFloorVF($VF, $contextTypeVerbsAll, $FSize);                  

            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbsAll, $TSize);
        }                        
        else if ($inputType1[0]->pictoType == 'name') {            
            // Algorisme V6 - Predictor de context (verb) últims 2 dies
            $contextTypeVerbs2Days = $this->getContextType2Days('verb');
            $VF = $this->insertCeilVF($VF, $contextTypeVerbs2Days, $FSize);
            
            // Algorisme V6 - Predictor de context (verb) total  
            $contextTypeVerbsAll = $this->getContextTypeAll('verb');
            $VF = $this->insertCeilVF($VF, $contextTypeVerbsAll, $FSize);
            
            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbsAll, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbs2Days, $TSize);

            if (sizeof($VF) < $TSize) {
                $freqX2 = $this->getRecommenderX2();    
                $VF = $this->rellenaVFX2X3($VF, $freqX2, $TSize);
            }
        }
        else { // ni name ni verb            
            // Algorisme V6 - Predictor de context (name) últims 2 dies          
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);
            
            // Algorisme V6 - Predictor de context (verb) total  
            $contextTypeVerbsAll = $this->getContextTypeAll('verb');
            $VF = $this->insertFloorVF($VF, $contextTypeVerbsAll, $FSize);
            
            // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeVerbsAll, $TSize);

            if (sizeof($VF) < $TSize) {
                $freqX2 = $this->getRecommenderX2();    
                $VF = $this->rellenaVFX2X3($VF, $freqX2, $TSize);
            }
        }
        
        // rellena
        if (sizeof($VF) < $TSize) {
            $freqX1 = $this->getRecommenderX1();
            unset($freqX1[0]);
            unset($freqX1[1]);
            $VF = $this->rellenaVFX2X3($VF, $freqX1, $TSize);
        }
        
        return $VF;                                   
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