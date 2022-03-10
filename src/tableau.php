<style>
/* Classe obligatoire pour les flèches */
.flecheDesc {
  width: 0; 
  height: 0; 
  float:right;
  margin: 10px;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-bottom: 5px solid black;
}
.flecheAsc {
  width: 0; 
  height: 0;
  float:right;
  margin: 10px;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid black;
}

/* Classe optionnelle pour le style */
.tableau {width:100%;table-layout: auto;width:100%;  border-collapse: separate;  border: 5px solid #000;}
.tableau td {padding:.3rem}
.zebre tbody tr:nth-child(odd) {background-color: #d6d3ce;border-bottom:1px solid #ccc;color:#444;}
.zebre tbody tr:nth-child(even) {background-color: #c6c3bd;border-bottom:1px solid #ccc;color:#444;}
.zebre tbody tr:hover:nth-child(odd) {background-color: #e6a756;color:#ffffff;}
.zebre tbody tr:hover:nth-child(even) {background-color: #e6a756;color:#ffffff;}
.avectri th {text-align:center;padding:5px 0 0 5px;vertical-align: middle;background-color:#e6a756;color:#444;cursor:pointer;
	-webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -o-user-select: none;
  user-select: none;
}
.avectri th.selection {background-color:#fbb55b;color:#fff;}
.avectri th.selection .flecheDesc {border-bottom-color: white;}
.avectri th.selection .flecheAsc {border-top-color: white;}
.zebre tbody td:nth-child(3) {text-align:center;}
</style>

<script>
  // Tri dynamique de tableau HTML
  // Auteur : Copyright © 2015 - Django Blais
  // Source : http://trucsweb.com/tutoriels/Javascript/tableau-tri/
  // Sous licence du MIT.
  function twInitTableau() {
    // Initialise chaque tableau de classe « avectri »
       [].forEach.call( document.getElementsByClassName("avectri"), function(oTableau) {
       var oEntete = oTableau.getElementsByTagName("tr")[0];
       var nI = 1;
  	  // Ajoute à chaque entête (th) la capture du clic
  	  // Un picto flèche, et deux variable data-*
  	  // - Le sens du tri (0 ou 1)
  	  // - Le numéro de la colonne
      [].forEach.call( oEntete.querySelectorAll("th"), function(oTh) {
        oTh.addEventListener("click", twTriTableau, false);
        oTh.setAttribute("data-pos", nI);
        if(oTh.getAttribute("data-tri")=="1") {
         oTh.innerHTML += "<span class=\"flecheDesc\"></span>";
        } else {
          oTh.setAttribute("data-tri", "0");
          oTh.innerHTML += "<span class=\"flecheAsc\"></span>";
        }
        // Tri par défaut
        if (oTh.className=="selection") {
          oTh.click();
        }
        nI++;
      });
    });
  }
  
  function twInit() {
    twInitTableau();
  }
  function twPret(maFonction) {
    if (document.readyState != "loading"){
      maFonction();
    } else {
      document.addEventListener("DOMContentLoaded", maFonction);
    }
  }
  twPret(twInit);

  function twTriTableau() {
    // Ajuste le tri
    var nBoolDir = this.getAttribute("data-tri");
    this.setAttribute("data-tri", (nBoolDir=="0") ? "1" : "0");
    // Supprime la classe « selection » de chaque colonne.
    [].forEach.call( this.parentNode.querySelectorAll("th"), function(oTh) {oTh.classList.remove("selection");});
    // Ajoute la classe « selection » à la colonne cliquée.
    this.className = "selection";
    // Ajuste la flèche
    this.querySelector("span").className = (nBoolDir=="0") ? "flecheAsc" : "flecheDesc";

    // Construit la matrice
    // Récupère le tableau (tbody)
    var oTbody = this.parentNode.parentNode.parentNode.getElementsByTagName("tbody")[0]; 
    var oLigne = oTbody.rows;
    var nNbrLigne = oLigne.length;
    var aColonne = new Array(), i, j, oCel;
    for(i = 0; i < nNbrLigne; i++) {
      oCel = oLigne[i].cells;
      aColonne[i] = new Array();
      for(j = 0; j < oCel.length; j++){
        aColonne[i][j] = oCel[j].innerHTML;
      }
    }

    // Trier la matrice (array)
    // Récupère le numéro de la colonne
    var nIndex = this.getAttribute("data-pos");
    // Récupère le type de tri (numérique ou par défaut « local »)
    var sFonctionTri = "none";
	if (this.getAttribute("data-type")=="num")
		sFonctionTri = "compareNombres";
	else if(this.getAttribute("data-type")=="date")
		sFonctionTri = "compareDate";
	else
		sFonctionTri = "compareLocale";
    // Tri
    aColonne.sort(eval(sFonctionTri));
    // Tri numérique
    function compareNombres(a, b) {return a[nIndex-1] - b[nIndex-1];}
	function convert_date(date_france)
	{
		var date_l = "";
		if (date_france.length<16)
			date_l = date_france.slice(6,10) +'-'+ date_france.slice(3,5) +'-'+ date_france.slice(0,2);
		else 
			date_l = date_france.slice(6,10) +'-'+ date_france.slice(3,5) +'-'+ date_france.slice(0,2) + date_france.slice(10);
		return date_l;
	}
	function compareDate(a, b) 
	{
		var date_a = new Date(convert_date(a[nIndex-1])).getTime();
		var date_b = new Date(convert_date(b[nIndex-1])).getTime();
		return date_a > date_b ? 1 : -1;
	}
    // Tri local (pour support utf-8)
    function compareLocale(a, b) 
	{
	//return a[nIndex-1].localeCompare(b[nIndex-1]);
		var reA = /[^a-zA-Z]/g;
		var reN = /[^0-9]/g;
		var aA = a[nIndex-1].replace(reA, "");
		var bA = b[nIndex-1].replace(reA, "");
		if (aA === bA) 
		{
			var aN = parseInt(a[nIndex-1].replace(reN, ""), 10);
			var bN = parseInt(b[nIndex-1].replace(reN, ""), 10);
			return aN === bN ? 0 : aN > bN ? 1 : -1;
		} 
		else 
		{
			return aA > bA ? 1 : -1;
		}
	}
    // Renverse la matrice dans le cas d’un tri descendant
    if (nBoolDir==0) aColonne.reverse();
    
    // Construit les colonne du nouveau tableau
    for(i = 0; i < nNbrLigne; i++){
      aColonne[i] = "<td style='background-color:#efc389;'>"+aColonne[i].join("</td><td style='background-color:#efc389;'>")+"</td>";
    }

    // assigne les lignes au tableau
    oTbody.innerHTML = "<tr align='center'>"+aColonne.join("</tr><tr align='center'>")+"</tr>";
  }
</script>