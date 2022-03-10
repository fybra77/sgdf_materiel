<?php 
/**
 * SGDF Matos - Site de Gestion materiel pour les scouts
 * PHP Version 5.6 ou au dessus
 *
 * @see https://github.com/fybra77/sgdf_matos.git The SGDF Matos Website project
 *
 * @author    Franck BRICOUT
 * @copyright 2016 - 2022 Franck BRICOUT
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
 ?>
 <?php 
include 'include.php'; 
include 'header.php'; 
include 'menu.php';

// $_SESSION['comefrom'] = $_SERVER['PHP_SELF'];
// $_SESSION['cur_shortname']
// $_SESSION['cur_type']
//if((check_login($_SERVER['PHP_SELF'])==true) 

$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
mysqli_query($db,"SET NAMES 'utf8'");
// $array_materiel =array();
$shortname = [];
$sql = "SELECT type,shortname FROM materiel_list ORDER BY type"; 
if ($resultat = mysqli_query($db,$sql))
{	
	while($row = mysqli_fetch_assoc($resultat)) 	
		$shortname[$row["type"]] = $row["shortname"];
	mysqli_free_result($resultat);
}
else
{
	info_message_erreur("Aucun type de matériel enregistré");
}	

function add_event($id, $event)
{
	global $db;
	
	if ($event =="")
		return true;
	
	$list_nom_variable = " id, date_evenement, commentaire ";
	$list_valeur_variable = "'".$id."', '".date('Ymd')."','".addslashes($event)."'";

	$sql = "INSERT INTO evenements (".$list_nom_variable.") "."VALUES (".$list_valeur_variable.")"; 
	
	set_loginfo("Ajout Event : ".$list_valeur_variable);
	$result =  mysqli_query($db,$sql);
	return $result;
}

$cur_id = 0;

if (($_POST) && is_user())
{
	$cur_id = $_POST['id'];	
	if($_POST['action'] == "update")
	{
		// récupération des info actuelles pour comparer
		$sql = "SELECT * from materiel WHERE id = '".$cur_id."' AND type='".$_SESSION['cur_type']."'" ; 
		
		if (!($resultat = mysqli_query($db,$sql)))
			info_message_erreur("Erreur le matériel n'exite pas");
		else
			$data_avant = mysqli_fetch_array($resultat);

		$comment_for_event = "";
		$nouvelles_valeurs = "";
		foreach($_POST as $nom => $valeur)
		{	
			if (($nom != "action") && ($nom != "buton"))
			{
				$nouvelles_valeurs = $nouvelles_valeurs." ".$nom." = '".addslashes($valeur)."',";
				if (($valeur != "") && ($valeur != "OK") && ($valeur != "Ok") && ($valeur != "ok"))
				{
					if ($data_avant[$nom] != $valeur)
						$comment_for_event .= $nom." => ".$valeur."<br>";
				}
			}
		}

		$nouvelles_valeurs = substr($nouvelles_valeurs,0,strlen($nouvelles_valeurs)-1);
		
		add_event($_SESSION['cur_shortname'].$_POST['id'], $comment_for_event);
		
		$sql = "UPDATE materiel SET".$nouvelles_valeurs." WHERE id = '".$cur_id."' AND type='".$_SESSION['cur_type']."'" ; 

		if ($resultat = mysqli_query($db,$sql))
		{	
			info_message_OK("La matériel ".$_SESSION['cur_shortname'].$cur_id." a été Modifiée");
		}
		else
		{
			info_message_erreur("Erreur matériel déja existante");
		}
	}
	elseif ($_POST['action'] == "add_event")
	{
		$id_curent = $_SESSION['cur_shortname'].$cur_id;
		$list_nom_variable = " id, date_evenement, commentaire ";
		$list_valeur_variable = "'".$id_curent."', '".date('Ymd')."','".addslashes($_POST['evenement'])."'";
		$sql = "INSERT INTO evenements (".$list_nom_variable.") "."VALUES (".$list_valeur_variable.")"; 
		$result =  mysqli_query($db,$sql);
	
		if ($result)
		{
			info_message_OK("Evènement sur le matériel ".$id_curent." ajouté");
		}
		else
			info_message_erreur("Erreur d'écriture de l'evenement");
	}
}




if(($cur_id>0) || !empty($_GET))
{

	if (isset($_GET["id"])) 
		$cur_id = $_GET['id'];
	
	if (isset($_GET["type"])) 
		$cur_type = $_GET["type"];
	else
		$cur_type = $_SESSION['cur_type'];
		
	if (isset($_SESSION['cur_shortname'])) 
		$cur_shortname = $_SESSION['cur_shortname'];
	else
		$cur_shortname = $shortname[$cur_type];

	
	$sql = "SELECT * FROM materiel WHERE id = '". $cur_id."' AND type='".$cur_type."'"; 
	// echo "<br>".$sql."<br>";
	$resultat = mysqli_query($db,$sql);

	if ($resultat->num_rows == 0)
	{
		header ('Location: '.$_SESSION['comefrom']);
		exit();
	}
		
	$data = mysqli_fetch_array($resultat);

	if ($data['utilisable'] == 'Oui')
	{
		$val_oui = "checked";
		$val_non = "";
	}
	else
	{
		$val_oui = "";
		$val_non = "checked";
	}	

	if ($data['etat_general'] == 'Bon')
	{
		$val_bon= "checked";
		$val_moyen = "";
		$val_mauvais = "";
	}
	else if ($data['etat_general'] == 'Moyen')
	{
		$val_bon= "";
		$val_moyen = "checked";
		$val_mauvais = "";
	}
	else if ($data['etat_general'] == 'Mauvais')
	{
		$val_bon= "";
		$val_moyen = "";
		$val_mauvais = "checked";
	}
	else
	{
		$val_bon= "";
		$val_moyen = "";
		$val_mauvais = "";
	}	


	// require 'phpqrcode.php';
	// $image = "./qrcode/".$_GET["tente"].".png";
	// QRcode::png($base_addr_site."update_tente.php?tente=".$_GET["tente"],$image, QR_ECLEVEL_L, 10,1); // creates code image and outputs it directly into browser
	// print '<center><img src='.$image.' height="10%" alt="texte alternatif" /></center>';


	// if (strpos($_SESSION['comefrom'], "index.php") !== FALSE) 
		$return_button = '
		<form name="return" method="post" action="voir_liste.php">
		<input id="type" name="type" type="hidden" value = "'.$cur_type.'">
		<button type="submit" style="background: none; cursor:pointer; border: 0; " value="box_update"><img title="Retour" src="./assets/retour.png" width=70 height=auto/></button>
		</form>';

	template_debut_bloc_normal();	
	echo $return_button;
	
	echo '
	<center>
	<form name="update_materiel" method="post" action="edit_materiel.php">

		<h2>'.$cur_type.' : '.$cur_shortname.$data['id'].'</h2> 
		<input id="id" name="id" type="hidden" value = "'.$data['id'].'">
		<p>Information diverse<br>
		<input id="info" name="info" type="text" value = "'.$data['info'].'" maxlength="30">
		<br>(ex. nom d\'unité...)<br></p>												
														
		<p>Modèle<br>
		<input id="modele" name="modele" type="text" value = "'.$data['modele'].'" maxlength="256">
		<br>(ex. Tente 4p, Marabout 8m...)<br></p>														
														
		<p>Date de mise en service:<br>
		<input id="date_mise_en_service" name="date_mise_en_service" type="date"  value = "'.$data['date_mise_en_service'].'"></p>												
																										
		<p>Date de vérification:<br>
		<input id="date_verification" name="date_verification" type="date"  value = "'.$data['date_verification'].'"></p>

		<u>Matériel Utilisable ? </u><br>
		<input id="Yes" name="utilisable" value="Oui" '.$val_oui.' type="radio"> 
		<label for="Yes">Oui</label>
		&nbsp;&nbsp;
		<input id="No" name="utilisable" value="Non" type="radio" '.$val_non.'>
		<label for="No">Non</label> 

		<br><br><u>Etat du matériel :</u> <br>

		<input id="Yes" name="etat_general" value="Mauvais" '.$val_mauvais.' type="radio"> 
		<label for="Yes">Mauvais</label>
		&nbsp;&nbsp;
		<input id="No" name="etat_general" value="Moyen" type="radio" '.$val_moyen.'>
		<label for="No">Moyen</label> 
		&nbsp;&nbsp;
		<input id="No" name="etat_general" value="Bon" type="radio" '.$val_bon.'>
		<label for="No">Bon</label> 

		<br><br>

		<legend>Commentaire</legend>
		<textarea style="max-width:300px; width: 100%;" name="commentaire" cols="40" rows="5" >'.$data['commentaire'].'</textarea>';
		if (is_user())	
			echo '<input id="action" name="action" type="hidden" value = "update">
	<br><br><input class="btn btn-primary"  name="buton" value="Mettre a jour" type="submit">';
	
	echo '<td>
	</form><br>
	</center>
	';
	echo $return_button;
	template_fin_bloc_normal();
	

	template_debut_bloc_normal();
	echo $return_button;
	echo '<center>';
	if (is_user())	
	echo '
	
	<form name="ajout_event" method="post" action="edit_materiel.php">
		<input id="id" name="id" type="hidden" value = "'.$data['id'].'">
		<legend>Ajouter un évènement</legend>
		<br>(réparation, sac perdu...)
		<br><textarea style="max-width:300px; width: 100%;" name="evenement" cols="40" rows="5" ></textarea>
		<br>
		<input id="action" name="action" type="hidden" value = "add_event">
		<br><input class="btn btn-primary"  type="submit" value="Ajouter l\'évènement" />
	</form>
	';
	
	$sql = "SELECT * FROM resa WHERE objet='".$cur_shortname.$cur_id."' ORDER BY date_debut_resa DESC, nom_camp, id"; 
	$full_resas= mysqli_query($db,$sql);
	$sql = "SELECT * FROM evenements WHERE id='".$cur_shortname.$cur_id."' ORDER BY date_evenement DESC, num DESC"; 
	$full_events = mysqli_query($db,$sql);
	// echo($full_events['num_rows']);
	if(isset($_POST['action']) && ($_POST['action'] == "full_historique"))
	{
		echo '<br><br><h2>Evènements : </h2>';
		if(mysqli_num_rows($full_events)>0)
		{
			// Generation du tableau
			require('tableau.php');
			echo '
			<table width="100%" style="background-color:#f6e1c5; border-collapse : separate; border-spacing : 1px;" class = "avectri">
			<thead><tr><th data-tri="1" class="selection" data-type="date">Date</th><th>Commentaire</th></tr></thead><tbody>';


			while($ligne = mysqli_fetch_assoc($full_events)) 	
			{
				echo '<tr><td>'.format_if_date($ligne['date_evenement']).'</td>'; 
				echo '<td>'.$ligne['commentaire'].'</td></tr>'; 
				// echo '<u><b>Le '.format_if_date($ligne['date_evenement']).' : </u></b><br>'.$ligne['commentaire'];
				// echo '<br><center>----------------------------------------</center><br><br>';
			}
			echo '</tbody></table>';
		}
		else
			echo 'Aucun évènement enregistré<br>';
		
		echo '<br><h2>Réservations : </h2><br>';
		if (mysqli_num_rows($full_resas)>0)
		{	
			require('tableau.php');
			echo '
			<table width="100%" style="background-color:#f6e1c5; border-collapse : separate; border-spacing : 1px;" class = "avectri">
			<thead><tr><th data-tri="0" class="selection" data-type="date">Debut</th><th data-type="date">Fin</th><th>Camp</th></tr></thead><tbody>';

			while($ligne = mysqli_fetch_assoc($full_resas)) 	
			{
				// echo 'Du '.format_if_date($ligne['date_debut_resa']).' au '.format_if_date($ligne['date_fin_resa']).'<br>Pour le camp :'.$ligne['nom_camp'];
				// echo '<br><center>----------------------------------------</center><br><br>';						
				echo '<tr><td>'.format_if_date($ligne['date_debut_resa']).'</td>'; 
				echo '<td>'.format_if_date($ligne['date_fin_resa']).'</td>'; 
				echo '<td>'.$ligne['nom_camp'].'</td></tr>'; 
			}
				
			// while($ligne = mysqli_fetch_assoc($full_resas)) 	
			// {

			// }
			echo '</tbody></table>';
		}
		else
			echo 'Aucune réservation enregistrée<br>';
		
		if (is_user())
			echo '<form name="voir_historique" method="post" action="edit_materiel.php">
		<br><input id="id" name="id" type="hidden" value = "'.$data['id'].'">
		<input id="action" name="action" type="hidden" value = "none">
		<input class="btn btn-primary"  type="submit" value="Cacher tout l\'historique" />
		<br><br>
		</form>
		
		';
		echo '</center>';
	}
	else
	{
		$last_event = mysqli_fetch_assoc($full_events);
		$last_resa = mysqli_fetch_assoc($full_resas);
		
		echo '<br><u><b>Dernier évènement :';
		if ($last_event)
			echo 'Le '.format_if_date($last_event['date_evenement']).' : </u></b><br>'.$last_event['commentaire'];
		else
			echo '</u></b><br>Aucun évènement enregistré';
		
		echo '<br><br><u><b>Dernière réservation : </u></b><br>';
		if ($last_resa)
			echo 'Du '.format_if_date($last_resa['date_debut_resa']).' au '.format_if_date($last_resa['date_fin_resa']).'<br>Pour le camp :'.$last_resa['nom_camp'];
		else
			echo 'Aucune réservation enregistrée';
		if (is_user())
		echo '<form name="voir_historique" method="post" action="edit_materiel.php">
			<br><input id="id" name="id" type="hidden" value = "'.$data['id'].'">
			<input id="action" name="action" type="hidden" value = "full_historique">
			<input class="btn btn-primary"  type="submit" value="Voir tout l\'historique" />
			<br><br>
			</form>
			
		';
		echo '</center>';
	}
	echo $return_button;
	template_fin_bloc_normal();

	template_debut_bloc_normal();
	echo '
	<center>
	<legend>Voir les listes</legend><br><br>';
	affiche_bouton_liste_materiel();
	echo '</center>';
	template_fin_bloc_normal();
	
}

sqli_close($db);
?> 

<?php

include 'footer.php'; 
?>	