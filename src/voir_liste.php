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

if((check_login($_SERVER['PHP_SELF'])==true) || ( (isset($_SESSION['comefromexternal'])) && ($_SESSION['comefromexternal']==true)) )
{
	$return_button = 
	'<button type="submit" style="background: none; cursor:pointer; border: 0; " onClick="javascript:document.location.href=\''.$_SESSION['comefrom'].'\'" /> <img title="Retour" src="./assets/retour.png" width=70 height=auto/></button>';
	template_debut_bloc_normal(0);
	// echo '<section class="page-section clearfix">
		// <div class="about-heading-content">
		// <div class="row">
		// <div class="col-xl-9 col-lg-10 mx-auto" > ';
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
	
	echo $return_button.'<br> <center>';

	if ($_POST['type'] != "Voir la liste complete")
	{
		// Récupération du short name
		$sql = "SELECT shortname FROM materiel_list WHERE type='".$_POST['type']."'"; 
		$resultat = mysqli_query($db,$sql);
		$row = mysqli_fetch_assoc($resultat);
		$_SESSION['cur_shortname'] = $row["shortname"];
		$_SESSION['cur_type'] = $_POST['type'];

		// Generation du tableau
		require('tableau_materiel.php');
		echo '<h3 style="color:black";>'.$_SESSION['cur_type'].'</h3>';
		echo '<div style="width:100%;height:100%;overflow:scroll;overflow-y: hidden;">
		<table style="background-color:#f6e1c5; border-collapse : separate; border-spacing : 1px;" class = "avectri">
		<thead>
			<tr>
				<th data-tri="1" class="selection" >Num</th>
				<th>Info</th>
				<th>Modele</th>
				<th>Commentaire</th>
				<th>Etat</th>
				<th>Date de verification</th>
				<th>Date mise en service</th>
				<th>Dispo.</th>
			</tr>
		</thead><tbody>';
		$sql = "SELECT id,info,modele,commentaire,etat_general,date_verification,date_mise_en_service,utilisable FROM materiel WHERE type='".$_POST['type']."' ORDER BY length(id),id"; 

		if ($resultat = mysqli_query($db,$sql))
		{	
			// echo '<table border=7>';
			while($ligne = mysqli_fetch_assoc($resultat)) 	
			{
				echo '<tr>';
					
				$first_value = true;
				foreach($ligne as $valeur) 
				{
					if ($first_value == true)
					{
						echo '<td> <a href="edit_materiel.php?id='.$valeur.'">'.$_SESSION['cur_shortname'].$valeur.'<a/></td>';
						$first_value = false;
					}
					else						
						echo '<td>'.format_if_date($valeur).'</td>'; 
				}		
			
				echo '</tr>';
			}
			echo '</tbody></table></div>';
		}
		else
		{
			echo "<font color='red'> <br>Erreur tente déja existante ou erreur dans le format de la date<br></font>";
		}
	}

	sqli_close($db);

	echo '</center><br>'.$return_button.'<br> ';
	// echo '</div></div></div></section>';
	template_fin_bloc_normal();
	?>
	
<?php
} //check_login

include 'footer.php'; 
?>		