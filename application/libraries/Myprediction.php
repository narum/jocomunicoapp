<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Myprediction {

    public function __construct() {}
    
    function getPrediction() {
        $numpar = $this->getcountElem();
        if ($numpar == 0) {
//            echo "entra 0";
//            echo "sale 0";
            return $this->getRecommenderX1();
        }
        else if ($numpar == 1) {
//            echo "entra 1";
            $res = $this->getRecommenderX2();
            if ($res == null) {
//                echo "sale 0";
                return $this->getRecommenderX1();
            }
            else {
//                echo "sale 1";                    
                return $res;
            }
        }
        else {
//          echo "entra +2";
            $res = $this->getRecommenderX3();
            if ($res == null) {
                $res = $this->getRecommenderX2();
                if ($res == null) {
    //              echo "sale 0";
                    return $this->getRecommenderX1();
                }
                else {
    //              echo "sale 1";                    
                    return $res;
                }                
            }                
            else {       
//              echo "sale +2";
                return $res;
            }
        }
    }    
    
    function getRecommenderX1() {  
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $output = $CI->Recommender->getRecommenderX1();
        return $output;                  
    }
    
    function getRecommenderX2() {
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $output = $CI->Recommender->getRecommenderX2();
        return $output;                 
    }
    
    function getRecommenderX3() {
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $output = $CI->Recommender->getRecommenderX3();
        return $output;      
    }     
    
    function getcountElem(){
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $output = $CI->Recommender->getcountElem();
        return $output;  
    }       
}

/* End of file Myprediction.php */