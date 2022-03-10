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
include 'configuration.php';
require( "zip.lib.php" ) ; //indiquez le chemin d'accès à la lib
require('fpdf.php');

class PDF extends FPDF
{
	// En-tête
	function Header()
	{
	}

	// Pied de page
	function Footer()
	{

	}
}


	echo '
<html lang="fr">
<head>
	<meta charset="utf-8"><title>Message</title>
	<link rel="stylesheet" type="text/css" href="sgdf.css">
	<meta name="viewport" content="width=device-width"/>
</head>


<body>
	<!-- HTML --> 
		<center>
		<div class = "message_ok">';
$fichiers = "";

// On recupere le nom des fichiers gz


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetMargins(0, 20 ,0);	

if($dossier = dir("./qrcode/")) {
	$zip = new zipfile( ) ; //on crée une nouvelle instance zip
	while(false !== ($fichier = $dossier->read())) {
		if($fichier != '.' && $fichier != '..') {
			if(is_dir("./qrcode/".$fichier)) {
				// Ceci est un dossier ( et non un fichier )
				continue;
			} else {
				// On ne prend que les fichiers se terminant par ".png"
				if(preg_match('/\.png$/i', $fichier)) {

					// J'explose dans un tableau à chaque fois que je rencontre un point
					$file_array = explode ('.',$fichier);
					// Je récupère l'indice dans le tableau de l'extension "jpg", soit le dernier élément
					$extension = count ($file_array) - 1;
					// Je découpe en enlevant l'extension cad (la taille de "jpg" + la taille du point d'où le -1)
					$fichier_noextension = substr ($fichier,0,strlen($fichier) -strlen ($file_array[$extension])-1);
					
					$pdf->AddPage();
					$pdf->SetFont('Arial','',50);
					if ($fichier[0] == 'T')
					{
						$pdf->Cell(210,10,'Tente '.$fichier_noextension,0,1,'C');
						$pdf->SetFont('Arial','',20);
						$pdf->Cell(210,20,'Lien : '.$base_addr_site.'update_tente.php?tente='.$fichier_noextension,0,1,'C');
						
					}
					else if($fichier[0] == 'L')
					{
						$pdf->Cell(210,10,'Latrine '.$fichier_noextension,0,1,'C');
						$pdf->SetFont('Arial','',20);
						$pdf->Cell(210,20,'Lien : '.$base_addr_site.'update_latrine.php?latrine='.$fichier_noextension,0,1,'C');
					}
					else if($fichier[0] == 'M')
					{
						$pdf->Cell(210,10,'Marabout '.$fichier_noextension,0,1,'C');
						$pdf->SetFont('Arial','',20);
						$pdf->Cell(210,20,'Lien : '.$base_addr_site.'update_marabout.php?marabout='.$fichier_noextension,0,1,'C');
					}
					else if($fichier[0] == 'D')
					{
						$pdf->Cell(210,10,'Douche '.$fichier_noextension,0,1,'C');
						$pdf->SetFont('Arial','',20);
						$pdf->Cell(210,20,'Lien : '.$base_addr_site.'update_douche.php?douche='.$fichier_noextension,0,1,'C');						
					}
					
					$pdf->Image('./qrcode/'.$fichier,85,50,40);
				
					$fo = fopen("./qrcode/".$fichier,'r') ; //on ouvre le fichier
					$contenu = fread($fo, filesize("./qrcode/".$fichier)) ; //on enregistre le contenu
					fclose($fo) ; //on ferme le fichier
					$zip->addfile($contenu, $fichier) ; //on ajoute le fichier
					$archive_zip = $zip->file() ; //on associe l'archive
				}
			}
		}
	} // while
	$pdf->Output('F','./qrcode/etiquettes.pdf');
	$fo = fopen("./qrcode/etiquettes.pdf",'r') ; //on ouvre le fichier
	$contenu = fread($fo, filesize("./qrcode/etiquettes.pdf")) ; //on enregistre le contenu
	fclose($fo) ; //on ferme le fichier	
	$zip->addfile($contenu, 'etiquettes.pdf') ; //on ajoute le fichier
	$archive_zip = $zip->file() ; //on associe l'archive	
	$dossier->close();
}
else
	echo message_error("Repertoire qrcode non trouvé");

 $open = fopen( "qrcode/qrcode.zip" , "wb"); //crée le fichier zip
 fwrite($open, $archive_zip); //enregistre le contenu de l'archive
 fclose($open); //ferme l'archive
 echo '
<br><input type="button" value="Telecharger les QRCODES" onClick="javascript:document.location.href=\'./qrcode/qrcode.zip\'" />
<br>
 <br><input type="button" value="Retour" onClick="javascript:document.location.href=\'index.php\'" />
<br><br>
</div>
	</center>
	</body>
</html>
';
?>