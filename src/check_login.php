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

session_start();

if(($serveur == '') && ($login_db == '') && ($pwd_db == '') && ($db_name == ''))
{
	//header_remove();
	$_SESSION['setup']="todo";
	//header('Location: ./install/setup.php');
	echo "<script type='text/javascript'>document.location.replace('./install/setup.php');</script>";
	exit();
}


function check_if_logged($php_file)
{
	global $serveur,$login_db,$pwd_db,$db_name,$salt;
	
	if (!isset($_SESSION['login'])) 
	{
		// on teste si le visiteur a soumis le formulaire de connexion
		if ((isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion') || (isset($_GET['connexion']) && $_GET['connexion'] == 'ConnexionExt') )
		{
			if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['pass']) && !empty($_POST['pass']))) 
			{
				set_loginfo("Connection de ".$_POST['login']);
				$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
				mysqli_query($db,"SET NAMES 'utf8'");

				// on teste si une entrée de la base contient ce couple login / pass
				$sql = 'SELECT pass_hashed FROM membre WHERE login="'.mysqli_escape_string($db,$_POST['login']).'"';
				$req = mysqli_query($db,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
				$nb_row = mysqli_num_rows($req);
				$data = mysqli_fetch_array($req);
				mysqli_free_result($req);

				sqli_close($db);

				// si on obtient une réponse, alors l'utilisateur est un membre
				if (password_verify($salt.$_POST['pass'], $data['pass_hashed']))
				{
					//header_remove();
					session_start();
					$_SESSION['comefromexternal']=false;
					$_SESSION['login'] = $_POST['login'];
					//header('Location: '.$php_file);
					echo "<script type='text/javascript'>document.location.replace('".$php_file."');</script>";
					exit();
				}
				// si on ne trouve aucune réponse, le visiteur s'est trompé soit dans son login, soit dans son mot de passe
				elseif ($nb_row==0) 
				{
					$erreur = 'Compte non reconnu.';
				}
				elseif ($nb_row==1) 
				{
					$erreur = 'Login ou mot de passe incorrect';
				}
				// sinon, alors la, il y a un gros problème :)
				else 
				{
					$erreur = 'Probème dans la base de données : plusieurs membres ont les mêmes identifiants de connexion.';
				}
			}
			else if ((isset($_GET['login']) && !empty($_GET['login'])) && (isset($_GET['pass']) && !empty($_GET['pass']))) 
			{
				set_loginfo("Connection de ".$_GET['login']);
				$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
				mysqli_query($db,"SET NAMES 'utf8'");

				// on teste si une entrée de la base contient ce couple login / pass
				$sql = 'SELECT pass_hashed FROM membre WHERE login="'.mysqli_escape_string($db,$_GET['login']).'"';
				$req = mysqli_query($db,$sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
				$nb_row = mysqli_num_rows($req);
				$data = mysqli_fetch_array($req);
				
				mysqli_free_result($req);

				sqli_close($db);

				// si on obtient une réponse, alors l'utilisateur est un membre
				if (password_verify($salt.$_GET['pass'], $data['pass_hashed']))
				{
					//header_remove();
					session_start();
					$_SESSION['login'] = $_GET['login'];
					$_SESSION['comefromexternal']=false;
					//header('Location: '.$php_file);
					echo "<script type='text/javascript'>document.location.replace('".$php_file."');</script>";
					exit();
				}
				// si on ne trouve aucune réponse, le visiteur s'est trompé soit dans son login, soit dans son mot de passe
				elseif ($nb_row==0) 
				{
					$erreur = 'Compte non reconnu.';
				}
				elseif ($nb_row==1) 
				{
					$erreur = 'Login ou mot de passe incorrect';
				}
				// sinon, alors la, il y a un gros problème :)
				else 
				{
					$erreur = 'Probème dans la base de données : plusieurs membres ont les mêmes identifiants de connexion.';
				}            
				
			}
			else
			{
			$erreur = 'Au moins un des champs est vide.';
			}
			message_erreur($erreur);
			exit();
		}
		else
		{
			template_debut_bloc_normal();
?>			<center>
			<form action="<?php echo $php_file; ?>" method="post">
			<br>
			<table>
			<tr><td>Utilisateur&#8239;:&#8239;</td><td><input size="13" type="text" name="login" value="<?php if (isset($_POST['login'])) echo htmlentities(trim($_POST['login'])); ?>"></td></tr>
			<tr><td>Mot&#8239;de&#8239;passe&#8239;:&#8239;</td><td><input size="13" type="password" name="pass" value="<?php if (isset($_POST['pass'])) echo htmlentities(trim($_POST['pass'])); ?>"></td></tr>
			</table>
			<br><input class="btn btn-primary"  type="submit" name="connexion" value="Connexion">
			</form>
			</center>
<?php
			template_fin_bloc_normal();
		}
		return false;
	}
	else
		return true;
}

function is_admin()
{
	if (isset($_SESSION['login']))
	{
		if ($_SESSION['login']=='admin')
			return true;
		else
			return false;
	}
	else
		return false;
}

function is_user()
{
	if (isset($_SESSION['login']))
		return true;
	else
		return false;
}

function check_login($php_file)
{
	if (check_if_logged($php_file) == true)
	{
		if (!isset($_SESSION['login']))
		{
			template_debut_bloc_info();
			?>

			<h2 class="section-heading mb-4">
				<span class="section-heading-upper">Vous n'avez pas le droit d'accéder à la page</span>
			</h2>

			<?php
			template_fin_bloc_info();
			return false;
		}
		else
			return true;
	}
	return false;
}

function check_admin($php_file)
{
	check_login($php_file);
	if ($_SESSION['login'] !== 'admin')
	{
			template_debut_bloc_info();
			?>

			<h2 class="section-heading mb-4">
				<span class="section-heading-upper">Vous n'avez pas le droit d'accéder à la page</span>
			</h2>

			<?php
			template_fin_bloc_info();
			return false;
	}
	else
		return true;
}

function deconnection()
{
	//header_remove();
	session_unset();
	session_destroy();
	//header('Location: login.php');
	echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
}
?>