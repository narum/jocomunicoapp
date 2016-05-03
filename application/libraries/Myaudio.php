<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Myaudio {
            
    function __construct() {}
    
    /*
     * RETURNS true if connected or false otherwise
     */
    public function isOnline() 
    {
        // pings example.com and google.com
        $is_conn = null;
        $connected1 = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
        $connected2 = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        // if either is successful
        if ($connected1 || $connected2){
            $is_conn = true; //action when connected
            fclose($connected1);
            fclose($connected2);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }
    
    /*
     * RETURNS 'local' if the App is running locally or 'server' if it's running from the server
     */
    public function AppLocalOrServer()
    {
        if (preg_match('/localhost/i', base_url())) return "local";
        else return "server";
    }
    
    /*
     * Gets the OS from which the app is running
     */
    public function getOS() { 
        
        $server_data = $_SERVER['HTTP_USER_AGENT'];

        $os_platform    =   "Unknown OS Platform";

        $os_array       =   array(
                                '/windows nt 10/i'      =>  'Windows',
                                '/windows nt 6.3/i'     =>  'Windows',
                                '/windows nt 6.2/i'     =>  'Windows',
                                '/windows nt 6.1/i'     =>  'Windows',
                                '/windows nt 6.0/i'     =>  'Windows',
                                '/windows nt 5.2/i'     =>  'Windows',
                                '/windows nt 5.1/i'     =>  'Windows',
                                '/windows xp/i'         =>  'Windows',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                            );

        foreach ($os_array as $regex => $value) { 

            if (preg_match($regex, $server_data)) {
                $os_platform    =   $value;
            }
        }   

        return $os_platform;
    }
    
    /*
     * Returns an array with [0] name of voices found locally [1] true if there was an error
     * [2] error message
     */
    public function getLocalVoices()
    {
        $user_agent = $this->getOS();
        
        $voices = array();
        $error = false;
        $errormessage = null;
        
        switch ($user_agent) {
                
            case "Mac OS X":
                    
                try {
                    // Llistat de veus
                    $cmdresponse = shell_exec("say --voice=?");

                    // Partim pels espais d'abans de la definició de l'idioma de format xX_xX
                    // fins al salt de línia
                    $voices = preg_split( '/[\s]+..[_-][a-zA-Z]+[\s]+#[^\r\n]*(\r\n|\r|\n)/', $cmdresponse);
                    // eliminem l'últim element que és buit
                    array_pop($voices);
                    
                } catch (Exception $ex) {
                    $error = true;
                    $errormessage = "Error. Unable to access your Mac OS X voices. Try activating your system"
                            . "voices. Otherwise, your OS X may not be compatible with the 'say' command.";
                }
                
                if (!$error && count($voices) < 1) {
                    $error = true;
                    $errormessage = "Error. No installed voices found. Activate your system"
                            . "voices or install external voices for Mac OS X (i.e. Acapela voices).";
                }

                break;
                    
            case "Windows":

                // error de Microsoft Speech Platform
                $errorMSP = false;
                $errorMSPtmp = null;
                
                try {
                    // Recollim els objectes de les llibreries Speech de Microsoft que necessitem
                    $msVoice = new COM('Speech.SpVoice');

                    $numvoices = $msVoice->GetVoices()->Count;

                    // agafem les veus, la descripció la farem servir per buscar els idiomes
                    // de cada una d'elles, idealment són les que s'haurien de llistar
                    // a la interfície de l'usuari
                    for ($i=0; $i<$numvoices; $i++) {
                        $voices[] = $msVoice->GetVoices()->Item($i)->GetDescription;
                    }

                    // DEBUG
                    // print_r($voices);
                    
                } catch (Exception $ex) {
                    $errorMSP = true;
                    $errorMSPtmp = "Error. Unable to access Microsoft Speech Platform.";
                }
                
                // error de SAPI
                $errorSAPI = false;
                $errorSAPItmp = null;
                
                try {
                    // Recollim els objectes de les llibreries SAPI que necessitem

                    $msSAPIVoice = new COM('SAPI.SpVoice');

                    $numvoicesSAPI = $msSAPIVoice->GetVoices()->Count;

                    // agafem les veus, la descripció la farem servir per buscar els idiomes
                    // de cada una d'elles, idealment són les que s'haurien de llistar
                    // a la interfície de l'usuari

                    for ($i=0; $i<$numvoicesSAPI; $i++) {
                        $voices[] = $msSAPIVoice->GetVoices()->Item($i)->GetDescription;
                    }
                    // DEBUG
                    // print_r($voices);
                    
                } catch (Exception $ex) {
                    $errorSAPI = true;
                    $errorSAPItmp = "Error. Unable to access SAPI voices.";
                }
                
                if ($errorMSP && $errorSAPI) {
                    $error = true;
                    $errormessage = "Error. Unable to access your Windows voices. "
                            . "Install Microsoft Speech Platform (MSP) or SAPI voices. Otherwise, "
                            . "your Windows may not be compatible with MSP or SAPI.";
                }
                else if (count($voices) < 1) {
                    $error = true;
                    $errormessage = "Error. No installed voices found. "
                            . "Install Microsoft Speech Platform or SAPI voices.";
                }

                break;
                
            default:
                $error = true;
                $errormessage = "Error. Your OS is not compatible with the offline version of this app. ";
                break;
        }
        
        $output = array(
            0 => $voices,
            1 => $error,
            2 => $errormessage
        );
        
        return $output;
    }

    /** 
     * @param bool $online parameter that says if online voices need to be added
     * (for the Interface voices, there will only be two default online voices for each language)
     * @return array $output Description: $output[0] an array of the available voices for the interface,
     * for each voice we have voiceName and voiceType
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2]
     */
    public function listInterfaceVoices($online) 
    {
        $output = array();
        $output[1] = false;
        $output[2] = null;
        $arrayVoices = array();
        
        if ($online) {
            $arrayVoices = array(
                0 => array(
                    'voiceName' => 'DEFAULT (fem)',
                    'voiceType' => 'online'
                ),
                1 => array(
                    'voiceName' => 'DEFAULT (masc)',
                    'voiceType' => 'online'
                )
            );
        }
                
        // we add the voices in the local CPU if the app is running locally
        if ($this->AppLocalOrServer() == 'local') {
            $auxresponse = $this->getLocalVoices();
            $localvoices = $auxresponse[0];
            $output[1] = $auxresponse[1];
            $output[2] = $auxresponse[2];
            
            for ($i=0; $i<count($localvoices); $i++) {
                $aux = array();
                $aux['voiceName'] = $localvoices[$i];
                $aux['voiceType'] = "offline";
                $arrayVoices[] = $aux;
            }
        }
        
        $output[0] = $arrayVoices;
        
        return $output;
    }
    
    /** 
     * @param bool $online parameter that says if online voices need to be added
     * @return array $output Description: $output[0] an array of the available voices for the interface,
     * for each voice we have voiceName and voiceType
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2]
     */
    public function listExpansionVoices($online)
    {
        $CI = &get_instance();
        $CI->load->model('Audio_model');
        
        $output = array();
        $output[1] = false;
        $output[2] = null;
        $arrayVoices = array();
        
        if ($online) {
            $arrayVoices = $CI->Audio_model->getOnlineVoices(0);
        }
                
        // we add the voices in the local CPU if the app is running locally
        if ($this->AppLocalOrServer() == 'local') {
            $auxresponse = $this->getLocalVoices();
            $localvoices = $auxresponse[0];
            $output[1] = $auxresponse[1];
            $output[2] = $auxresponse[2];
            
            for ($i=0; $i<count($localvoices); $i++) {
                $aux = array();
                $aux['voiceName'] = $localvoices[$i];
                $aux['voiceType'] = "offline";
                $arrayVoices[] = $aux;
            }
        }
        
        $output[0] = $arrayVoices;
        
        return $output;
    }
    
    /**
     * 
     * @param int $idusu Id of the current user
     * @param string $text string to generate audio
     * @param bool $interface TRUE if it's a string for the Interface,
     * FALSE if it's a string that comes from Expansion (MD5 and voices are 
     * treated differently for each of them)
     * @return string Name of the generated audio file
     */
    public function generateAudio($idusu, $text, $interface) 
    {
        $CI = &get_instance();
        $CI->load->model('Audio_model');
        
        $userinfo = $CI->Audio_model->getUserInfo($idusu);
        
        $md5 = "";
        
        
        
        
    }
    
}

/* End of file Myaudio.php */