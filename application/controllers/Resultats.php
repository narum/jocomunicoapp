<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resultats extends CI_Controller {
    
    var $allpatterns = array(); // Array amb tots els patterns possibles per una entrada amb un o varis verbs
    /* Un pattern té un array d'slots (cada slot té les propietats més un array de paraules que l'estan omplint)
     * més un booleà que diu si ja està ple.
     */
    var $puntsallpatterns = array(); // array amb els punts finals de cada pattern
    var $patternescollit = 0; // id del pattern dins de l'array allpatterns
    
    var $errormessagetemp = null; 
    var $errormessage = array(); 
    var $preguntaposada = array();
    var $paraulescopia = array();

	public function __construct()
        {
            parent::__construct();

            $this->load->model('Lexicon');
            $this->load->library('Myword');
            $this->load->library('Myslot');
            $this->load->library('Mypattern');
            $this->load->library('Myexpander');
            // $this->load->library('Mymatching');
            // $this->load->library('Mypatterngroup');
        }

	public function index()
	{
            $expander = new Myexpander();
            $expander->expand();
            $info = $expander->info;
            
            // si el sistema operatiu és un Mac
            $server_data = $_SERVER['HTTP_USER_AGENT'];
            $user_agent = $this->getOS($server_data);
            
            $language = $this->session->userdata('ulangabbr');
            $isfem = $this->session->userdata('isfem');
            
            $isconnected = $this->is_connected();
            
            // DEBUG
            // if ($isconnected) echo "GREAT!!";
            // else echo "OOOHHH!";
            
            switch ($user_agent) {
                case "Mac OS X":
                    
                    $concatveus = "";
                                        
                    switch ($language) {
                        case "CA":
                            if ($isfem) $concatveus = "-r 190 -v Laia ";
                            else $concatveus = "-r 190 -v Laia ";
                            break;
                            
                        case "ES":
                            if ($isfem) $concatveus = "-r 165 -v Monica ";
                            else $concatveus = "-r 180 -v Jorge ";
                            break;
                            
                        case "EN":
                            if ($isfem) $concatveus = "-r 220 -v Vicki ";
                            else $concatveus = "-r 220 -v Alex ";
                            break;

                        default:
                            if ($isfem) $concatveus = "-r 165 -v Monica ";
                            else $concatveus = "-r 180 -v Jorge ";
                            break;
                    }
                    
                    // $concatoutput = "-o mp3/filename.m4a ";
                    
                    $cmd='say '.$concatveus.'"'.$info['frasefinal'].'" > /dev/null 2>&1 &';
                    // $cmd='say '.$concatveus.$concatoutput.'"'.$info['frasefinal'].'" > /dev/null 2>&1 &';
                    shell_exec($cmd);
                    break;

                default:
                    
                    if ($isconnected) {
                        
                        $info['connected'] = true;
                        
                        $curl = curl_init();

                        $url = "http://www.vocalware.com/tts/gen.php";
                        $secret_phrase = "5a823f715692c02de9e215fef94c5dc2";

                        $data = array(
                            'EID' => '2',
                            'LID' => '2',
                            'VID' => '6',
                            'TXT' => $info['frasefinal'],
                            'EXT' => 'mp3',
                            'ACC' => '5795433',
                            'API' => '2490514'                    
                        );

                        switch ($language) {
                            case "CA":
                                $data['LID'] = '5';
                                if ($isfem) $data['VID'] = '1';
                                else $data['VID'] = '2';
                                break;

                            case "ES":
                                $data['LID'] = '2';
                                if ($isfem) $data['VID'] = '1';
                                else $data['VID'] = '6';
                                break;

                            case "EN":
                                $data['LID'] = '1';
                                if ($isfem) $data['VID'] = '1';
                                else $data['VID'] = '2';
                                break;

                            default:
                                break;
                        }

                        $data['CS'] = md5($data['EID'].$data['LID'].$data['VID'].$data['TXT'].$data['EXT'].$data['ACC'].$data['API'].$secret_phrase);

                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                        $result = curl_exec($curl);

                        curl_close($curl);

                        // si no hi ha hagut cap error
                        if (!strpos($result, "Error: ")) {

                            $filenamewrite = "mp3/".$data['CS'].".mp3";
                            $fitxertxtwrite = fopen($filenamewrite,"w+b");

                            if (flock($fitxertxtwrite, LOCK_EX)) {
                                fwrite($fitxertxtwrite, $result);
                                flock($fitxertxtwrite, LOCK_UN);
                                fclose($fitxertxtwrite);
                            }

                            $info['voiceerror'] = false;
                        }
                        else $info['voiceerror'] = true;
                    }
                    else {
                        $info['connected'] = false;
                    }
                                        
                    break;
            }
                        
            $this->load->view('resultats', $info); 
            
	}
        
        public function gracies()
        {
            $identry = $this->input->post('identry', true);
            $scoreparser = $this->input->post('scoreparser', true);
            $scoregen = $this->input->post('scoregen', true);
            $comments = $this->input->post('comments', true);

            $this->Lexicon->addEntryScores($identry, $scoreparser, $scoregen, $comments);

            $this->load->view('gracies');
        }
        
        function getOS($user_agent) { 

            $os_platform    =   "Unknown OS Platform";

            $os_array       =   array(
                                    '/windows nt 10/i'     =>  'Windows 10',
                                    '/windows nt 6.3/i'     =>  'Windows 8.1',
                                    '/windows nt 6.2/i'     =>  'Windows 8',
                                    '/windows nt 6.1/i'     =>  'Windows 7',
                                    '/windows nt 6.0/i'     =>  'Windows Vista',
                                    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                    '/windows nt 5.1/i'     =>  'Windows XP',
                                    '/windows xp/i'         =>  'Windows XP',
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

                if (preg_match($regex, $user_agent)) {
                    $os_platform    =   $value;
                }
            }   

            return $os_platform;
        }
        
        function is_connected()
        {
            $connected = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
            if ($connected){
                $is_conn = true; //action when connected
                fclose($connected);
            }else{
                $is_conn = false; //action in connection failure
            }
            return $is_conn;
        }
        
}
