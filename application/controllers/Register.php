
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Register extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct('rest', TRUE);
        $this->load->model('main_model');
        $this->load->library('Myaudio');
    }
    
    public function runningLocalOrServer_get()
    {
        $audio = new Myaudio();
        $appRunning = $audio->AppLocalOrServer();
        $response = [
            'appRunning'=>$appRunning
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function allContent_get()
    {
        $section = $this->query("section");
        if($section == NULL || $section == "") {
            $this->response("missing argument startswith", 400);
        }
        else {
            $languages = $this->main_model->getLanguagesAvailable();

            //miramos el numero de idiomas disponibles
            $languagesNumber = sizeof($languages);

            //Creamos un array con el content en cada idioma
            for ($i = 1; $i <= $languagesNumber; $i++) {

                $result = $this->main_model->getContent($section,$i);

                //Cojemos los datos de las dos columnas de la petición y lo convertimos en un objecto clave:valor
                $array1 = array_column($result, 'tagString');
                $array2 = array_column($result, 'content');
                $keyValue = array_combine($array1, $array2);

                $content[$i]=$keyValue;
            }

            $response = [
                "languages" => $languages,
                "content" => $content
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    
    public function languagesAvailable_get()
    {
        $languages = $this->main_model->getLanguagesAvailable();

            $response = [
                "languages" => $languages
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
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

    public function checkData_get()
    {
        $table=$this->query("table");
        $column=$this->query("column");
        $data=$this->query("data");

        $exist = $this->main_model->checkData($table, $column, $data);

        $response = [
                "exist" => $exist
            ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    
    public function saveData_post()
    {
        $data = json_decode($this->query("data"), true); // convertimos el string json del post en array.
        $table = $this->query("table");

        $saved=$this->main_model->saveData($table, $data);

        $response = [
                "saved" => $saved
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    public function saveUserData_post()
    {
        $SUname = $this->query("SUname");
        $ID_ULanguage = $this->query("ID_ULanguage");
        $defLanguage = $this->query("defLanguage");

        $response=$this->main_model->saveUser($SUname,$ID_ULanguage);

        if($ID_ULanguage==$defLanguage){
            $defUser = ['cfgDefUser'=> $response["ID_U"]];
            //Save new password
            $this->main_model->changeData('SuperUser', 'ID_SU', $response["ID_SU"], $defUser);
        }

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }
    public function changePass_post()
    {
        $emailKey = $this->query("emailKey");
        $ID_SU = $this->query("ID_SU");
        $pass = json_decode($this->query("pass"), true); // convertimos el string json del post en array.
        $changed=false;

        $response=$this->main_model->userValidation($emailKey, $ID_SU);
        $userExist=$response["userExist"];

        if($userExist){
            $changed=$this->main_model->changeData('SuperUser', 'ID_SU', $ID_SU, $pass);
        }
        $response = [
                "passChanged" => $changed,
                "userExist" => $userExist
            ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function emailValidation_post()
    {
        $emailKey = $this->query("emailKey");
        $ID_SU = $this->query("ID_SU");

        $response=$this->main_model->userValidation($emailKey, $ID_SU);


        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    public function generateValidationMail_post()
    {
        //Check data
        $user = $this->query("user");
        if($user == NULL || $user == "") {
            $this->response("missing arguments", 400);
        }else{

            $sended=false;
            $local=false;
            $message="User Does not exist";

            //Check if user exist
            $userValidated=$this->main_model->checkData('SuperUser', 'ID_SU', $user);
            //If user exists
            if($userValidated === "true"){

                //Check server is local or online
                $audio = new Myaudio();
                $appRunning = $audio->AppLocalOrServer();
                if ($appRunning == 'local'){
                    $local=true;
                    $message="Local server";
                    //Change user validated to 1
                    $this->main_model->changeData('SuperUser', 'ID_SU', $user, ['UserValidated' => '1']);
                }else{
                    //get data from user
                    $data=$this->main_model->getFirstData('SuperUser', 'ID_SU', $user);

                    //send email
                    $email=$data["email"];
                    $userName=$data["realname"] ." " . $data["surnames"] ;
                    $ID_SU=$data["ID_SU"];
                    $pass=$data["pswd"];
                    $language=$data["cfgDefUser"];
                    $hash=md5($pass . $ID_SU);
                    $url= base_url() . '#/emailValidation/' . $hash . '/' . $ID_SU;

                    if($language==1){
                        //Catalan email
                        $subject    = 'JoComunico/Recuperació de contrasenya';
                        $message   = 'Accedeix el següent enllaç per validar la conta. \r\n ' . $url;
                    }else{
                        //Spanish email
                        $subject    = 'JoComunico/Recuperación de contraseña';
                        $message   = 'Dirigete al siguiente enlace para validar la cuenta. \r\n ' . $url;
                    }

                    $sended=$this->sendEmail($email, $userName, $subject, $message);
                }
            }
            $response = [
                "sendend" => $sended,
                "local" => $local,
                "message" => $message
            ];

            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
    public function passRecoveryMail_post()
    {
        $sended=false;
        $exist=false;
        $local=false;
        $message="User Does not exist";

        $user = $this->query("user");

        //Check data
        if($user == NULL || $user == "") {
            $response = ["sendend" => $sended,"exist" => $exist,"message" => $message];
            $this->response($response, REST_Controller::HTTP_OK);
        }
        else{
            //Check if data are email
            if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
                //Check if email exist
                $email=$user;
                $emailValidated=$this->main_model->checkData("SuperUser", "email", $email);
                //If email exists get data from user
                if($emailValidated === "true"){
                    $data=$this->main_model->getFirstData('SuperUser', 'email', $email);
                    $exist=true;
                }
            }
            else{
                //Check if user exist
                $userValidated=$this->main_model->checkData('SuperUser', 'SUname', $user);
                //If user exists get data from user
                if($userValidated === "true"){
                    $data=$this->main_model->getFirstData('SuperUser', 'SUname', $user);
                    $exist=true;
                }
            }

            if($exist){
                
                //User data for email
                $email=$data["email"];
                $userName=$data["realname"] ." " . $data["surnames"] ;
                $ID_SU=$data["ID_SU"];
                $pass=$data["pswd"];
                $language=$data["cfgDefUser"];
                $hash=md5($pass . $ID_SU);
                $path= '/passRecovery/' . $hash . '/' . $ID_SU;
                $url= base_url() . '#/passRecovery/' . $hash . '/' . $ID_SU;
                
                //Check server is local or online
                $audio = new Myaudio();
                $appRunning = $audio->AppLocalOrServer();
                if ($appRunning == 'local'){
                    $sended=false;
                    $local=true;
                    $message="Local server";
                }else{

                    //send email
                    if($language==1){
                        //Catalan email
                        $subject    = 'JoComunico/Recuperació de contrasenya';
                        $message   = 'Accedeix el següent enllaç per introduir la nova contrasenya. \r\n ' . $url;
                    }else{
                        //Spanish email
                        $subject    = 'JoComunico/Recuperación de contraseña';
                        $message   = 'Dirigete al siguiente enlace para introducir la nueva contraseña. \r\n ' . $url;
                    }

                    $sended=$this->sendEmail($email, $userName, $subject, $message);
                }
            }

            $response = [
                "sendend" => $sended,
                "exist" => $exist,
                "local" => $local,
                "url" => $path,
                "message" => $message
            ];

            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }

    }
    
    public function sendEmail($mail, $userName, $subject, $message){
        //Cargamos la libreria de codeigniter
        $this->load->library('email');
        //Indicamos el protocolo a utilizar
        $config['protocol'] = 'smtp';
        //El servidor de correo que utilizaremos
        $config["smtp_host"] = 'smtp.gmail.com';
        //Nuestro usuario
        $config["smtp_user"] = '';
        //Nuestra contraseña
        $config["smtp_pass"] = '';
        //El puerto que utilizará el servidor smtp
        $config["smtp_port"] = '587';
        //El juego de caracteres a utilizar
        $config['charset'] = 'utf-8';
        //Permitimos que se puedan cortar palabras
        $config['wordwrap'] = TRUE;
        //El email debe ser valido
        $config['validate'] = true;

        //Establecemos esta configuración
        $this->email->initialize($config);
        //Ponemos la dirección de correo que enviará el email y un nombre
        $this->email->from('jocomunico@jocomunico.com', 'JoComunico');

        //Destinatario
        $this->email->to($mail, $userName);

        //Definimos el asunto del mensaje
        $this->email->subject($subject);

        //Definimos el mensaje a enviar
        $this->email->message($message);

        //Enviamos el email y comprovamos el envio
        if($this->email->send()){
            return true;
        }else{
            return false;
        }
    }
}
