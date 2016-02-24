<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class SearchWord extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DBwords');

    }
        
    public function index_get()
    {
        
    }
    
    public function getDBAll_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $Names = $this->DBwords->getDBNamesLike($startswith, $language);
        $Verbs = $this->DBwords->getDBVerbsLike($startswith, $language);
        $Adj = $this->DBwords->getDBAdjLike($startswith, $language);
        $Exprs = $this->DBwords->getDBExprsLike($startswith, $language);
        $Advs = $this->DBwords->getDBAdvsLike($startswith, $language);
        $Modifs = $this->DBwords->getDBModifsLike($startswith, $language);
        $QuestionPart = $this->DBwords->getDBQuestionPartLike($startswith, $language);
        
        // Marge all arrays to one
        $DataArray = array_merge($Names, $Verbs, $Adj, $Exprs, $Advs, $Modifs, $QuestionPart);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function getDBNames_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBNamesLike($startswith, $language);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function getDBVerbs_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBVerbsLike($startswith, $language);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function getDBAdj_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBAdjLike($startswith, $language);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function getDBExprs_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBExprsLike($startswith, $language);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function getDBOthers_post()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        //$languageNum get cookie lenguage user
        $languageNum = 2;

        // MODIF: canviar por cookie
        $language = $this->switch_language($languageNum);

        
        // Controller search all names from all picto table
        $Advs = $this->DBwords->getDBAdvsLike($startswith, $language);
        $Modifs = $this->DBwords->getDBModifsLike($startswith, $language);
        $QuestionPart = $this->DBwords->getDBQuestionPartLike($startswith, $language);
        
        $DataArray = array_merge($Advs, $Modifs, $QuestionPart);
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    
    //Unused function, read row by row and make changes on the array
    function create_paths($DataArray){
        function concat_path($row)
        {
            $newPath = base_url() . "img/pictos/" . $row["imgPicto"];
            $row["imgPicto"] = $newPath;
            return $row;
        }
        return array_map("concat_path", $DataArray);
    }
    // MODIF: funcio temporal per escollir el idoma.
    function switch_language($languageNum)
    {
        if ($languageNum == 1){
            $language = "CA";
        } else if($languageNum == 2) {
            $language = "ES";
        }
        return $language;
    }
}

