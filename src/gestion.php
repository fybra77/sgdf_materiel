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

if((check_admin($_SERVER['PHP_SELF'])==true))
{
?>
<?php
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
	

	
	if (isset($_POST['action']))
	{
		if($_POST['action'] == 'ajout_type_materiel')
		{
			$sql = "SELECT id FROM materiel_list WHERE type='".$_POST['type']."'"; 
			// echo $sql."<br>";
			$resultat = mysqli_query($db,$sql);
			if ($resultat->num_rows > 0)
			{	
				info_message_erreur("Type de materiel déjà présent");
				
			}
			else
			{
				$sql = "SELECT id FROM materiel_list WHERE shortname='".$_POST['shortname']."'"; 
				// echo $sql."<br>";
				$resultat = mysqli_query($db,$sql);
				if ($resultat->num_rows > 0)
				{	
					info_message_erreur("Nom court déjà utilisé");
				}
				else
				{
					// ajout du  nouveau type de matériel
					$sql = "INSERT INTO materiel_list (type, shortname) VALUES ('".$_POST['type']."', '".$_POST['shortname']."')"; 
					$resultat = mysqli_query($db,$sql);
					info_message_OK("Matériel Ajouté");
				}
			}
		
		}
		elseif($_POST['action'] == 'ajout_materiel')
		{
			// Détermination du numéro de tente à utiliser
			// ATTENTION penser à detecter les trous dans le ca de la supresion d'un matéiel
			$nom_trouve  = false;
			$new_num = 1;
			while($nom_trouve == false)
			{	
				$sql = "SELECT id FROM materiel WHERE type='".$_POST['type']."' AND id='".$new_num."'"; 
				// echo $sql."<br>";
				$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
				mysqli_query($db,"SET NAMES 'utf8'");
				$resultat = mysqli_query($db,$sql);

				// echo $new_num."<br>";
				if ($resultat->num_rows > 0)
				{	
					$new_num++;
				}
				else
				{
					$nom_trouve  = true;
				}
				
				if ($new_num == 9999)
				{
					$nom_trouve  = true;
					$new_num = "0";
				}
			}			
			
			$sql = "SELECT shortname FROM materiel_list WHERE type='".$_POST['type']."'"; 
			$resultat = mysqli_query($db,$sql);
			$row = mysqli_fetch_assoc($resultat);
			$shortname = $row["shortname"];
			
			$list_nom_variable = "id, shortname, ";
			$list_valeur_variable = "'".$new_num."' ,'".$shortname."' ,";
			foreach($_POST as $nom => $valeur)
			{	
				if(($valeur != "") and ($nom  != "action") and ($nom  != "bouton"))
				{
					$list_nom_variable = $list_nom_variable.$nom.",";
					$list_valeur_variable = $list_valeur_variable."'".addslashes($valeur)."' ,";
				}
			}
			$list_nom_variable = substr($list_nom_variable,0,strlen($list_nom_variable)-1);
			$list_valeur_variable = substr($list_valeur_variable,0,strlen($list_valeur_variable)-1);
			$sql = "INSERT INTO materiel (".$list_nom_variable.") "."VALUES (".$list_valeur_variable.")"; 

			if ($resultat = mysqli_query($db,$sql))
			{	
				info_message_OK($_POST['type']." (". $shortname.$new_num.") a été ajoutée");
			}
			else
			{
				info_message_erreur("Erreur tente déja existante");
			}
		
		}
		elseif($_POST['action'] == 'supprimer_materiel') 
		{
			$sql = "DELETE FROM `materiel` WHERE id = '".$_POST['id']."' AND type='".$_POST['type']."'"; 
			// echo "<br>".$sql."<br>";
			$resultat = mysqli_query($db,$sql);
			
			if ($resultat)
			{
				info_message_OK($_POST['type']." n°".$_POST['id']." a été supprimé(e)");
			}
			else
			{
				info_message_erreur("Erreur dans la suppression de ".$_POST['shortname'].$_POST['id']);
			}	
		}
			elseif($_POST['action'] == 'supprimer_type_materiel') 
		{
			$sql = "DELETE FROM `materiel` WHERE type='".$_POST['type']."'"; 
			$resultat = mysqli_query($db,$sql);
			$sql = "DELETE FROM `materiel_list` WHERE type='".$_POST['type']."'"; 
			$resultat = mysqli_query($db,$sql);
			
			if ($resultat)
			{
				info_message_OK($_POST['type']." a été supprimé(e)");
			}
			else
			{
				info_message_erreur("Erreur dans la suppression de ".$_POST['type']);
			}	
		}	
		
		
	}
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	$array_materiel =array();

	$liste_materiel= "<SELECT name='type' size='1'>";	
	$sql = "SELECT type FROM materiel_list ORDER BY type"; 
	if ($resultat = mysqli_query($db,$sql))
	{	
		while($row = mysqli_fetch_assoc($resultat)) 	
		{
			//echo "id: " . $row["id"]."<br>";
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
	


?>

	<?php template_debut_bloc_normal(); ?>
		<center>
		<h3>
			Ajout d'un nouveau matériel
		</h3>
		<p>
			<form name="gestion" method="post" action="gestion.php">

			<p>Type de matériel<br>
			<?php echo $liste_materiel;?>
			<p>Information diverse<br>(ex. Compagnon groupe n°1...)<br>
			<input id="info" name="info" type="text" maxlength="30"></p>												
															
			<p>Modèle<br>(ex. nombre de places, taille du marabout...)<br>
			<input id="modele" name="modele" type="text" value = "" maxlength="256"></p>												
															
			<p>Date de mise en service:<br>
			<input id="date_mise_en_service" name="date_mise_en_service" type="date"></p>												
																											
			<p>Date de vérification:<br>
			<input id="date_verification" name="date_verification" type="date"></p>


			<u><b>Matériel utilisable ? </u></b><br>
			<input id="Yes" name="utilisable" value="Oui" checked="checked" type="radio"> 
			<label for="Yes">Oui</label>
			&nbsp;&nbsp;
			<input id="No" name="utilisable" value="Non" type="radio">
			<label for="No">Non</label> 

			<br><br>
			<u><b>Etat du matériel : </u></b><br>
			<input id="Yes" name="etat_general" value="Mauvais" type="radio"> 
			<label for="Yes">Mauvais</label>
			&nbsp;&nbsp;
			<input id="No" name="etat_general" value="Moyen" type="radio">
			<label for="No">Moyen</label> 
			&nbsp;&nbsp;
			<input id="No" name="etat_general" value="Bon" checked="checked" type="radio" >
			<label for="No">Bon</label> 

			<br><br>

				<legend>Commentaire</legend>
				<textarea style="width: 100%;" name="commentaire" cols="40" rows="5"></textarea>

			<br>
			<input id="action" name="action" type="hidden" value = "ajout_materiel">
			<input class="btn btn-primary" name="bouton" value="Ajouter le matériel" type="submit">
			
			</form>
		</p>
		</center>
	<?php template_fin_bloc_normal(); ?>

	<?php template_debut_bloc_normal(); ?>
		<center>
		<h3>
			Ajout d'un nouveau type de matériel
		</h3>
	
		<p>
			<form action="gestion.php" method="post">
				Type de matériel (ex. tente, marabout...) : <input type="text" name="type" required>
				<br><br>Nom court (ex. T pour Tente, M pour Marabout...) : <input type="text" name="shortname" required>
				<br>=> Le nom court est l'identifiant que vous mettrez sur votre matériel
				<br><input id="action" name="action" type="hidden" value = "ajout_type_materiel">
				<br><input class="btn btn-primary" type="submit" name="bouton" value="Ajouter le nouveau type de matériel">
			</form>	
		</p>
		</center>
	<?php template_fin_bloc_normal(); ?>
	
	<?php template_debut_bloc_normal(); ?>
		<center>
		<h3>
			Suppression de matériel
		</h3>

		<br>
	
		<?php
		foreach($array_materiel as $materiel)
		{
			$sql = "SELECT id,shortname FROM materiel WHERE type='".$materiel."' ORDER BY  length(id),id"; 
			if ($resultat = mysqli_query($db,$sql))
			{	
				$liste_deroulante = "<SELECT name='id' size='1'>";	
				while($row = mysqli_fetch_assoc($resultat)) 	
				{
					//echo "id: " . $row["id"]."<br>";
					$liste_deroulante = $liste_deroulante."<OPTION>".$row["id"];
				}
				$liste_deroulante = $liste_deroulante."</SELECT>";
				mysqli_free_result($resultat);
			}
			echo "<br>";
		?>
	
			<form method="post" action="gestion.php">
				<?php echo $materiel.' '.$liste_deroulante; ?>
				&nbsp;
				<input id="action" name="action" type="hidden" value = "supprimer_materiel">
				<input id="type" name="type" type="hidden" value = "<?php echo $materiel;?>">
				<input class="btn btn-primary" value="Supprimer" type="submit" onclick="if(!confirm('ATTENTION : Voulez-vous vraiment SUPPRIMER ?')) return false;">
			</form>

		<?php 
		}	
		?>
		</center>
	<?php template_fin_bloc_normal(); ?>

	<?php template_debut_bloc_normal(); ?>
		<center>
		<h3>
			Suppression d'un type de matériel
		</h3>

		<br>
	
			<form method="post" action="gestion.php">
				<?php echo $liste_materiel; ?>
				&nbsp;
				<input id="action" name="action" type="hidden" value = "supprimer_type_materiel">
				<input class="btn btn-primary" value="Supprimer" type="submit" onclick="if(!confirm('ATTENTION : Voulez-vous vraiment SUPPRIMER tout le matériel associé?')) return false;">
			</form>

		</center>
	<?php template_fin_bloc_normal(); ?>	

<?php
} //check_login
sqli_close();
include 'footer.php'; 
?>		

