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

?>

<?php template_debut_bloc_normal(0); ?>
<center>
<br>
<h3>
	Liste des messages reçus
</h3>
<br>

<?php
	if (isset($_POST['action']) && ($_POST['action']=="voir_plus"))
		$limit_nb_message = 200;
	else
		$limit_nb_message = 30;
		
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
	require('tableau.php');
	echo '<div style="width:100%;height:100%;overflow:scroll;overflow-y: hidden;">
	<table style="border-collapse : separate; border-spacing : 1px;"  class = "avectri" >
	<thead><tr><th data-tri="0" class="selection" data-type="date">Date</th><th>Nom</th><th>Matériel</th><th>Message</th></tr></thead><tbody>';
	$sql = "SELECT date,name,materiel,message FROM demande ORDER BY date DESC LIMIT ".$limit_nb_message; 
	// echo $sql;
	if ($resultat = mysqli_query($db,$sql))
	{	
		$ligne_presente = false;
		while($ligne = mysqli_fetch_assoc($resultat)) 	
		{
			$ligne_presente = true;
		echo '<tr>';

					foreach($ligne as $valeur) {
							echo '<td align="left">'.nl2br(format_if_date($valeur)).'</td>'; 
					}
		echo '</tr>';
		}
		echo '</tbody></table></div>';
		if ($ligne_presente==false)
			echo "<br><h3> Vous n'avez encore aucun message</h3><br>";
	}
	else
	{
		echo "<font color='red'> <br>Erreur recupérations des messages<br></font>";
	}
	sqli_close($db);
	
	
?>
	<br><form name="return" method="post" action="messages.php">
	<input id="action" name="action" type="hidden" value = "voir_plus">
	<input class="btn btn-primary" type="submit" value="Voir plus de messages" />
	</form><br>
<?php template_fin_bloc_normal(); ?>
				

</center>
<?php

include 'footer.php'; 
?>		

