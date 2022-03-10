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


// if(check_login($_SERVER['PHP_SELF'])==true)
if(check_login('index.php')==true)
{
	if ( isset($_POST['connexion']) && ($_POST['connexion'] == 'Deconnexion'))
	{
		//header_remove();
		session_unset();
		session_destroy();
		//header('Location: login.php');
		echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
	}
	else
	{
?>

<?php template_debut_bloc_info(); ?>
<p>
			<h2>Vous êtes authentifié en tant que <br>"<?php echo $_SESSION['login'];?>"</h2>
 			<form action="login.php" method="post">
			<input class="btn btn-primary"  type="submit" name="connexion" value="Deconnexion">
			</form>	
</p>
<?php template_fin_bloc_info(); ?>



		
<?php
	//header_remove();
	// header('Location: index.php');
	} // connexion
} //check_login
include 'footer.php'; 
?>	

