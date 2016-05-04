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
            $this->load->library('Myaudio');
            // $this->load->library('Mymatching');
            // $this->load->library('Mypatterngroup');
        }

	public function index()
	{
            $expander = new Myexpander();
            $expander->expand();
            $info = $expander->info;
            
            // GENERAR AUDIO
            // $audio = new Myaudio();
            // $aux = $audio->generateAudio($this->session->userdata('idsubuser'), $info['frasefinal'], true);
            
            // DEBUG
            // print_r($aux); echo "<br />";
            
            // si hi ha hagut algun error
            // if ($aux[1]) echo $aux[2];
            
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
                
}
