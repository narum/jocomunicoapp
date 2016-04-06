<?php

class DBwords extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();

        $this->load->library('Myword');
    }

    /*
     * GETS THE NOUNS OF THE TYPE $type FROM THE DATABASE
     */

    function getDBNamesLike($startswith, $language)
    {
        $output = array();
      
        $this->db->select('nameid as id, nomtext as text, imgPicto');// seleccionem els camps que ens interessa retornar
        $this->db->from('Name'. $language);// Seleccionem la taula nameca o namees
        $this->db->join('Pictograms', 'Name' . $language . '.nameid = Pictograms.pictoid', 'left'); // ajuntem les columnes de les dos taules
        $this->db->like('nomtext', $startswith, 'after');// Seleccionem els noms de la taula que comencen per $startswith
        $this->db->order_by('Name' . $language . '.nomtext', 'asc'); // ordenem de manera ascendent tota la taula en funció del nomtext
        $query = $this->db->get();// Fem la query i la guardem a la variable query
              
        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }
        return $output;
    }

    //IMPLEMENTAR AMB LIKE LES SEGUENTS FUNCIONS!
    
    function getDBVerbsLike($startswith, $language)
    {
        $output = array();
      
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

    function getDBAdjLike($startswith, $language)
    {
        $output = array();

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
    function getDBExprsLike($startswith, $language)
    {
        $output = array();

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

    function getDBAdvsLike($startswith, $language)
    {
        $output = array();
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

    function getDBModifsLike($startswith, $language)
    {
        $output = array();

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



    function getDBQuestionPartLike($startswith, $language)
    {
        $output = array();
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
