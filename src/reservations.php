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

$_SESSION['comefrom'] = $_SERVER['PHP_SELF'];

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

// info_message_erreur($shortname['Tente']);

function print_resultat($db, $resutat_sql, $nom)
{
	global $shortname;
	if ($resutat_sql)
	{	
		$materiel_trouve = false;
		while($ligne = mysqli_fetch_assoc($resutat_sql)) 	
		{
			$sql = "SELECT * FROM resa WHERE objet = '".$shortname[$nom].$ligne['id']."' AND 
																		(
																			(date_debut_resa <= '".$_POST['date_debut']."' AND date_fin_resa >= '".$_POST['date_debut']."')
																			OR 
																			(date_debut_resa <= '".$_POST['date_fin']."' AND date_fin_resa >= '".$_POST['date_fin']."')
																			OR
																			(date_debut_resa >= '".$_POST['date_debut']."' AND date_fin_resa < '".$_POST['date_fin']."')
																		);";
			
			$data = mysqli_query($db,$sql);
			echo mysqli_error($db);
			if(is_bool($data) === false)
			{
				if ((mysqli_num_rows($data) == 0) AND ($ligne['utilisable']=='Oui') )
				{
					$materiel_trouve = true;
					// pas de reservation en cours
					echo '<tr>';
						echo '<td style="vertical-align: top;"><input type="checkbox" id="'.$shortname[$nom].$ligne['id'].'" name="'.$shortname[$nom].$ligne['id'].'"/><td>';
						echo '<td style="vertical-align: top;">'.$nom."&nbsp".$ligne['id'].'<td>';
						$info_disp = "";
						$modele_disp = "";
						if(array_key_exists('info',$ligne))
							$info_disp = $ligne['info'];
						if(array_key_exists('modele',$ligne))
							$modele_disp = $ligne['modele'];
							
						echo '<td>';
						
						if($info_disp!="")
							echo ': '.$info_disp;
						
						if($modele_disp!="")
						{
							if($info_disp!="")
								echo ' / '.$modele_disp;
							else
								echo ': '.$modele_disp;
						}
						
						echo '</td>';

							
					echo '</tr>';
				}
			}
			else
				echo '<br><br> BOOLEEN = '.$data.'<br>';
		}
		if ($materiel_trouve == false)
			echo 'Pour '.$nom.' : aucun matériel disponible';
	}
	else
	{
		message_erreur("Aucune tente n'est enregistrée");
	}
}

if(check_login($_SERVER['PHP_SELF'])==true)
{
	$array_materiel =array();
	$liste_materiel= "<SELECT name='type' size='1'>";	
	$sql = "SELECT type FROM materiel_list ORDER BY type"; 
	if ($resultat = mysqli_query($db,$sql))
	{	
		while($row = mysqli_fetch_assoc($resultat)) 	
		{
			$liste_materiel = $liste_materiel."<OPTION>".$row["type"];
			array_push($array_materiel, $row["type"]);
		}
		$liste_materiel = $liste_materiel."</SELECT>";
		mysqli_free_result($resultat);
	}
	else
	{
		info_message_erreur("Aucun type de matériel enregistré");
	}	


	
	if ($_POST)
	{
		if(isset($_POST['voir_resa']))
		{
			template_debut_bloc_info(1);
			if ($_POST['date_debut'] != "")
			{
				// info_message_erreur("La date de début n'a pas été rentrée");
				if ($_POST['date_fin'] == "")
					info_message_erreur("La date de fin n'a pas été rentrée");	
				else if ($_POST['date_debut'] > $_POST['date_fin'])
					info_message_erreur("La date de fin est plus petite que la date de début !!!!!");
			}

			$liste_deroulante = "<SELECT name='resa' size='1'>";
		
			$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = 'resa' AND table_schema='sgdf';"	;
			// echo $sql;
			if ($resultat = mysqli_query($db,$sql))
			{	
				require('tableau.php');
				echo '<center><form name="suppression" method="post" action="reservations.php">';
				echo '<div style="width:100%;height:100%;overflow:scroll;overflow-y: hidden;"><table width="100%"; style="border-collapse : separate; border-spacing : 1px;"  class = "avectri" >';
				echo '<thead><tr><th></th><th>Matériel</th><th data-type="date">Date de Début</th><th data-type="date">Date de fin</th><th data-tri="1" class="selection" >Nom du camp</th></tr></thead><tbody>';
				while($ligne = mysqli_fetch_assoc($resultat)) 	
				{
						foreach($ligne as $valeur) {
							echo '<td>'.$valeur.'</td>'; }
				}
			}
			else
			{
				info_message_erreur('Erreur tente déja existante ou erreur dans le format de la date');
			}
			
			if ($_POST['date_debut'] != '')
			{
				$sql = "SELECT * FROM resa WHERE  
						(
							(date_debut_resa <= '".$_POST['date_debut']."' AND date_fin_resa >= '".$_POST['date_debut']."')
							OR 
							(date_debut_resa <= '".$_POST['date_fin']."' AND date_fin_resa >= '".$_POST['date_fin']."')
							OR
							(date_debut_resa >= '".$_POST['date_debut']."' AND date_fin_resa < '".$_POST['date_fin']."')
						);"; 
			}
			else
			{
				$sql = "SELECT * FROM resa ORDER BY date_debut_resa DESC, length(objet), objet;"; 
			}
				
			// $sql = "INSERT INTO tente (id, nb_place) VALUES ('T', 2)"; 
			// $sql = "INSERT INTO tente (id, nb_place, modele) VALUES ('T5', '2', 'tit')"; 
			// echo $sql;
			if ($resultat = mysqli_query($db,$sql))
			{	
				while($ligne = mysqli_fetch_assoc($resultat)) 	
				{
				echo '<tr>';
							// id	objet	date_debut_resa	date_fin_resa	nom_camp
							echo '<td><input type="checkbox" id="'.$ligne['id'].'" name="'.$ligne['id'].'"/></td>';
							// echo '<td>'.$ligne['id'].'</td>';
							echo '<td>'.$ligne['objet'].'</td>';
							echo '<td>'.format_if_date($ligne['date_debut_resa']).'</td>';
							echo '<td>'.format_if_date($ligne['date_fin_resa']).'</td>';
							echo '<td>'.$ligne['nom_camp'].'</td>';		
				echo '</tr>';
				}
				echo '</tbody></table><div>';
				if (is_admin())
					echo '<BR><input class="btn btn-primary" name="supprimer_reservation" value="Supprimer" type="submit" onclick="if(!confirm(\'ATTENTION : Voulez-vous vraiment SUPPRIMER ?\')) return false;">';
				
				echo '</form>';
			}
			else
			{
				echo mysqli_error($db);
			}

			// echo '<br><input type="button" value="Retour" onClick="javascript:document.location.href=\'index.php\'" /> <br><br> </center>';
			// echo '<br><br>';
			template_fin_bloc_info();
		}
		elseif(isset($_POST['faire_resa']))
		{
			if ($_POST['date_debut'] == "")
				info_message_erreur("La date de début n'a pas été rentrée");
			else if ($_POST['date_fin'] == "")
				info_message_erreur("La date de fin n'a pas été rentrée");	
			else if ($_POST['date_debut'] > $_POST['date_fin'])
				info_message_erreur("La date de fin est plus petite que la date de début !!!!!");
			else
			{
				template_debut_bloc_info();
				// Liste du matériel disponible
				echo '<form name="reservation" method="post" action="reservations.php">
				
				<input type="text" name="date_debut_resa" id="date_debut_resa" style="display:none" value = "'.$_POST['date_debut'].'"/>
				<input type="text" name="date_fin_resa" id="date_fin_resa" style="display:none" value = "'.$_POST['date_fin'].'"/>
				<br>
				';
				echo 'Pour la période du '.format_if_date($_POST['date_debut']).' au '.format_if_date($_POST['date_fin']);
				
				
				foreach($array_materiel as $materiel)
				{
					echo '<br><h2>'.$materiel.' disponible</h2>';
					echo '<center><table width:100%; style="border-collapse : separate; border-spacing : 1px;">';
					$sql = "SELECT id,info,modele,utilisable FROM materiel WHERE type='".$materiel."' ORDER BY length(id),id"; 
					$materiel_dispo = mysqli_query($db,$sql);
					print_resultat($db,$materiel_dispo, $materiel);
					echo '</table><center>';
				}

				
				echo '	<br>Nom du camps / WE : <input type="text" name="nom_camp" id="nom_camp" required/><br><br>
					<input class="btn btn-primary" name="finaliser_reservation" value="Réserver" type="submit">
				</form>';		
				template_fin_bloc_info();
			}

		}
		elseif(isset($_POST['finaliser_reservation']))
		{
			$liste_chainee = "";
			$resa_OK = true;
			foreach($_POST as $nom => $valeur)
			{
				$list_nom_variable = "date_debut_resa,date_fin_resa,nom_camp,";
				$list_valeur_variable = "'".$_POST['date_debut_resa']."', '".$_POST['date_fin_resa']."', '".addslashes($_POST['nom_camp'])."' ,";
				
				if(($valeur == "on") AND ($nom != 'nom_camp'))
				{
					$list_nom_variable = $list_nom_variable."objet";
					$list_valeur_variable = $list_valeur_variable."'".$nom."'";

					$sql = "INSERT INTO resa (".$list_nom_variable.") "."VALUES (".$list_valeur_variable.")"; 
					$liste_chainee = $liste_chainee." ".$list_valeur_variable."\r\n";

					if (!($resultat = mysqli_query($db,$sql)))
						$resa_OK = false;
				}
				
			}
			
			if ($resa_OK == true)
				info_message_OK('Le matériel a été reservé <br>du '.format_if_date($_POST['date_debut_resa']).' au '.format_if_date($_POST['date_fin_resa']));
			else
				info_message_erreur("Erreur lors de la réservation");

		}
		elseif(isset($_POST['supprimer_reservation']))
		{
			$liste_chainee = "";
			$suppr_OK = true;
			foreach($_POST as $nom => $valeur)
			{
				if($valeur == "on") 	
				{
					$sql = "DELETE FROM resa WHERE id="."'".$nom."'"; 
					if (!mysqli_query($db,$sql))
						$suppr_OK = false;
				}
			}
			
			if($suppr_OK == true)
				info_message_OK('Suppression de la réservation');
			else
				info_message_erreur('Erreur lors de la suppression de la réservation');
		}
	}
?>
<?php template_debut_bloc_normal(); ?>
	<center>
	<h3>
	Faire une réservation
	</h3>
	<form name="reservation" method="post" action="reservations.php">
		<p>Date de début:<br>
		<input id="date_debut" name="date_debut" type="date"></p>												
																										
		<p>Date de fin:<br>
		<input id="date_fin" name="date_fin" type="date"></p>
		<input class="btn btn-primary" name = "voir_resa" value="Consulter les réservations" type="submit">
		<br><br><input class="btn btn-primary" name = "faire_resa" value="Faire une Réservation" type="submit">
	</form>
	</center>
<?php template_fin_bloc_normal(); ?>
				

<?php
} //check_login

include 'footer.php'; 
?>		

