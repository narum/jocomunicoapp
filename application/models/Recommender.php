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
        
        // Ids of the pictograms for "I" and "you" in all languages
        $subjList = array(444, 466);
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('pictogramslanguage');
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left');                             
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));
        $this->db->where_in('pictograms.pictoid', $subjList);
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
    
    private function getfreqUsuariX2NonExpan($inputid1) {                            
        $output = array();
        $output = null;
        
        $this->db->select('pictograms.imgPicto, pictograms.pictoid, pictogramslanguage.pictotext');
        $this->db->from('p_statsuserpictox2');              
        $this->db->join('pictogramslanguage', 'p_statsuserpictox2.picto2id = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'p_statsuserpictox2.picto2id = pictograms.pictoid', 'left'); 
        $this->db->where('p_statsuserpictox2.ID_PSUP2User', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                   
        $this->db->where('p_statsuserpictox2.picto1id', $inputid1);  
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
        
        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'pictogramslanguage.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $pictoType);               
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
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
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
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
        $this->db->where('p_statsuserpictox3.picto2id', $inputid2);  
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

        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('adjclass'.$this->session->userdata('ulangabbr'), 'pictogramslanguage.pictoid = adjclass'.$this->session->userdata('ulangabbr').'.adjid', 'left');
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('adjclass'.$this->session->userdata('ulangabbr').'.class', $pictoType);               
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output; 
    }
    
    private function getContextType2Days($pictoType) {  
                
        $output = null;
        $date = array(date("Y-m-d"), date("Y-m-d", strtotime("yesterday")));

        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where_in('P_StatsUserPicto.lastdate', $date);
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('pictograms.pictoType', $pictoType);               
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }
    
    private function getContextFitsNClass2Days($fits) {                  
        $output = null;
        $date = array(date("Y-m-d"), date("Y-m-d", strtotime("yesterday")));    
                
        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('nameclass'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = nameclass'.$this->session->userdata('ulangabbr').'.nameid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where_in('P_StatsUserPicto.lastdate', $date);
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where_in('nameclass'.$this->session->userdata('ulangabbr').'.class', $fits);        
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    } 
    
    private function getContextFitsNClassAll($fits) {                            
        $output = null;

        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('nameclass'.$this->session->userdata('ulangabbr'), 'pictograms.pictoid = nameclass'.$this->session->userdata('ulangabbr').'.nameid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where_in('nameclass'.$this->session->userdata('ulangabbr').'.class', $fits);        
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
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

        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->join('modifier'.$this->session->userdata('ulangabbr'), 'P_StatsUserPicto.pictoid = modifier'.$this->session->userdata('ulangabbr').'.modid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('modifier'.$this->session->userdata('ulangabbr').'.type', $pictoType);               
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();                

        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    } 
    
    private function getContextTypeAll($pictoType) {                            
        $output = null;

        $this->db->select('P_StatsUserPicto.pictoid, P_StatsUserPicto.countx1 as repes, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->from('P_StatsUserPicto');              
        $this->db->join('pictogramslanguage', 'P_StatsUserPicto.pictoid = pictogramslanguage.pictoid', 'left'); 
        $this->db->join('pictograms', 'P_StatsUserPicto.pictoid = pictograms.pictoid', 'left'); 
        $this->db->where('P_StatsUserPicto.ID_PSUPUser', $this->session->userdata('idusu'));               
        $this->db->where('pictogramslanguage.languageid', $this->session->userdata('ulanguage'));                                                           
        $this->db->where('pictograms.pictoType', $pictoType);               
        $this->db->group_by('P_StatsUserPicto.pictoid, pictogramslanguage.pictotext, pictograms.imgPicto');
        $this->db->order_by('repes', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }
    
    public function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    
    private function boolDetPos($pictoid) {
        $output = null;
        
        $this->db->select('type');
        $this->db->from('modifier'.$this->session->userdata('ulangabbr'));              
        $this->db->where('modid', $pictoid);  
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        
        $res = false;
        if ($output[0]->type == 'det' || $this->startsWith($output[0]->type, 'pos')) $res = true;
        return $res;
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
    
    private function get1Opt($picto1id, $case) {
        $output = array();
        $output = null;
        $this->db->select($case);    
        $this->db->from('pattern'.$this->session->userdata('ulangabbr'));        
        $this->db->where('verbid', $picto1id);     
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
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
    
    private function get1OptFitsX2($inputid1, $case, $VF, $TSize, $fits) {
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
            else if ($fits != null && $fits != 'modif' && $fits != 'quant') {
                // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                $res = $this->getfreqUsuariNameX2($inputid1, $fits);
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);                        
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
            else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                $res = $this->getfreqUsuariNameX2($inputid1, $fits);
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
            }
        } 
        else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
            // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
            $res = $this->getfreqUsuariNameX2($inputid1, $fits);
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
            else if ($fits != null && $fits != 'modif' && $fits != 'quant') {
                // Algorisme V6 - Predictor de context ($fits) últims 2 dies
                $res = $this->getContextFitsNClass2Days($fits);
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                // Algorisme V6 - Predictor de context ($fits) total              
                $res = $this->getContextFitsNClassAll($fits);
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
            else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') { // ¿ algun caso ?
                // Algorisme V6 - Predictor de context (name) últims 2 dies
                $res = $this->getContextType2Days('name');
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                // Algorisme V6 - Predictor de context (name) total              
                $res = $this->getContextTypeAll('name');
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
            }  
        }                       
        else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
            // Algorisme V6 - Predictor de context ($fits) últims 2 dies
            $res = $this->getContextFitsNClass2Days($fits);
            $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

            // Algorisme V6 - Predictor de context ($fits) total              
            $res = $this->getContextFitsNClassAll($fits);
            $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
        }           
        return $VF;
    }
    
    private function get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $fits) {
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
            else if ($fits != null && $fits != 'modif' && $fits != 'quant') {
                // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
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
            else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') {
                // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
                $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
            }
        } 
        else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
            // Algorismes V3 i V4 - Predictor verbs I i II (basat en freq. usuari)
            $res = $this->getfreqUsuariNameX3($inputid1, $inputid2, $fits);
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
            else if ($fits != null && $fits != 'modif' && $fits != 'quant') {
                // Algorisme V6 - Predictor de context ($fits) últims 2 dies
                $res = $this->getContextFitsNClass2Days($fits);
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                // Algorisme V6 - Predictor de context ($fits) total              
                $res = $this->getContextFitsNClassAll($fits);
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
            else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj') { // ¿ algun caso ?
                // Algorisme V6 - Predictor de context (name) últims 2 dies
                $res = $this->getContextType2Days('name');
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

                // Algorisme V6 - Predictor de context (name) total              
                $res = $this->getContextTypeAll('name');
                $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
            }  
        }                       
        else if ($fits != null && $fits != 'ordinal' && $fits != 'modif' && $fits != 'adj' && $fits != 'adv' && $fits != 'quant') {
            // Algorisme V6 - Predictor de context ($fits) últims 2 dies
            $res = $this->getContextFitsNClass2Days($fits);
            $VF = $this->rellenaVFX2X3($VF, $res, $TSize);

            // Algorisme V6 - Predictor de context ($fits) total              
            $res = $this->getContextFitsNClassAll($fits);
            $VF = $this->rellenaVFX2X3($VF, $res, $TSize);
        }           
        return $VF;
    }

    private function get1OptFits($inputid1, $case, $b) {
        $fits = null;
        $tipus = $this->getCaseTipus($inputid1, $case, $b);
        $caseTipus = $case."tipus";
        if ($tipus != null && $tipus[0]->$caseTipus != 'adj' && $tipus[0]->$caseTipus != 'adv' && $tipus[0]->$caseTipus != 'modif' && $tipus[0]->$caseTipus != 'quant' && $tipus[0]->$caseTipus != 'verb' && $tipus[0]->$caseTipus != 'ordinal') {
            $fits = $this->getMMFits($tipus, $case);
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
        else if ($tipus != null && $case == 'locto') {
            $fits = 'lloc';
        }
        else if ($tipus != null && $case == 'locfrom') {
            $fits = 'lloc';
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
        $this->db->order_by('pictograms.pictoid', 'random');
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
    
    private function getfreqUsuariX3NonExpan($inputid1, $inputid2) {
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
        $this->db->order_by('countx3', 'desc');        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        }
        return $output;   
    }

    private function insertFloorVF($VF, $Prediction, $FSize) {
        $k = 0;
        foreach($Prediction as $value) {
            if (sizeof($VF) == 0) {
                $VF = array();
                array_push($VF,$value);
                $FSize = 7;
            }
                                    
            $repe = false;
            $paraulesFrase = $this->getIdsElem();
            for ($i = 0; $i < sizeof($paraulesFrase); $i++) {
                if ($value->pictoid == $paraulesFrase[$i]->pictoid) {
                    $repe = true;
                    break;
                }
            }
            
            if (!$repe) {   
                for ($i = 0; $i < sizeof($VF); $i++) {
                    if($k == floor($FSize/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                    else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                        array_push($VF,$value);
                        $k++;
                    }
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
                        
            $repe = false;
            $paraulesFrase = $this->getIdsElem();
            for ($i = 0; $i < sizeof($paraulesFrase); $i++) {
                if ($value->pictoid == $paraulesFrase[$i]->pictoid) {
                    $repe = true;
                    break;
                }
            }
            
            if (!$repe) {      
                for ($i = 0; $i < sizeof($VF); $i++) {
                    if($k == ceil($FSize/2) || $value->pictoid == $VF[$i]->pictoid) { break; }
                    else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                        array_push($VF,$value);
                        $k++;
                    }
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
            
            $repe = false;
            $paraulesFrase = $this->getIdsElem();
            for ($i = 0; $i < sizeof($paraulesFrase); $i++) {
                if ($value->pictoid == $paraulesFrase[$i]->pictoid) {
                    $repe = true;
                    break;
                }
            }
            
            if (!$repe) {            
                for ($i = 0; sizeof($VF) < $TSize && $i < sizeof($VF); $i++) {
                    if ($value->pictoid == $VF[$i]->pictoid) { break; }
                    else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                        array_push($VF,$value);
                    }
                }
            }
        }
        return $VF;
    }
    
    private function rellenaVFX1($VF, $Prediction, $TSize) {
        foreach($Prediction as $value) {
                                    
            $repe = false;
            $paraulesFrase = $this->getIdsElem();
            for ($i = 0; $i < sizeof($paraulesFrase); $i++) {
                if ($value->pictoid == $paraulesFrase[$i]->pictoid) {
                    $repe = true;
                    break;
                }
            }            
            
            if (!$repe) {            
                for ($i = 0; sizeof($VF) < $TSize &&  $i < sizeof($VF); $i++) {
                    if ($value->pictoid == $VF[$i]->pictoid) { break; }
                    else if ($value->pictoid != $VF[$i]->pictoid && $i+1 === sizeof($VF)) {
                        array_push($VF,$value);
                    }
                }
            }
        }
        return $VF;
    }
    
    function getRecommenderX1() {
        $pred = null;
        if ($this->session->userdata('cfgExpansionOnOff')) $pred = $this->getRecommenderX1Expan();
        else $pred = $this->getRecommenderX1NonExpan();
        return $pred;
    }
        
    function getRecommenderX2() {
        $pred = null;
        if ($this->session->userdata('cfgExpansionOnOff')) $pred = $this->getRecommenderX2Expan();
        else $pred = $this->getRecommenderX2NonExpan();
        return $pred;
    }   
   
    function getRecommenderX3() {   
        $pred = null;
        if ($this->session->userdata('cfgExpansionOnOff')) $pred = $this->getRecommenderX3Expan();
        else $pred = $this->getRecommenderX3NonExpan();
        return $pred;
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
    
    private function getRecommenderX1Expan() {
        $TSize = $this->session->userdata('cfgPredBarNumPred');

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
    
    private function getRecommenderX1NonExpan() {
        $TSize = $this->session->userdata('cfgPredBarNumPred');

        // Algorisme V5 - Predictor inicial (cas 00 no hi ha res (fix jo i tu)  
        $VF = $this->getSubj();        

        // Algorisme V2 - Predictor freqüència II (d'usuari)                   
        $freqUsuari = $this->getfreqUsuariX1();
        $VF = $this->rellenaVFX1($VF, $freqUsuari, $TSize);           

        return $VF;
    }  
    
    private function getRecommenderX2Expan() {
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;        
        $inputType = $this->getTypesElem($inputid1);

        // Algorisme V2 - Predictor freqüència II (d'usuari)
        $VF = array();
        $VF = array_merge($VF,$this->getfreqUsuariX2($inputid1));
        $TSize = $this->session->userdata('cfgPredBarNumPred');
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
            $caseList = array("theme", "locto", "locfrom", "manera", "time", "tool");
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid1, $case)[0]->$case == 1) $VF = $this->get1OptFitsX2($inputid1, $case, $VF, $TSize, $case);
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid1, $case, 1);
                        $VF = $this->get1OptFitsX2($inputid1, $case, $VF, $TSize, $fits);
                    }
                }
            }
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid1, $case)[0]->$case == 'opt') {
                        $VF = $this->get1OptFitsX2($inputid1, $case, $VF, $TSize, $case);
                    }
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid1, $case, 'opt');
                        $VF = $this->get1OptFitsX2($inputid1, $case, $VF, $TSize, $fits); 
                    }
                }
            }
        }
        else if ($inputType[0]->pictoType == 'modifier' && $this->boolDetPos($inputid1)) {
            // Algorisme V6 - Predictor de context (name) últims 2 dies                                
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);                   
            
            // Algorisme V6 - Predictor de context (name) total                      
            $contextTypeNamesAll = $this->getContextTypeAll('name');
            $VF = $this->insertFloorVF($VF, $contextTypeNamesAll, $FSize);   
            
                        // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeNamesAll, $TSize);            
        }
        else {
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
        
        // Algorisme V6 - Predictor de context (name) total
        if (sizeof($VF) < $TSize) {
            $contextTypeNameAll = $this->getContextTypeAll('name');
            $VF = $this->rellenaVFX1($VF, $contextTypeNameAll, $TSize); 
        }
        return $VF;
    }
    
    private function getRecommenderX2NonExpan() {
        
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;        

        // Algorisme V2 - Predictor freqüència II (d'usuari)
        $VF = array();
        $VF = array_merge($VF,$this->getfreqUsuariX2NonExpan($inputid1));        
        $TSize = $this->session->userdata('cfgPredBarNumPred');
        
        // rellena
        if (sizeof($VF) < $TSize) {
            $freqX1 = $this->getfreqUsuariX1();
            unset($freqX1[0]);
            unset($freqX1[1]);
            $VF = $this->rellenaVFX2X3($VF, $freqX1, $TSize);
        }

        return $VF;                
    }
    
    private function getRecommenderX3Expan() {
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
        
        if ($inputType2[0]->pictoType == 'modifier' && $this->boolDetPos($inputid2)) {
            // Algorisme V6 - Predictor de context (name) últims 2 dies                                
            $contextTypeName2Days = $this->getContextType2Days('name');
            $VF = $this->insertCeilVF($VF, $contextTypeName2Days, $FSize);                   
            
            // Algorisme V6 - Predictor de context (name) total                      
            $contextTypeNamesAll = $this->getContextTypeAll('name');
            $VF = $this->insertFloorVF($VF, $contextTypeNamesAll, $FSize);   
            
                        // rellena
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeName2Days, $TSize);
            if (sizeof($VF) < $TSize) $VF = $this->rellenaVFX2X3($VF, $contextTypeNamesAll, $TSize);            
        }
        else if ($inputType1[0]->pictoType != 'verb' && $inputType2[0]->pictoType == 'name' || 
                ($inputType1[0]->pictoType == 'name' && $inputType2[0]->pictoType != 'verb')) {          
            
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
            $caseList = array("theme", "locto", "locfrom", "manera", "time", "tool");            
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid2, $case)[0]->$case == 1) $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $case);
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid2, $case, 1);
                        $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $fits);
                    }
                }
            }
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid2, $case)[0]->$case == 'opt') $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $case);
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid2, $case, 'opt');
                        $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $fits); 
                    }
                }
            }
        }
        else if ($inputType1[0]->pictoType != 'verb' && $inputType2[0]->pictoType != 'name') {
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
            $caseList = array("theme", "locto", "locfrom", "manera", "time", "tool");            
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid2, $case)[0]->$case == 1) $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $case);
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid2, $case, 1);
                        $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $fits);
                    }
                }
            }
            foreach ($caseList as $case) {
                if ($case == "time" || $case == "tool") {
                    if (sizeof($VF) < $TSize && $this->get1Opt($inputid2, $case)[0]->$case == 'opt')$VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $case);
                }
                else {
                    if (sizeof($VF) < $TSize) {
                        $fits = $this->get1OptFits($inputid2, $case, 'opt');
                        $VF = $this->get1OptFitsX3($inputid1, $inputid2, $case, $VF, $TSize, $fits); 
                    }
                }
            }
        }
        else if ($inputType1[0]->pictoType != 'verb' && $inputType2[0]->pictoType != 'name') {
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
    
    private function getRecommenderX3NonExpan() {
        $paraulesFrase = $this->getIdsElem();
        $inputid1 = $paraulesFrase[sizeof($paraulesFrase)-2]->pictoid;
        $inputid2 = $paraulesFrase[sizeof($paraulesFrase)-1]->pictoid;
        
        $inputType1 = $this->getTypesElem($inputid1);
        $inputType2 = $this->getTypesElem($inputid2);
        
        // Algorisme V2 - Predictor freqüència II (d'usuari)
        $VF = array();
        $VF = array_merge($VF,$this->getfreqUsuariX3NonExpan($inputid1, $inputid2));        
        $TSize = $this->session->userdata('cfgPredBarNumPred');
                        
        // rellena
        if (sizeof($VF) < $TSize) {
            $freqX2 = $this->getfreqUsuariX2NonExpan($inputid2);
            $VF = $this->rellenaVFX2X3($VF, $freqX2, $TSize);
        }

        // rellena
        if (sizeof($VF) < $TSize) {
            $freqX1 = $this->getfreqUsuariX1();
            unset($freqX1[0]);
            unset($freqX1[1]);
            $VF = $this->rellenaVFX2X3($VF, $freqX1, $TSize);
        }
        
        return $VF;
    }
    
    private function delfreqUsuariX1() {
        $this->db->where('ID_PSUPUser', $this->session->userdata('idusu'));                             
        $this->db->delete('p_statsuserpicto');
    }
    
    public function delfreqUsuariX2() {
        $this->db->where('ID_PSUP2User', $this->session->userdata('idusu'));                             
        $this->db->delete('p_statsuserpictox2');
    }
    
    public function delfreqUsuariX3() {
        $this->db->where('ID_PSUP3User', $this->session->userdata('idusu'));                             
        $this->db->delete('p_statsuserpictox3');
    }
}

?>