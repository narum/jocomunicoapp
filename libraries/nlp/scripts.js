function afegirParaula(base_url, nomllistat, taula) {

        var form = $('form-frase');
        var llistat = form[nomllistat];
        var wordid = $(llistat).getValue();

        var url = base_url+"frase/afegirParaula";
        var data = "idparaula="+wordid+"&taula="+taula;

        new Ajax.Updater ('contenidor-frase', url, {method: 'post', postBody: data, evalScripts: true});
}

function afegirModifNom(base_url, modif) {

        var url = base_url+"frase/afegirModifNom";
        var data = "modif="+modif;

        new Ajax.Updater ('contenidor-frase', url, {method: 'post', postBody: data, evalScripts: true});
}

function eliminarParaula(base_url, identry) {

        var url = base_url+"frase/eliminarParaula";
        var data = "identry="+identry;

        new Ajax.Updater ('contenidor-frase', url, {method: 'post', postBody: data, evalScripts: true});
}

function updateConditionTree(base_url, path, nommenu) {
        
        var form = $('form-frase');
        var llistat = form[nommenu];
        var value = $(llistat).getValue();
        
        var newpath = path+value+" ";
        
        var url = base_url+"enternouns/updateConditionTree";
        var data = "path="+newpath;

        new Ajax.Updater ('conditions-menu', url, {method: 'post', postBody: data, evalScripts: true});
}

function hiHaFrase(base_url) {

        var url = base_url+"inputfitxer/hiHaFrase";
        var timer = document.getElementById('timer').innerHTML;
        timer = parseInt(timer);
        var data = "timer="+timer;
        
        new Ajax.Request (url, {method: 'post', postBody: data, evalScripts: true, 
            onComplete: function(dumptruck) {
                var json = dumptruck.responseText.evalJSON(true);
                $('timer').update(json.first);
                if (json.second !== "") {
                    $('frasegenerada').update(json.second);
                }
                esperar(base_url);}
        });
                
}

function esperar(base_url) {
    setTimeout(function() {hiHaFrase(base_url)},1000);
}