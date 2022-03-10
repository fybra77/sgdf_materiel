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
echo '<style type="text/css"> 

a { color: blue; } /* CSS link color */

</style>';
require('cellpdf.php');

if(check_login($_SERVER['PHP_SELF'])==true)
{
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
	$list_type_of_matos = array();
	
	$sql = "SELECT type FROM materiel_list ORDER BY type"; 
	if ($resultat = mysqli_query($db,$sql))
	{	
		while($row = mysqli_fetch_assoc($resultat)) 	
		{
			array_push($list_type_of_matos,$row["type"]);
		}
		mysqli_free_result($resultat);
	}

	// print_r($list_type_of_matos);
    mysqli_query($db,"SET NAMES 'utf8'");
	$pdf=new CellPDF('L');
	$pdf->AliasNbPages();
	$pdf->SetMargins(5, 5 ,5);	
	$pdf->SetAutoPageBreak(true, 5);

	foreach($list_type_of_matos as $type_matos)
	{		
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,10,utf8_decode('Etat du matériel : "'.$type_matos.'" au '.date("d-m-Y H:i")).' :',0,1,'L',true);
		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(51,209,255);
		$pdf->Cell(20,5,utf8_decode('Nom'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Info'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Modele'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Mise en service'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Vérification'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Utilisable'),1,0,'C',true);
		$pdf->Cell(20,5,utf8_decode('Etat'),1,0,'C',true);
		$pdf->Cell(147,5,utf8_decode('Commentaire'),1,1,'C',true);
		//id,nb_place,modele,date_mise_en_service,date_verification,utilisable,etat_general,commentaire
		
		$sql = "SELECT * FROM materiel WHERE type='".$type_matos."' ORDER BY length(id),id" ; 

		if ($resultat = mysqli_query($db,$sql))
		{	
			while($ligne = mysqli_fetch_array($resultat)) 	
			{
				if ($ligne['utilisable'] == 'Oui')
					$pdf->SetFillColor(0,255,0);
				else
					$pdf->SetFillColor(255,0,0);
				
				$hauteur_cellule = 5 + 8*mb_substr_count($ligne['commentaire'], "\n");

				if ($hauteur_cellule > 5)
					$hauteur_cellule = $hauteur_cellule -4;
				
				// Récupération dernière reservation
				$sql = "SELECT * FROM resa WHERE objet='".$ligne['shortname'].$ligne['id']."' ORDER BY date_fin_resa DESC";
				if ($resultat2 = mysqli_query($db,$sql))
					$last_resa = mysqli_fetch_assoc($resultat2);
				if(($last_resa) && ($ligne['commentaire'] != ""))
					$hauteur_cellule = $hauteur_cellule + 5;
					
				$nb_colonnes = count($ligne)/2 ;
				
				for ($i = 0; $i < ($nb_colonnes-1); $i++) 
				{
					if ($i==3)
						$pdf->Cell(20,$hauteur_cellule,utf8_decode($ligne[2].$ligne[3]),1,0,'C',true);
					elseif ($i>3)
						$pdf->Cell(20,$hauteur_cellule,utf8_decode($ligne[$i]),1,0,'C',true);
				}

				$ligne_finale = $ligne[$i];

				if($last_resa)
				{
					$ligne2 =  'Dernière reservation : '.$last_resa['nom_camp'].' du '.format_if_date($last_resa['date_debut_resa']).' au '.format_if_date($last_resa['date_fin_resa']);
					
					if ($ligne[$i] == "")
						$ligne_finale = $ligne2;
					else
					{
						$ligne_finale = $ligne2."\n".$ligne[$i];
					}
				}


					
				$pdf->Cell(147,$hauteur_cellule,utf8_decode($ligne_finale),1,1,'L',true);

			}

		}
		else
		{
			echo "<font color='red'> <br>Erreur dans l'accès de la base<br></font>";
		}
	}

	
	$pdf->Output('F','liste_materiel_SGDF.pdf');
	sqli_close($db);
	template_debut_bloc_info();
	echo '<center><h2><a target="_blank" class = "fake_button" href="liste_materiel_SGDF.pdf">Télécharger la liste</a> </h2><br><br>';
	echo '<button type="submit" style="background: none; cursor:pointer; border: 0; " onClick="javascript:document.location.href=\''.$_SESSION['comefrom'].'\'" /> <img title="Retour" src="./assets/retour.png" width=70 height=auto/></button><br>';
	template_fin_bloc_info();
	?>
	
<?php
} //check_login

include 'footer.php'; 
?>		