<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Myaudio {
            
    function __construct() {}
    
    public function isOnline() 
    {
        $is_conn = null;
        $connected1 = @fsockopen("www.example.com", 80); //website, port  (try 80 or 443)
        $connected2 = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected1 || $connected2){
            $is_conn = true; //action when connected
            fclose($connected1);
            fclose($connected2);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }
    
    public function AppLocalOrServer()
    {
        return !preg_match('/localhost/i', $subject);
    }
    
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
    
}

/* End of file Myaudio.php */