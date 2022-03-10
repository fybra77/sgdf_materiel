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

function add_log($event)
{
	global $db, $serveur, $login_db, $pwd_db, $db_name,$sqli_connected ;
	if ($event != "")
	{
		$save_sqli_connected = $sqli_connected;

		if($save_sqli_connected==false)
		{
			$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
			mysqli_query($db,"SET NAMES 'utf8'");
		}
		
		$sql = "SELECT * FROM log";
		$resultat = mysqli_query($db,$sql);	
		$nb_row = mysqli_num_rows($resultat	);
		mysqli_free_result($resultat);
		
		if ($nb_row > 1000)
			keep_last_log(500);
		
		$sql = "INSERT INTO log (event) VALUES ('".addslashes($event)."')"; 
		$resultat = mysqli_query($db,$sql);	
		
		if($save_sqli_connected==false)
			sqli_close($db);	
		
		if ($resultat)
			return true;		
		else
			return false;		
	}
	return true;
}

function clear_log()
{
	global $db;
	mysqli_query($db,"SET NAMES 'utf8'");
	$sql = "TRUNCATE TABLE `log`"; 
	$resultat = mysqli_query($db,$sql);
	if ($resultat)
		return true;		
	else
		return false;	
}

function keep_last_log($nb_log_to_keep)
{
	global $db;
	mysqli_query($db,"SET NAMES 'utf8'");	
	$sql = "DELETE FROM log WHERE id NOT IN (SELECT * FROM (SELECT id FROM `log` ORDER BY id DESC LIMIT ".$nb_log_to_keep.") temp)"; 
	$resultat = mysqli_query($db,$sql);
	if ($resultat)
		return true;		
	else
		return false;		
}

function show_log($number_of_records)
{
	global $db;
	require('tableau.php');
	echo '<center><table style="border-collapse : separate; border-spacing : 1px;"  class = "avectri" ><thead><tr><th th data-tri="0" class="selection" data-type="date">Date</th><th>Evenement</th></tr></thead>';
	$sql = "SELECT date,event FROM log ORDER BY date DESC LIMIT ".$number_of_records; 
	// echo $sql;

	if ($resultat = mysqli_query($db,$sql))
	{	
		// echo '<table border=7>';
		while($ligne = mysqli_fetch_assoc($resultat)) 	
		{
		echo '<tr>';

					foreach($ligne as $valeur) {
							echo '<td align="left">'.nl2br(format_if_date($valeur)).'</td>'; 
					}
		echo '</tr>';
		}
		echo '</table></center>';
		return true;
	}
	else
	{
		return false;
	}
}

?>
