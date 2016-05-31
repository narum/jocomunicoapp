<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Myaudio {
            
    function __construct() {}
    
    /*
     * RETURNS true if connected or false otherwise
     */
    public function isOnline() 
    {

    }
    
    /*
     * RETURNS 'local' if the App is running locally or 'server' if it's running from the server
     */
    public function AppLocalOrServer()
    {

    }
    
    /*
     * Gets the OS from which the app is running
     */
    public function getOS() { 
        
        
    }
    
    /*
     * Returns an array with [0] name of voices found locally [1] true if there was an error
     * [2] error message [3] error code
     */
    public function getLocalVoices()
    {
        
    }
    /** 
     * @param bool $online parameter that says if online voices need to be added
     * (for the Interface voices, there will only be two default online voices for each language)
     * @return array $output Description: $output[0] an array of the available voices for the interface,
     * for each voice we have voiceName and voiceType
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2] errorcode in $output[3]
     */
    public function listInterfaceVoices($online) 
    {
        
    }
    
    /** 
     * @param bool $online parameter that says if online voices need to be added
     * @return array $output Description: $output[0] an array of the available voices for the interface,
     * for each voice we have voiceName and voiceType
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2] errorcode in $output[3]
     */
    public function listExpansionVoices($online)
    {
        
    }
    
    /**
     * 
     * @param int $idusu Id of the current user
     * @param string $text string to generate audio
     * @param bool $interface TRUE if it's a string for the Interface,
     * FALSE if it's a string that comes from Expansion (MD5 and voices are 
     * treated differently for each of them)
     * @return array $output[0] Name of the generated audio file
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2] errorcode in $output[3]
     */
    public function generateAudio($idusu, $text, $interface) 
    {
        
        
    }
    
    /**
     * 
     * It generates the audio and saves it to the database and into an audio file
     * @param string $md5 filename without the extension
     * @param string $text string to synthesize
     * @param string $voice voice name for offline voices or id for online voices (except 
     * for DEFAULT online interface voices)
     * @param string $type online or offline
     * @param int $language id of the language of the string to synthetize
     * @param type $rate rate of speech speed of offline voices
     * @return array $output[0] Name of the generated audio file with the extension
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2] errorcode in $output[3]
     */
    function synthesizeAudio($md5, $text, $voice, $type, $language, $rate)
    {      
        
    }
    
    /**
     * Requests and saves audio file from online voice service
     * @param type $vocalwareLID
     * @param type $vocalwareVID
     * @param type $text
     * @param type $filename (without extension)
     * @return array $output calling function should check for returned errors in $output[0],
     * errormessage in $output[1] errorcode in $output[2]
     */
    function synthesizeOnline($vocalwareLID, $vocalwareVID, $text, $filename)
    {
        
    }
    
    /**
     * Requests and saves audio file from online voice service
     * @param type $voice
     * @param type $text
     * @param type $filename (without extension)
     * @param type $rate rate of speech speed of offline Mac OS X voices
     * @return array $output calling function should check for returned errors in $output[0],
     * errormessage in $output[1] errorcode in $output[2]
     */
    function synthesizeMacOSX($voice, $text, $filename, $rate)
    {        
        
    }
    
    /**
     * Requests and saves audio file from online voice service
     * @param type $voice
     * @param type $text
     * @param type $filename (without extension)
     * @return array $output calling function should check for returned errors in $output[0],
     * errormessage in $output[1] errorcode in $output[2]
     */
    function synthesizeWindows($voice, $text, $filename)
    {
        
    }
    
    /**
     * 
     * It generates the audio for the dropdown voices' menus in the user configuration of the app 
     * and saves it to the database and into an audio file
     * @param string $idusu user id
     * @param string $text string to synthesize
     * @param string $voice voice name for offline voices or id for online voices (except 
     * for DEFAULT online interface voices)
     * @param string $type online or offline
     * @param int $language id of the language of the string to synthetize
     * @param type $rate rate of speech speed of offline voices
     * @return array $output[0] Name of the generated audio file with the extension
     * NOTE: calling function should check for returned errors in $output[1],
     * errormessage in $output[2] errorcode in $output[3]
     */
    public function selectedVoiceAudio($idusu, $text, $voice, $type, $language, $rate) 
    {
        
    }
    
    /**
     * If there is no error, waits for the file to be available and frees it 
     * @param type $file
     * @param type $error
     */
    public function waitForFile($file, $error)
    {
        
    }
    
}
/* End of file Myaudio.php */