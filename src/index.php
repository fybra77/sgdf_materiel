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
?>

<?php 
if((check_login($_SERVER['PHP_SELF'])==true) || ( (isset($_SESSION['comefromexternal'])) && ($_SESSION['comefromexternal']==true)) )
{
	template_debut_bloc_normal(0); 
?>
	<center>
	<br>
	<h3>Derniers messages</h3>
	<?php

		$sql = "SELECT date,name,materiel,message FROM demande ORDER BY date DESC LIMIT 2"; 
		// echo $sql;
		if ($resultat = mysqli_query($db,$sql))
		{	
			if (mysqli_num_rows($resultat )>0)	
			{
				require('tableau.php');
				echo '<table style="border-collapse : separate; border-spacing : 1px;"  class = "avectri" >
				<thead><tr><th data-tri="0" class="selection" data-type="date">Date</th><th>Nom</th><th>Matériel</th><th>Message</th></tr></thead><tbody>';
				while($ligne = mysqli_fetch_assoc($resultat)) 	
				{
					echo '<tr>';
					foreach($ligne as $valeur) {
							echo '<td align="left">'.nl2br(format_if_date($valeur)).'</td>'; 
					}
					echo '</tr>';
					
				}
				echo '</tbody></table><br>';
			}
			else
				echo "Aucun message...";

		}
		else
		{
			echo "<font color='red'> <br>Erreur recupérations des messages<br></font>";
		}
	?>
	</center>
<?php
	template_fin_bloc_normal(); 
?>

<?php template_debut_bloc_normal(); 
	echo '<center><h3>Listes du matériel</h3><br>';
	affiche_bouton_liste_materiel();
	if (is_user())	
		echo '	<br>
			<br><input class="btn btn-primary" type="button" value="Télécharger les listes en PDF" onClick="javascript:document.location.href=\'gen_liste_pdf.php\'" /><br>(pour la revue du matériel)';
?>
	
	</center>

<?php 
	template_fin_bloc_normal(); 

?>
			
<?php

	template_debut_bloc_normal(); 
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	{
		$url = "https";
	}
	else
	{
		$url = "http"; 
	}  
	$url .= "://"; 
	$url .= $_SERVER['HTTP_HOST']; 
	$url .= $_SERVER['REQUEST_URI']; 
	$url = substr($url, 0, strrpos($url, "/"));
	$url .= "/matos";
	
	$qr_image_generique = "./qrcode/QR_Code_Generique.png";
	if ( (file_exists('./qrcode/QR_Code_Generique.png') != true) || ( isset($_POST['action']) && ($_POST['action']=="update_generic_qrcode") ))
	{
		require 'phpqrcode.php';
		QRcode::png($url,$qr_image_generique, QR_ECLEVEL_L, 10,1); // creates code image and outputs it directly into browser
	}
	
?>
<center>
<h3>Telecharger le QR Code</h3>
<br>
<img src='<?php echo $qr_image_generique;?>' height="10%" alt="QR Code Génerique" />
<br>
<big>
<a style='color:blue;' href='<?php echo $url;?>'><?php echo $url;?></a>
</big>

<br>
<br>
<?php if (is_user())
{
?>
<form method="post" action="index.php">
	<input id="action" name="action" type="hidden" value = "update_generic_qrcode">
	<input class="btn btn-primary" value="Regénérer le QRCode" type="submit">
</form>
<?php 
}
?>

<?php template_fin_bloc_normal(); ?>		

		
<?php
} //check_login

include 'footer.php'; 
?>		

