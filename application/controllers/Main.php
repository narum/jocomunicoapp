<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Main extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('main_model');
        $this->load->library('Myaudio');
    }
        
    public function content_get()
    {
        //parametros que nos llegan del get
        $section = $this->query("section");
        $idLanguage = $this->query("idLanguage");

        //comprobación de los parametros
        if($section == NULL || $section == "" || $idLanguage == NULL || $idLanguage == "") {
            $this->response("missing argument startswith", 400);
        } 
        else {

            //Petición al modelo
            $saveResult = $this->main_model->getContent($section, $idLanguage);

            
            //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
            $array1 = array_column($saveResult, 'tagString');
            $array2 = array_column($saveResult, 'content');

            $keyValue = array_combine($array1, $array2);

            // Convertimos el array en un objeto
            $response = [
                "data" => $keyValue
            ];

            //respuesta
            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    
    public function getConfig_get()
    {
        $ID_SU = $this->query('IdSu');
        $response = $this->main_model->getConfig($ID_SU);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function saveSUserNames_post()
    {
        $ID_SU = $this->query('IdSu');
        $data = json_decode($this->query("data"), true); // convertimos el string json del post en array.

        $response = $this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $data);
        //reescrivimos la cookies
        $this->main_model->getConfig($ID_SU);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function checkPassword_post()
    {
        $ID_SU = $this->query('IdSu');
        $password = md5($this->query('pass'));
        $response = $this->main_model->checkSingleData('SuperUser', 'ID_SU', $ID_SU, 'pswd', $password);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function savePassword_post()
    {
        $ID_SU = $this->query('IdSu');
        $oldPass = md5($this->query('oldPass'));
        $newPass = md5($this->query('newPass'));
        //Check old password
        $passOk = $this->main_model->checkSingleData('SuperUser', 'ID_SU', $ID_SU, 'pswd', $oldPass);
        if($passOk['data']=='true'){
            $pass = ['pswd'=> $newPass];
            //Save new password
            $response = $this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $pass);

            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }else{
            $this->response("Passwords does not match", 400);
        }
        //reescrivimos la cookies
        $this->main_model->getConfig($ID_SU);
    }
    
    public function changeDefUser_post()
    {
        $ID_SU = $this->query('IdSu');
        $ID_U = $this->query('idU');
        $data = ['cfgDefUser'=> $ID_U]; // convertimos el string json del post en array.

        $response = $this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $data);
        //reescrivimos la cookies
        $this->main_model->getConfig($ID_SU);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function changeCfgBool_post()
    {
        $ID_SU = $this->query('IdSu');
        $data = ['cfg'.$this->query('data') => $this->query('value')]; // convertimos el string json del post en array.

        $this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $data);
        //reescrivimos la cookies
        $response = $this->main_model->getConfig($ID_SU);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function changeCfgVoices_post()
    {
        $ID_U = $this->query('IdU');
        $data = ['cfg'.$this->query('data') => $this->query('value')]; // convertimos el string json del post en array.

        $response = $this->main_model->changeData('User', 'ID_User', $ID_U, $data);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
  
    public function addUser_post()
    {
        // convertimos el string json del post en array.
        $data = [
            'ID_USU'=>$this->query('IdSu'),
            'ID_ULanguage'=>$this->query('ID_ULanguage'),
            'cfgExpansionLanguage'=>$this->query('cfgExpansionLanguage')
            ];

        $response = $this->main_model->saveData('User', $data);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    //Myaudio library Access
    public function getVoices_get(){
        $audio = new Myaudio();
        
        $interfaceVoices = $audio->listInterfaceVoices(true);
        $expansionVoices = $audio->listExpansionVoices(true);
        
        $appRunning = $audio->AppLocalOrServer();
        if ($appRunning == 'local'){
            $interfaceVoicesOffline = $audio->listInterfaceVoices(false);
            $expansionVoicesOffline = $audio->listExpansionVoices(false);
        }else{
            $interfaceVoicesOffline = array (
                [0] => 'App on server',
                [1] => false
            );
            $expansionVoicesOffline = array (
                [0] => 'App on server',
                [1] => false
            );
        }
        
        $voices = [
            'interfaceVoices'=>$interfaceVoices,
            'interfaceVoicesOffline'=>$interfaceVoicesOffline,
            'expansionVoices'=>$expansionVoices,
            'expansionVoicesOffline'=>$expansionVoicesOffline
            ];
        $response = [
            'voices'=>$voices,
            'appRunning'=>$appRunning
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    //Generate audio
    public function generateAudio_post(){
        
        $idusu = $this->query('IdU');
        $text = $this->query('text');
        $voice = $this->query('voice');
        $type = $this->query('type');
        $language = $this->query('language');
        $rate = $this->query('rate');
        
        $audio = new Myaudio();
        
        $response = $audio->selectedVoiceAudio($idusu, $text, $voice, $type, $language, $rate);
        
        $audio->waitForFile($response[0], $response[1]);
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function userValidate2_post(){
        $ID_SU = $this->query('IdSu');
        $data = [$this->query('data') => $this->query('value')]; // convertimos el string json del post en array.

        $response = $this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $data);
        //respuesta
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    //get today,last week and last month historic
    public function getHistoric_get()
    {
        $idusu = $this->session->userdata('idusu');
        
        $this->main_model->deleteHistoric();//delete all historic after last 30 days
        
        $today = $this->main_model->getHistoric($idusu, '1');
        $lastWeek = $this->main_model->getHistoric($idusu, '7');
        $lastMonth = $this->main_model->getHistoric($idusu, '30');

        $response = [
            'today' => $today,
            'lastWeek' => $lastWeek,
            'lastMonth' => $lastMonth
        ];
        
        $this->response($response, REST_Controller::HTTP_OK);
        
    }
    //get today,last week and last month historic
    public function getSentenceFolders_get()
    {
        $idusu = $this->session->userdata('idusu');
        
        $folders = $this->main_model->getData('S_Folder', 'ID_SFUser', $idusu);
        $response = [
            'folders' => $folders
        ];
        
        $this->response($response, REST_Controller::HTTP_OK);
    }
    //Up historic folder Order
    public function upHistoricFolder_post()
    {
        $idusu = $this->session->userdata('idusu');
        $ID_Folder = $this->query('ID_Folder');
        
        $folderToUp = $this->main_model->getSingleData('S_Folder', 'ID_SFUser', $idusu, 'ID_Folder', $ID_Folder);
        $folderToDown = $this->main_model->getSingleData('S_Folder', 'ID_SFUser', $idusu, 'folderOrder', $folderToUp[0]['folderOrder']-1);
        
        $orderUp = ['folderOrder'=> $folderToUp[0]['folderOrder']-1];
        $order = ['folderOrder'=> $folderToUp[0]['folderOrder']];
        
        $this->main_model->changeData('S_Folder', 'ID_Folder', $ID_Folder, $orderUp);
        $this->main_model->changeData('S_Folder', 'ID_Folder', $folderToDown[0]['ID_Folder'], $order);
        
        $this->response($response, REST_Controller::HTTP_OK);
    }
    //Down historic folder Order
    public function downHistoricFolder_post()
    {
        $idusu = $this->session->userdata('idusu');
        $ID_Folder = $this->query('ID_Folder');
        
        $folderToDown = $this->main_model->getSingleData('S_Folder', 'ID_SFUser', $idusu, 'ID_Folder', $ID_Folder);
        $folderToUp = $this->main_model->getSingleData('S_Folder', 'ID_SFUser', $idusu, 'folderOrder', $folderToDown[0]['folderOrder']+1);
        
        $orderDown = ['folderOrder'=> $folderToDown[0]['folderOrder']+1];
        $order = ['folderOrder'=> $folderToDown[0]['folderOrder']];
        
        $this->main_model->changeData('S_Folder', 'ID_Folder', $ID_Folder, $orderDown);
        $this->main_model->changeData('S_Folder', 'ID_Folder', $folderToUp[0]['ID_Folder'], $order);
        
        $this->response($response, REST_Controller::HTTP_OK);
    }
    //
    public function getSentencesOrHistoricFolder_post()
    {
        $idusu = $this->session->userdata('idusu');
        $ID_Folder = $this->query('ID_Folder');

        if($ID_Folder<0){
            $sol = $this->main_model->getHistoric($idusu, ($ID_Folder * (-1)));
        }else{
            $sol='sentence';
        }
        
        $response = [
            'ID_Folder' => $sol
        ];
        
        $this->response($response, REST_Controller::HTTP_OK);
    }
}
