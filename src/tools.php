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

function affiche_bouton_liste_materiel()
{
	global $db;
	$nb_max_pour_boutons = 10;
	$sql = "SELECT type FROM materiel_list ORDER BY type"; 
	if ($resultat = mysqli_query($db,$sql))
	{	
		if ($resultat->num_rows>$nb_max_pour_boutons)
			$liste_materiel= "<SELECT name='type' size='1'>";	
		else
			$liste_materiel = "";
		
		while($row = mysqli_fetch_assoc($resultat)) 	
		{
			//echo "id: " . $row["id"]."<br>";
			if ($resultat->num_rows>$nb_max_pour_boutons)
				$liste_materiel = $liste_materiel."<OPTION>".$row["type"];
			else
				$liste_materiel = $liste_materiel.'<input class="btn btn-primary" name="type" type="submit" value="'.$row['type'].'"  />&nbsp;&nbsp;';
		}
		if ($resultat->num_rows>$nb_max_pour_boutons)
			$liste_materiel = $liste_materiel."</SELECT>&nbsp;&nbsp;<input name='voir_liste_from_select' type='submit' value='Voir la liste'/>";
		
		// $liste_materiel = $liste_materiel.'
		// <br>
		// <br><input name="type" type="submit" value="Voir la liste complete"  /> <br>(pour la revue du matériel)';
		
		mysqli_free_result($resultat);
		echo '<form name="voir_list" method="post" action="voir_liste.php">';
		echo $liste_materiel;
		echo '</form>';

	}
	else
	{
		info_message_erreur("Aucun tpe de matériel enregistré");
	}
}


function set_loginfo($chaine)
{
	add_log($chaine);
}

function info_message_erreur($message,$add_in_log=true)
{
		echo '<center><h2 style="color:red;background-color:powderblue;">'.$message.'</h2></center>'; 
		if ($add_in_log) add_log($message);
}

function info_message_OK($message,$add_in_log=true)
{
		echo '<center><h2 style="color:green;background-color:powderblue;">'.$message.'</h2></center>'; 
		if ($add_in_log) add_log($message);
}


function message_OK($message)
{
	template_debut_bloc_info();
	echo '
					<h2 class="section-heading mb-4">
					Message:
					</h2>
					<p>
					'.$message.'
					</p>
				'; 
	template_fin_bloc_info();	

}

function message_erreur($message)
{
	template_debut_bloc_info();
	echo '		<h2 class="section-heading mb-4">
					Erreur:
					</h2>
					<p>
					'.$message.'
					</p>'; 
					
	template_fin_bloc_info();
}

function format_if_date($date)
{
	if (check_date($date)==true)
	{
		return $date[8].$date[9].'-'.$date[5].$date[6].'-'.$date[0].$date[1].$date[2].$date[3];
	}
	else
	{
		if (check_date_heure($date)==true)
			return $date[8].$date[9].'-'.$date[5].$date[6].'-'.$date[0].$date[1].$date[2].$date[3].substr($date, 10, -3);
		else
			return $date;
	}
}

function check_date($date)
{
	if (strlen($date) != 10)
		return false;
	
	if ($date[4] != '-')
		return false;

	if ($date[7] != '-')
		return false;
	
	return true;

}

function check_date_heure($date)
{
	if (strlen($date) != 19)
		return false;
	
	if ($date[4] != '-')
		return false;

	if ($date[7] != '-')
		return false;
	
	return true;

}