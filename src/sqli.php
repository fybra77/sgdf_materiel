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
// salt ajouté au mot de passe, vous pouvez le modifier mais attention les anciens mots de passe ne fonctionneront plus
// A modifier avant de faire l'installation
$salt = "cequetuveux";

$db = "";
$sqli_connected = false;

function sqli_connect($serveur, $login_db, $pwd_db, $db_name)
{
	global $db,$sqli_connected;
	if ($sqli_connected==false)
		$db = mysqli_connect($serveur, $login_db, $pwd_db, $db_name);
	$sqli_connected = true;
	return $db;
	
}

function sqli_close()
{
	global $db,$sqli_connected;
	if ($sqli_connected==true)
		mysqli_close($db);
	$sqli_connected = false;
}

?>