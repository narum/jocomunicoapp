<?php

class DBwords extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }

    /*
     * Gets all names from ddbb that starts with ($startswith) in the language ($language)
     */

    function getDBNamesLike($startswith, $language, $user)
    {
        $output = array();
        
        $this->db->limit(6);// limit up to 6
        
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('nameid as id, nomtext as text, imgPicto, Pictograms.ID_PUser');// rename the field like we want
        //$this->db->from('Name'. $language);// select the table name+language
        $this->db->join('Pictograms', 'Name' . $language . '.nameid = Pictograms.pictoid', 'left'); // Join the tables name with the picto associate
        $this->db->like('nomtext', $startswith, 'after');// select only the names that start with $startswith
        $this->db->order_by('Name' . $language . '.nomtext', 'asc'); // order the names 
        $query = $this->db->get('Name'. $language);// execute de query
              
        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }
        return $output;
    }

   /*
     * Gets all verbs from ddbb that starts with ($startswith) in the language ($language)
     */
    
    function getDBVerbsLike($startswith, $language, $user)
    {
        $output = array();
      
        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('verbid as id,verbtext as text, imgPicto');
        $this->db->from('Verb'.$language);
        $this->db->join('Pictograms', 'Verb'.$language.'.verbid = Pictograms.pictoid', 'left');
        $this->db->where('actiu', '1');
        $this->db->like('verbtext', $startswith, 'after');
        $this->db->order_by('Verb'.$language.'.verbtext', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }
        
        return $output;
    }

    /*
     * Gets all adjectius from ddbb that starts with ($startswith) in the language ($language)
     */
    function getDBAdjLike($startswith, $language, $user)
    {
        $output = array();
        
        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('adjid as id,masc as text, imgPicto');
        $this->db->from('Adjective'.$language);
        $this->db->join('Pictograms', 'Adjective'.$language.'.adjid = Pictograms.pictoid', 'left');
        $this->db->like('masc', $startswith, 'after');
        $this->db->order_by('Adjective'.$language.'.masc', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }
        return $output;
    }
    
    /*
     * Gets all expressions from ddbb that starts with ($startswith) in the language ($language)
     */
    function getDBExprsLike($startswith, $language, $user)
    {
        $output = array();

        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('exprid as id,exprtext as text, imgPicto');
        $this->db->from('Expressions'.$language);
        $this->db->join('Pictograms', 'Expressions'.$language.'.exprid = Pictograms.pictoid', 'left');
        $this->db->like('exprtext', $startswith, 'after');
        $this->db->order_by('Expressions'.$language.'.exprtext', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }
        return $output;
    }

    /*
     * Gets all adverbs from ddbb that starts with ($startswith) in the language ($language)
     */
    function getDBAdvsLike($startswith, $language, $user)
    {
        $output = array();
        
        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('advid as id,advtext as text, imgPicto');
        $this->db->from('Adverb'.$language);
        $this->db->join('Pictograms', 'Adverb'.$language.'.advid = Pictograms.pictoid', 'left');
        $this->db->like('advtext', $startswith, 'after');
        $this->db->order_by('Adverb'.$language.'.advtext', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }

        return $output;
    }

    /*
     * Gets all modifier from ddbb that starts with ($startswith) in the language ($language)
     */
    function getDBModifsLike($startswith, $language, $user)
    {
        $output = array();

        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('modid as id,masc as text, imgPicto');
        $this->db->from('Modifier'.$language);
        $this->db->join('Pictograms', 'Modifier'.$language.'.modid = Pictograms.pictoid', 'left');
        $this->db->like('masc', $startswith, 'after');        
        $this->db->order_by('Modifier'.$language.'.masc', 'asc');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }

        return $output;
    }


    /*
     * Gets all QuestionPart from ddbb that starts with ($startswith) in the language ($language)
     */
    function getDBQuestionPartLike($startswith, $language, $user)
    {
        $output = array();
        
        
        $this->db->limit(6);
        $this->db->or_where_in('Pictograms.ID_PUser', array('1',$user)); //Get all default and own user pictos
        $this->db->select('questid as id,parttext as text, imgPicto');
        $this->db->from('QuestionPart'.$language);
        $this->db->join('Pictograms', 'QuestionPart'.$language.'.questid = Pictograms.pictoid', 'left');
        $this->db->like('parttext', $startswith, 'after'); 
        $this->db->order_by('parttext', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }

        return $output;
    }

}
