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

/*session is started if you don't write this line can't use $_Session  global variable*/
$_SESSION["comefrom"]= $_SERVER['PHP_SELF'];


//*****************************************
// CREATION Listes deroulantes
//*****************************************

// Liste des tentes
$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
mysqli_query($db,"SET NAMES 'utf8'");
	
	
$shortname = [];
$array_materiel = array();
$liste_materiel= "<SELECT id='materiel_choisit' name='type' size='1' onChange=\"viewlist()\" ><OPTION>Autre materiel ou demande";	

$sql = "SELECT type,shortname FROM materiel_list ORDER BY type"; 
if ($resultat = mysqli_query($db,$sql))
{	
	while($row = mysqli_fetch_assoc($resultat)) 	
	{
		$shortname[$row["type"]] = $row["shortname"];
		$liste_materiel = $liste_materiel."<OPTION>".$row["type"];
		array_push($array_materiel, $row["type"]);
	}
	mysqli_free_result($resultat);
}
else
{
	info_message_erreur("Aucun type de matériel enregistré");
}	
$liste_materiel .= "</SELECT>";

if ($_POST)
{
	if(isset($_POST['add_message']))
	{
		$post_materiel = $_POST['type'].' '.$_POST[$_POST['type'].'_sel'];
		$subject = 'SGDF Gestion Matériel';

		$message = '  De : '.$_POST['nom'];
		
		$subject .= ' - '.$_POST['type'].' : '.$_POST[$_POST['type'].'_sel'];

// Attention ne pas réagence le code , car les espaces mis seront ceux dans le mail final
		$message .= '
  Matériel : '.$post_materiel;
		$message .= '
		
  Commentaire : 
  
  '.$_POST['evenement'];
  
		if(in_array($_POST['type'],$array_materiel))
		{
			$comment_to_add = '\nDe '.$_POST['nom'].' le '.date('d/m/Y').':\n'.$post_materiel;
			$to_replace = $_POST[$_POST['type'].'_sel'];
			$curid = str_replace($shortname[$_POST['type']], "", $_POST[$_POST['type'].'_sel']);
			$curtype = $_POST['type'];
			$sql = "UPDATE materiel SET commentaire=CONCAT(COALESCE(commentaire, ''),'".$comment_to_add."') WHERE id = '".$curid."' AND type='".$curtype."'" ;  
			if (!($resultat = mysqli_query($db,$sql)))
				info_message_erreur("Erreur le matériel n'exite pas");
			
			
		}
  
		$list_nom_variable = "name,materiel,message";
		$list_valeur_variable = "'".addslashes($_POST['nom'])."', '".addslashes($post_materiel)."', '".addslashes($_POST['evenement'])."'";
		$sql = "INSERT INTO demande (".$list_nom_variable.") "."VALUES (".$list_valeur_variable.")"; 
		if ($resultat = mysqli_query($db,$sql))
		{	
			info_message_OK("Message enregistré");
			require './email/send_email.php';
			sleep(1);
			if (envoie_email($email_from,$email_to,$subject,$message)==false)
				info_message_erreur("Erreur envoie par email, mais votre mesage a quand même été enregistré");
		}
		else
		{
			info_message_erreur(mysqli_error($db)."Erreur tente déja existante");
		}	
		
	}


}
?>


<?php template_debut_bloc_normal(); ?>
<center>
<form name="ajout_message" method="post" action="add_message.php">

	Choisissez un matériel :
	<br><?php echo $liste_materiel;?>
	<br>

<?php

	foreach($array_materiel as $materiel)
	{
		$liste_deroulante = "<SELECT id='".$materiel."_sel' name='".$materiel."_sel' size='1'>";	
		$sql = "SELECT id FROM materiel WHERE type='".$materiel."' ORDER BY  length(id),id"; 
		if ($resultat = mysqli_query($db,$sql))
		{	
			while($row = mysqli_fetch_assoc($resultat)) 	
			{
				if(($_GET) && ($shortname[$materiel].$row["id"] == $_GET["tente"]))
					$liste_deroulante = $liste_deroulante.'<OPTION selected="selected">'.$shortname[$materiel].$row["id"];
				else
					$liste_deroulante = $liste_deroulante."<OPTION>".$shortname[$materiel].$row["id"];
			}
			$liste_deroulante = $liste_deroulante."</SELECT>";
			mysqli_free_result($resultat);
			echo '<div id="div_'.$materiel.'" style="display :';
			if(isset($_GET[$materiel])) 
				echo 'block'; 
			else 
				echo 'none';
			echo '" >';
			echo "<br>".$liste_deroulante;
		echo "&nbsp;&nbsp;&nbsp;<input class='btn btn-primary' type='button' value='Voir etat' onclick=\"location.href='edit_materiel.php?type=".$materiel."&id='+get_id_materiel(document.getElementById('".$materiel."_sel').value,'".$shortname[$materiel]."');\"/></div>";
		}
		else
		{
			info_message_erreur(mysqli_error($db));
		}
		
	}
?>
	<br>
	Nom :<br><input type="text" id="nom" name="nom" required maxlength="30" size="20">
	<br>   <br>
	<fieldset style="width: 80%;border:0;"> 
		<legend>Commentaire:</legend>
		<textarea style="width: 100%;" name="evenement" rows="5" required ></textarea>
	</fieldset>
	<br>
	<input class="btn btn-primary"  name="add_message" type="submit" value="Envoyer le commentaire" />

</form>
</center>
<?php template_fin_bloc_normal(); ?>

<script type="text/javascript">

function viewlist()
{
	var selectElmt = document.getElementById('materiel_choisit');
<?php 	
	foreach($array_materiel as $materiel)
	{
		echo "
		if (selectElmt.options[selectElmt.selectedIndex].value == '".$materiel."')
		{
			document.getElementById(\"div_".$materiel."\").style.display='block';
		}
		else
		{
			document.getElementById(\"div_".$materiel."\").style.display='none';
		}\n";

	}
?>
}

function get_id_materiel(nom,shortname)
{
	var tp_nom = nom;
	return tp_nom.replace(shortname,'');
}
</script>

<?php sqli_close($db); ?>

<?php

include 'footer.php'; 
?>	