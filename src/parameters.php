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
include './email/email_configuration.php';

$_SESSION['comefrom'] = $_SERVER['PHP_SELF'];
$init_base_ok = true;
if(check_login($_SERVER['PHP_SELF'])==true)
{
	if (isset($_POST['action']))
	{
		if($_POST['action'] == 'update_configuration')
		{
			// tester             
			// set_error_handler(function($niveau, $message, $fichier, $ligne){
                // echo 'Erreur : ' .$message. '<br>';
                // echo 'Niveau de l\'erreur : ' .$niveau. '<br>';
                // echo 'Erreur dans le fichier : ' .$fichier. '<br>';
                // echo 'Emplacement de l\'erreur : ' .$ligne. '<br>';
				
            // });
			 // $mysqli_connection = new MySQLi($_POST['serveur'], $_POST['login'], $_POST['SQLmdp'], $_POST['dbname']);
			// if($mysqli_connection->connect_error)
				// info_message_erreur("Erreur connection");
			// else
				// info_message_OK("Connection OK");
			// exit();
			try
			{		
				// error_reporting(E_ALL);
				$mysqli_connection = new MySQLi($_POST['serveur'], $_POST['login'], $_POST['SQLmdp'], $_POST['dbname']);
				if ($mysqli_connection->connect_error) {
				   info_message_erreur("Mauvaise configuration, essayez encore !");
				   $init_base_ok = false;
				}
				else
					$mysqli_connection->close();			
			}
			catch(Exception $e)
			{
				info_message_erreur("Mauvaise configuration, essayez encore !");
				$init_base_ok = false;
			}			

			if ($init_base_ok == true)
			{
				$serveur = $_POST['serveur'];
				$login_db = $_POST['login'];
				$pwd_db = $_POST['SQLmdp'];
				$db_name = $_POST['dbname'];
			
			
				$fichier_content = "<?php
\$serveur = '".$_POST['serveur']."';
\$login_db = '".$_POST['login']."';
\$pwd_db = '".$_POST['SQLmdp']."';
\$db_name = '".$_POST['dbname']."';
?>";
				$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name); 
				mysqli_query($db,"SET NAMES 'utf8'");
				
				$file = fopen("./configuration.php", "w");
				fwrite($file,$fichier_content);
				fclose($file);
				
				info_message_OK("Configuration mise à jour !");
			}

		}
		else
		{
			$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
			mysqli_query($db,"SET NAMES 'utf8'");
			if($_POST['action'] == 'delete_all')
			{
				// Force the reinstallation
				$fichier_content = "<?php
	\$serveur = '';
	\$login_db = '';
	\$pwd_db = '';
	\$db_name = '';
?>";
				$file = fopen("./configuration.php", "w");
				fwrite($file,$fichier_content);
				fclose($file);
					
				$fichier_content = "<?php
	\$email_from = '';
	\$email_to = '';
	\$email_contact = '';
?>";
				$file = fopen("./email/email_configuration.php", "w");
				fwrite($file,$fichier_content);
				fclose($file);
				//header_remove();
				session_destroy();
				//header('Location: ./index.php');
				echo "<script type='text/javascript'>document.location.replace('./index.php');</script>";
				exit();
			}
			elseif($_POST['action'] == 'update_email')
			{
				$fichier_content = "<?php
	\$email_from = '".$_POST['from']."';
	\$email_to = '".$_POST['to']."';
	\$email_contact = '".$_POST['contact']."';
?>";
				$email_from = $_POST['from'];
				$email_to = $_POST['to'];
				$email_contact = $_POST['contact'];
				$file = fopen("./email/email_configuration.php", "w");
				fwrite($file,$fichier_content);
				fclose($file);
				info_message_OK("Configuration mise à jour !");
			}
			elseif($_POST['action'] == 'mot_de_passe')
			{
				if ($_POST['sgdfmdp'] == "")
				{
					info_message_erreur("Le mot de passe ne peut pas être vide");
				}
				else
				{
					$sql = "UPDATE membre SET pass_hashed = '".password_hash($salt.$_POST['sgdfmdp'],PASSWORD_BCRYPT)."' WHERE login='".$_POST['user']."'"; 
					if ($resultat = mysqli_query($db,$sql))
						info_message_OK("Mot de passe modifié !");		
					else
						info_message_erreur("Echec modification du mot de passe");
				}
			}
			elseif($_POST['action'] == 'ajout_utilisateur')
			{
				if ($_POST['sgdfmdp'] == "")
				{
					info_message_erreur("Le mot de passe ne peut pas être vide");
				}
				else
				{
					$sql = "INSERT INTO membre (pass_hashed, login) VALUES ('".password_hash($salt.$_POST['sgdfmdp'],PASSWORD_BCRYPT)."', '".$_POST['username']."')"; 
					if ($resultat = mysqli_query($db,$sql))
						info_message_OK("Utilisateur ajouté !");		
					else
						info_message_erreur("Echec ajout d'un utilisateur");
				}
			}			
			elseif($_POST['action'] == 'suppr_utilisateur')
			{
				$sql = "DELETE FROM `membre` WHERE login='".$_POST['user']."'"; 
				if ($resultat = mysqli_query($db,$sql))
					info_message_OK("Utilisateur supprimé !");		
				else
					info_message_erreur("Echec supression d'un utilisateur");
			}
			elseif($_POST['action'] == 'voir_log')
			{	
				template_debut_bloc_info(); 
				show_log(200);
				template_fin_bloc_info();
			}		
			elseif($_POST['action'] == 'keep_last_log')
			{	
				if(keep_last_log(200))
					info_message_OK("Effacement des anciens logs effectué !");
				else
					info_message_erreur("Erreur dans l'effacement");
			}				
			elseif($_POST['action'] == 'clear_log')
			{	
				if(clear_log())
					info_message_OK("Historique effacé !");
				else
					info_message_erreur("Erreur dans l'effacement de l'historique");
			}							
		
			
			sqli_close($db);
		}		
	}
			
?>


<?php 
if ((!($_POST))||( ($_POST) && ($_POST['action'] != 'update_configuration_FAIL') ))
{
	
	template_debut_bloc_normal(); 
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
	$array_users =array();
	$liste_users= "<SELECT name='user' size='1'>";	
	$liste_all_users= "<SELECT name='user' size='1'>";	

	if (is_admin())
	{
		$sql = "SELECT login FROM membre ORDER BY login"; 
		if ($resultat = mysqli_query($db,$sql))
		{	
			while($row = mysqli_fetch_assoc($resultat)) 	
			{
				if ($row["login"] != "admin")
				{
					//echo "id: " . $row["id"]."<br>";
					$liste_users = $liste_users."<OPTION>".$row["login"];
					$liste_all_users = $liste_all_users."<OPTION>".$row["login"];
					array_push($array_users, $row["login"]);
				}
				else
					$liste_all_users = $liste_all_users."<OPTION>".$row["login"];
			}
			$liste_users = $liste_users."</SELECT>";
			$liste_all_users = $liste_all_users."</SELECT>";
			mysqli_free_result($resultat);
		}
		else
		{
			info_message_erreur("Aucun utilisateur enregistré");
		}	
	}
	else
	{
		$liste_all_users = $liste_all_users."<OPTION>".$_SESSION['login']."</OPTION></SELECT><br>";
	}


?>
<center>
<?php if (is_admin()) { ?>	
<h3>Gestion utilisateurs</h3>
<?php } else { ?>
<h3>Modifer votre mot de passe</h3>
<?php } ?>
	<form name="mot_de_passe_admin" method="post" action="parameters.php">
	<?php if (is_admin()) { ?>	
		<p><u><b>Changer un mot de passe :</b></u><br>
	<?php } ?>		
		<?php echo $liste_all_users;?>
		<br><input id="sgdfmdp" name="sgdfmdp" type="password" value = "" maxlength="256">	
		<input id="action" name="action" type="hidden" value = "mot_de_passe">
		<?php if (!is_admin()) {echo '<br>';}  ?><br><input class="btn btn-primary" name="bouton" value="Mettre à jour" type="submit">
		</p>
	</form>	
	<?php if (!is_admin()) {echo '<br>';}  ?>
<?php 
if (is_admin()) 
{
?>	
<br>
	<form name="ajout_utilisateur" method="post" action="parameters.php">
		<p><u><b>Ajouter un utilisateur : </u></b><br>
		Login :<br><input id="username" name="username" type="text" value = "" maxlength="256">
		<br>Mot de pase :<br><input id="sgdfmdp" name="sgdfmdp" type="password" value = "" maxlength="256">	
		<input id="action" name="action" type="hidden" value = "ajout_utilisateur">
		<br><input class="btn btn-primary" name="bouton" value="Mettre à jour" type="submit">
	</form>	
	<br>	
	<form name="suppr_utilisateur" method="post" action="parameters.php">
		<p><u><b>Supprimer un utilisateur :</b></u> <br>
		Login :<br><?php echo $liste_users;?>
		<input id="action" name="action" type="hidden" value = "suppr_utilisateur">
		<br><input class="btn btn-primary" name="bouton" value="Supprimer" type="submit" onClick="if(!confirm('ATTENTION : Vous allez supprimer définitivement utilisateur !')) return false;">
	</form>	
<?php
}
?>
</center>			


<?php 
template_fin_bloc_normal(); 
}

?>

<?php if (is_admin()) 
{
?>
<?php template_debut_bloc_normal(); ?>
<center>
	<h3>
		Configuration des Emails
	</h3>
	<form name="gestion" method="post" action="parameters.php">

	<p>Email expediteur (from): <br>
	<input name="from" type="email" value = "<?php echo $email_from;?>" maxlength="256"></p>												
													
	<p>Email du destinataire (to): <br>
	<input name="to" type="email" value = "<?php echo $email_to;?>"  maxlength="256"></p>												

	<br>
	<p>Email pour le support: <br>
	<input name="contact" type="email" value = "<?php echo $email_contact;?>"  maxlength="256"></p>												
	<br>
	<input id="action" name="action" type="hidden" value = "update_email">
	<input class="btn btn-primary" name="bouton" value="Mettre à jour" type="submit">

	
	</form>	
</center>			
<?php template_fin_bloc_normal(); ?>


<?php  
if (	(!($_POST))		||( ($_POST) && ($_POST['action'] != 'update_configuration_FAIL') )	)
{
	template_debut_bloc_normal();
?>
<center>
	<h3>
		Gestion des logs
	</h3>

	<form name="clear_log" method="post" action="parameters.php">
		<input id="action" name="action" type="hidden" value = "voir_log">
		<br><input class="btn btn-primary" name="bouton" value="Voir les logs" type="submit" onClick="if(!confirm('ATTENTION : Vous allez effacer tout l'historique de connection (mais aucune autre donnée) ?')) return false;">
		</p>
	</form>		

	<form name="keep_last_log" method="post" action="parameters.php">
		<input id="action" name="action" type="hidden" value = "keep_last_log">
		<br><input class="btn btn-primary" name="bouton" value="Garder les 200 derniers enregistrements" type="submit" onClick="if(!confirm('ATTENTION : seul les 200 derniers enregistrement seront gardés ?')) return false;">
		</p>
	</form>		
	
	<form name="clear_log" method="post" action="parameters.php">
		<input id="action" name="action" type="hidden" value = "clear_log">
		<br><input class="btn btn-primary" name="bouton" value="Effacer tous les logs" type="submit" onClick="if(!confirm('ATTENTION : Vous allez effacer tout l'historique de connection (mais aucune autre donnée) ?')) return false;">
		</p>
	</form>	
</center>
<?php template_fin_bloc_normal(); 
}?>

<?php template_debut_bloc_normal(); ?>
<center>
	<h3>
		Configuration serveur
	</h3>
	<form name="gestion" method="post" action="parameters.php">

	<p>Nom du serveur SQL (souvent localhost) : <br>
	<input id="serveur" name="serveur" type="text" value = "<?php echo $serveur;?>" maxlength="256"></p>												
													
	<p>Login SQL (souvent root) : <br>
	<input id="login" name="login" type="text" value = "<?php echo $login_db;?>" maxlength="256"></p>												
													
	<p>Mot de passe SQL : <br>
	<input id="SQLmdp" name="SQLmdp" type="password" value = "" maxlength="256"></p>	

	<p>Nom de la base de donnée SQL : <br>
	<input id="dbname" name="dbname" type="text" value = "<?php echo $db_name;?>" maxlength="256"></p>	

	<br>
	<input id="action" name="action" type="hidden" value = "update_configuration">
	<input class="btn btn-primary" name="bouton" value="Tester et mettre à jour" type="submit">

	
	</form>	
</center>			
<?php template_fin_bloc_normal(); ?>


<?php  
if (	(!($_POST))		||( ($_POST) && ($_POST['action'] != 'update_configuration_FAIL') )	)
{
	template_debut_bloc_normal();
?>
<center>
	<h3>
		Administration
	</h3>
	<h4>Base de donnée :</h4>
		<input class="btn btn-primary" type="button" value="Sauvegarder la base de donnée" onClick="javascript:document.location.href='backup.php?action=save'" />
		<br><br>
		<form name="upload_sqlfile" method="post" action="backup.php" enctype="multipart/form-data">
			<input id="action" name="action" type="hidden" value = "load_sqlfile">
			<input type="file" id="sqlfile" name="sqlfile">
			<br><input class="btn btn-primary" name="bouton" value="Restaurer" type="submit" onClick="if(!confirm('ATTENTION : Vous allez effacer toutes les données ?')) return false;">
			</p>
		</form>		

		<br><input class="btn btn-primary" type="button" value="Restaurer la la dernière sauvegarde" onClick="if(confirm('ATTENTION : Vous allez effacer les dernières entrées ?')){javascript:document.location.href='backup.php?action=restore';}" />
		<br><br>
	
	<br>			
	<h4>Réinitialisation complète du site :</h4>			
	<form name="delete_all" method="post" action="parameters.php">
		<input id="action" name="action" type="hidden" value = "delete_all">
		
		<input class="btn btn-primary" name="bouton" value="Réinitialiser" type="submit" onClick="if(!confirm('ATTENTION : Vous allez effacer toutes les données ?')) return false;">
		</p>
	</form>		


</center>			
<?php 
template_fin_bloc_normal(); 
}
}
?>



<?php
} //check_login
sqli_close($db);
include 'footer.php'; 
?>		

