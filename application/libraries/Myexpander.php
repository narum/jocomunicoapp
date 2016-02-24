<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Myexpander {
    
    
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
    var $info = array();
        
    function __construct() {}
    
    public function expand()
    {
        $CI = &get_instance();
        $CI->load->model('Lexicon');

        $this->info['errormessage'] = null; // Missatges d'error o warnings
        $this->info['error'] = false; // Si el missatge és d'error
        $this->info['printparsepattern'] = "No pattern found.";

        // GET SENTENCE
        $idusu = $CI->session->userdata('idusu');
        $this->paraulescopia = $CI->Lexicon->getLastSentence($idusu); // array amb les paraules
        $propietatsfrase = $CI->Lexicon->getLastSentenceProperties($idusu);

        if ($this->paraulescopia == null) $this->info['errormessage'] = "Error. No hi ha cap frase per aquest usuari.";

        // FIND VERBS
        $arrayVerbs = array();

        for ($i=0; $i<count($this->paraulescopia); $i++) {
            $word = &$this->paraulescopia[$i];
            if ($word->tipus == "verb") $arrayVerbs[] = &$word;
        }

        // GET PATTERNS

        $this->initialiseVerbPatterns($arrayVerbs, $propietatsfrase);
        // $verbPatterns = new Mypatterngroup();            
        // $verbPatterns.initialise($arrayVerbs);

        if ($this->errormessagetemp != null) {
            $this->info['errormessage'] = $this->errormessagetemp;
            $this->info['error'] = true;

            return;
        }

        else {

            $partpreguntaposada = false;
            // Per cada PATTERN
            for ($i=0; $i<count($this->allpatterns); $i++) {

                // fem una còpia de les paraules per treballar des de 0 a cada pattern
                $paraules = unserialize(serialize($this->paraulescopia));
                $partPregunta = array();
                $arrayNouns = array();
                $arrayAdjs = array();
                $arrayAdvs = array();
                $arrayModifs = array();
                $arrayExpressions = array();

                for ($j=0; $j<count($paraules); $j++) {
                    $word2 = &$paraules[$j];
                    if ($word2->tipus == "name") $arrayNouns[] = &$word2;
                    else if ($word2->tipus == "adj") {
                        if ($word2->isClass("numero")) $arrayModifs[] = &$word2;
                        else $arrayAdjs[] = &$word2; // els ordinals hi segueixen sent, vigilar amb els slots tipus ordinal
                    }
                    else if ($word2->tipus == "adv") $arrayAdvs[] = &$word2;
                    else if ($word2->tipus == "expression") $arrayExpressions[] = &$word2;
                    else if ($word2->tipus == "modifier") $arrayModifs[] = &$word2;
                    else if ($word2->tipus == "questpart") $partPregunta[] = &$word2;
                }

               $auxpattern = &$this->allpatterns[$i];
               $auxpattern->paraules = $paraules;

               // Posem les expressions a la tira d'expressions
               for ($j=0; $j<count($arrayExpressions); $j++) {
                   $auxpattern->exprsarray[] = $arrayExpressions[$j]->text;
               }

               // Si hi ha una partícula de pregunta
               $numpreguntes = count($partPregunta);
               if ($numpreguntes > 1) {
                   $this->errormessagetemp = "Error. La frase no pot contenir més d'una pregunta.";

                   $this->info['errormessage'] = $this->errormessagetemp;
                   $this->info['error'] = true;

                   return;
               }
               else if ($numpreguntes == 1) {
                   $partpreguntaposada = $auxpattern->fillPartPregunta($partPregunta[0]);

                   if (!$partpreguntaposada) $this->errormessagetemp = "Warning. No s'ha trobat lloc per la partícula
                                                                    de la pregunta.";
               } // Fi tractament de pregunta


               // Si el verb és pseudoimpersonal o si hi ha una pregunta, invertim les preferències
               // d'aparèxier abans i després del verb, ja que ara el subjecte va darrere del verb
               if ($auxpattern->pseudoimpersonal || $partpreguntaposada) {
                   for ($j=0; $j<count($paraules); $j++) {
                       $auxword = &$paraules[$j];
                       $auxword->beforeverb = !$auxword->beforeverb;
                   }
               }

               // echo "Noms: ".count($arrayNouns)."<br />";
               // Els noms
               $auxpattern->solveNouns($arrayNouns);
               // $auxpattern->printPattern();


               // echo "Adverbis: ".count($arrayAdvs)."<br />";
               // Els adverbis
               $auxpattern->solveAdverbs($arrayAdvs);
               // $auxpattern->printPattern();


               // echo "Adjectius: ".count($arrayAdjs)."<br />";
               // Els adjectius menys els numerals que els passarem amb la resta d'adjs i modificadors
               $auxpattern->solveAdjs($arrayAdjs);
               // $auxpattern->printPattern();


               // echo "Modificadors: ".count($arrayModifs)."<br />";
               // Els modificadors
               $auxpattern->solveModifs($arrayModifs);

               $puntspattern = $auxpattern->calcPuntsFinalPattern();

               $this->puntsallpatterns[] = $puntspattern;

               $this->errormessage[] = $this->errormessagetemp;
               $this->preguntaposada[] = $partpreguntaposada;
               // DEBUG
               // echo $auxpattern->printPattern();

            }

            // escollim el pattern amb més puntuació com a resultat
            $bestpatternindex = 0;
            $bestpatternpunts = -1000;

            for ($i=0; $i<count($this->puntsallpatterns); $i++) {
                
                // PER VEURE LES PUNTUACIONS DE TOTS ELS PATRONS QUE HA PROVAT 
                // echo "Patró ".$i.": ".$this->puntsallpatterns[$i]." </br ><br />";
                
                if ($this->puntsallpatterns[$i] > $bestpatternpunts) {
                    $bestpatternpunts = $this->puntsallpatterns[$i];
                    $bestpatternindex = $i;
                }
            }

            $bestpattern = new Mypattern();
            $bestpattern = unserialize(serialize($this->allpatterns[$bestpatternindex]));

            // agafem l'string amb el parse tree del pattern escollir
            $printparsepattern = $bestpattern->printPattern();

            $this->info['printparsepattern'] = $printparsepattern;

            // CRIDEM EL GENERADOR AMB EL MILLOR PATTERN, LES PROPIETATS DE LA FRASE SELECCIONADA
            // I SI HI HA UNA PARTÍCULA DE PREGUNTA

            $userlanguage = $CI->session->userdata('ulangabbr');
            $frasefinal = "";

            if ($userlanguage == "CA") {
                $frasefinal = $this->generateSentence($bestpattern, $propietatsfrase, $this->preguntaposada[$bestpatternindex]);
            } else if ($userlanguage == "ES") {
                $frasefinal = $this->generateSentenceES($bestpattern, $propietatsfrase, $this->preguntaposada[$bestpatternindex]);
            }

            $this->info['frasefinal'] = $frasefinal;

            // Guardar parse tree i frase final a la base de dades
            $CI->Lexicon->guardarParseIFraseResultat($propietatsfrase['identry'], $printparsepattern, $frasefinal);

            $this->info['identry'] = $propietatsfrase['identry'];
            $this->info['inputwords'] = $propietatsfrase['inputwords'];
            $this->info['errormessage'] = $this->errormessage[$bestpatternindex];

            // MOSTREM LA INTERFÍCIE
            return;                
        }
    }



    // INICIALITZA TOTS ELS PATTERNS POSSIBLES I ELS POSA A L'ARRAY ALLPATTERNS
    function initialiseVerbPatterns($arrayVerbs, $propietatsfrase)
    {   
        $CI = &get_instance();
        $CI->load->model('Lexicon');
        
        $numverbs = count($arrayVerbs);

        $auxword = new Myword();
        $auxpattern = new Mypattern();

        if ($numverbs > 2) {
            $this->allpatterns = null;
            $this->errormessagetemp = "Error. Hi ha més de dos verbs a la frase. <br />
                                El sistema actual no pot generar frases d'aquesta mena.";
            return; // En aquest cas ja hauríem acabat
        }

        else if ($numverbs == 0) {
            // Agafem els verbless patterns
            $arrayVerbs[] = $CI->Lexicon->getPatternsVerb(0); // Verbless

            // si no és una resposta afegir també els patterns de ser i estar
            if ($propietatsfrase['tipusfrase'] != "resposta") {
                $arrayVerbs[] = $CI->Lexicon->getPatternsVerb(100); // Estar
                $arrayVerbs[] = $CI->Lexicon->getPatternsVerb(86); // Ser
            }

            // Per cada paraula
            for ($i=0; $i<count($arrayVerbs); $i++) {

                $auxword = &$arrayVerbs[$i]; // paraules passades per referència

                // Treiem els patterns de la paraula
                foreach ($auxword->patterns as $pattern) {

                    $auxpattern = new Mypattern();
                    $auxpattern->initialise($pattern); // inicialitzem el pattern

                    // Omplim el main verb
                    $auxpattern->forceFillSlot("Main Verb", $auxword, 0, 0);

                    $this->allpatterns[] = $auxpattern; // Posem el pattern al llistat de possibles patterns
                }
            }
            return; // En aquest cas ja hauríem acabat
        }

        else if ($numverbs == 1) {

            $auxword = &$arrayVerbs[0];

            foreach ($auxword->patterns as $pattern) {

                // menys els que eren de subverb
                if ($pattern->subverb == '0') {
                    $auxpattern = new Mypattern();
                    $auxpattern->initialise($pattern);

                    $auxpattern->forceFillSlot("Main Verb", $auxword, 0, 0);

                    $this->allpatterns[] = $auxpattern;
                }
            }
            return; // En aquest cas ja hauríem acabat
        }

        else if ($numverbs == 2) {

            $auxword = &$arrayVerbs[0];
            $auxword2 = new Myword();
            $auxword2 = &$arrayVerbs[1];

            $subverbfound = false;

            // Per cada pattern del 1er verb
            foreach ($auxword->patterns as $pattern) {

                if ($pattern->subverb == '1') { // Si el pattern accepta subverb

                    $auxpattern = new Mypattern();
                    $auxpattern->initialise($pattern);

                    // Posar a dins els patterns del segon verb que no accepten subverb
                    foreach ($auxword2->patterns as $pattern2) {

                        if ($pattern2->subverb == '0') {

                            $subverbfound = true;

                            $auxpattern2 = new Mypattern();
                            $auxpattern2->initialise($pattern2);

                            $auxpatternfusion = new Mypattern();
                            $auxpatternfusion = unserialize(serialize($auxpattern));

                            $auxpatternfusion->fusePatterns($auxpattern2);

                            // FER ELS FILLS DELS SLOTS DELS VERBS
                            $auxpatternfusion->forceFillSlot("Main Verb 1", $auxword, 0, 0);
                            $auxpatternfusion->forceFillSlot("Secondary Verb 2", $auxword2, 0, 0);

                            $this->allpatterns[] = $auxpatternfusion;
                        }
                    }

                }
            }

            if (!$subverbfound) { // si el primer verb no podia ser el principal

                // Per cada pattern del 2on verb
                foreach ($auxword2->patterns as $pattern2) {

                    if ($pattern2->subverb == '1') { // Si el pattern accepta subverb

                        $auxpattern2 = new Mypattern();
                        $auxpattern2->initialise($pattern2);

                        // Posar a dins els patterns del segon verb que no accepten subverb
                        foreach ($auxword->patterns as $pattern) {

                            if ($pattern->subverb == '0') {

                                $subverbfound = true;

                                $auxpattern = new Mypattern();
                                $auxpattern->initialise($pattern);

                                $auxpatternfusion = new Mypattern();
                                $auxpatternfusion = unserialize(serialize($auxpattern2));

                                $auxpatternfusion->fusePatterns($auxpattern);

                                // FER ELS FILLS DELS SLOTS DELS VERBS
                                $auxpatternfusion->forceFillSlot("Main Verb 1", $auxword2, 0, 0);
                                $auxpatternfusion->forceFillSlot("Secondary Verb 2", $auxword, 0, 0);

                                $this->allpatterns[] = $auxpatternfusion;
                            }
                        }
                    }
                }
            }
            if (!$subverbfound) $this->errormessagetemp = "Error. No s'ha trobat cap patró
                                                        possible amb aquests verbs.";
        } // Fi if ($numverbs == 2)
    }


    function generateSentence($patternfinal, $propietatsfrase, $partpreguntaposada)
    {
        $pattern = new Mypattern();
        $pattern = $patternfinal;
        
        // Indiquem que si el temps per defecte és l'imperatiu, que la frase és una ordre
        // a no ser que estigui activat el modificador de desig o permís que tenen preferència o
        // que hi hagi una partícula de pregunta.
        if ($propietatsfrase['tense'] == "defecte" && $pattern->defaulttense == "imperatiu"
                && (!$propietatsfrase['tipusfrase'] == "desig" || !$propietatsfrase['tipusfrase'] == "permis"
                || !$partpreguntaposada)) {
            $propietatsfrase['tipusfrase'] = "ordre";
        }
        else if ($partpreguntaposada) $propietatsfrase['tipusfrase'] = "pregunta";
        
        // Si el tipus de frase triat era per defecte, ara el tipus de frase és el per defecte del patró
        if ($propietatsfrase['tipusfrase'] == "defecte") $propietatsfrase['tipusfrase'] = $pattern->tipusfrase;

        // 1. Ordenem els slots segons el tipus de frase
        $pattern->ordenarSlotsFrase($propietatsfrase);

        // 2 i 3. 2: Ordenar paraules de dins dels slots, ja posant les preposicions.
        // 3: Controlar que les paraules concordin en gènere i número (els adjs amb els noms 
        // i la PartPregunta "quant" amb el theme, si hi és). Afegir també les coordinacions
        // només de NOMS, ADJECTIUS i ADVERBIS DE MANERA
        $pattern->ordenarSlotsInternament();

        // 4. Posar articles als noms

        $pattern->putArticlesToNouns($propietatsfrase["tipusfrase"]);

        // 5. Conjugar els verbs

        $pattern->conjugarVerbs($propietatsfrase);

        // 6. Treure els "jo" i "tu" dels subjectes. Canviar receivers a pronoms febles i posar-los
        // a darrere el verb si cal. Posar modificadors de frase com el "no" o el "també".
        // Fusionar preposicions amb articles (de+el/s = del/s... a+el, per+el...). Posar apòstrofs 
        // de preps i pronoms febles (i guions?). Netejar espais abans o després dels apòstrofs.
        // Escriure la frase final, posant les expressions i altres advs de temps al final.

        $pattern->launchCleaner($propietatsfrase["tipusfrase"]);

        return $pattern->printFraseFinal();
    }

    function generateSentenceES($patternfinal, $propietatsfrase, $partpreguntaposada)
    {
        $pattern = new Mypattern();
        $pattern = $patternfinal;

        // Indiquem que si el temps per defecte és l'imperatiu, que la frase és una ordre
        // a no ser que estigui activat el modificador de desig o permís que tenen preferència o
        // que hi hagi una partícula de pregunta.
        if ($propietatsfrase['tense'] == "defecte" && $pattern->defaulttense == "imperatiu"
                && (!$propietatsfrase['tipusfrase'] == "desig" || !$propietatsfrase['tipusfrase'] == "permis"
                || !$partpreguntaposada)) {
            $propietatsfrase['tipusfrase'] = "ordre";
        }
        else if ($partpreguntaposada) $propietatsfrase['tipusfrase'] = "pregunta";
        
        // Si el tipus de frase triat era per defecte, ara el tipus de frase és el per defecte del patró
        if ($propietatsfrase['tipusfrase'] == "defecte") $propietatsfrase['tipusfrase'] = $pattern->tipusfrase;

        // 1. Ordenem els slots segons el tipus de frase
        $pattern->ordenarSlotsFraseES($propietatsfrase);

        // 2 i 3. 2: Ordenar paraules de dins dels slots, ja posant les preposicions.
        // 3: Controlar que les paraules concordin en gènere i número (els adjs amb els noms 
        // i la PartPregunta "quant" amb el theme, si hi és). Afegir també les coordinacions
        // només de NOMS, ADJECTIUS i ADVERBIS DE MANERA
        $pattern->ordenarSlotsInternamentES();

        // 4. Posar articles als noms

        $pattern->putArticlesToNounsES($propietatsfrase["tipusfrase"]);

        // 5. Conjugar els verbs

        $pattern->conjugarVerbsES($propietatsfrase);

        // 6. Treure els "jo" i "tu" dels subjectes. Canviar receivers a pronoms febles i posar-los
        // a darrere el verb si cal. Posar accents a les noves formes verbals, si cal.
        // Posar modificadors de frase com el "no" o el "també".
        // Fusionar preposicions amb articles (de+el/s = del/s... a+el...).
        // Escriure la frase final, posant les expressions i altres advs de temps al final.

        $pattern->launchCleanerES($propietatsfrase["tipusfrase"]);

        return $pattern->printFraseFinal();
    }
    
}

/* End of file Mypatterngroup.php */