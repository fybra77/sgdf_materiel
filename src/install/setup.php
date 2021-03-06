<?php 
session_start();
include '../configuration.php';
include '../sqli.php';
include '../log.php';
include '../template.php';
include '../tools.php';

if ($_SESSION['setup'] != "todo")
	exit();

$serveur = "localhost";
$login_db = "root";
$pwd_db = "";
$db_name = "";
$init_base_ok = true;
if (isset($_POST['action']))
{
	if($_POST['action'] == 'update_configuration')
	{
		try
		{		
			// error_reporting(E_ALL);
			$mysqli_connection = new MySQLi($_POST['serveur'], $_POST['login'], $_POST['motdepasse'], $_POST['dbname']);
			if ($mysqli_connection->connect_error) {
			   info_message_erreur("Mauvaise configuration, essayez encore !",false);
			   $init_base_ok = false;
			}
			else
				$mysqli_connection->close();			
		}
		catch(Exception $e)
		{
			info_message_erreur("Mauvaise configuration, essayez encore !",false);
			$init_base_ok = false;
		}			

		if ($init_base_ok == true)
		{
			$serveur = $_POST['serveur'];
			$login_db = $_POST['login'];
			$pwd_db = $_POST['motdepasse'];
			$db_name = $_POST['dbname'];
		
		
			$fichier_content = "<?php
	\$serveur = '".$_POST['serveur']."';
	\$login_db = '".$_POST['login']."';
	\$pwd_db = '".$_POST['motdepasse']."';
	\$db_name = '".$_POST['dbname']."';
	?>";
			$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name); 
			mysqli_query($db,"SET NAMES 'utf8'");
			$sql = file_get_contents('./init.sql');
			/* execute multi query */
			mysqli_multi_query($db,$sql);
			while (mysqli_next_result($db)) {;} // flush multi_queries sinon la query suivante n'est pas exécuté !!!!!
			// echo "<br>Base de donnée initialisée<br>";	

			$sql = 'INSERT INTO membre (login, pass_hashed) VALUES ("'.mysqli_escape_string($db,'admin').'", "'.mysqli_escape_string($db,password_hash($salt.'admin',PASSWORD_BCRYPT)).'")';
			$resultat = mysqli_query($db,$sql);
			// echo "<br>Utilisateur 'admin' ajouté avec le mot de passe 'admin'<br>";		
			
			$file = fopen("../configuration.php", "w");
			fwrite($file,$fichier_content);
			fclose($file);
			$_SESSION['setup'] = "done";
			
			info_message_OK("Configuration mise à jour !");			

		}			

	}
}
?>
<body style="background-color:black;background-image: url('../assets/img/bg.jpg');background-repeat: no-repeat;background-position: top;">
<center>
<div style="background-color:rgba(255, 99, 71, 0.8);width:400px;">
<img style="width:400px;" src="../assets/img/logo_complet.png" />
<?php
if ($_SESSION['setup'] == "todo")
{
?>

	<h3>
		<br>
		INSTALLATION
		<br>
		<br>Merci de remplir les informations suivantes
	</h3>
	<form name="gestion" method="post" action="setup.php">

	<p>Nom du serveur SQL (souvent localhost) : <br>
	<input id="serveur" name="serveur" type="text" value = "<?php echo $serveur;?>" maxlength="256"></p>												
													
	<p>Login SQL (souvent root) : <br>
	<input id="login" name="login" type="text" value = "<?php echo $login_db;?>" maxlength="256"></p>												
													
	<p>Mot de passe SQL : <br>
	<input id="motdepasse" name="motdepasse" type="password" value = "" maxlength="256"></p>	

	<p>Nom de la base de donnée SQL : <br>
	<input id="dbname" name="dbname" type="text" value = "<?php echo $db_name;?>" maxlength="256"></p>	

	<br>
	<input id="action" name="action" type="hidden" value = "update_configuration">
	<input class="btn btn-primary" name="bouton" value="Tester et mettre  à jour" type="submit">

	
	</form>	
	<br>
	<br>
	


<?php
}
else
{
	session_destroy();
	echo '<h2>Installation finie !</h2>
	<br><h3>Login : "admin"
	<br>Mot de passe : "admin"
	<br><br>ATTENTION pensez à changer le mot de passe dans le menu PARAMETRES du site !</h2>
	<br><br><input class="btn btn-primary"  type="button" value="Acceder au site" onClick="javascript:document.location.href=\'../index.php\'" />
	<br><br>
	';
}
?>			
</div>
</center>
</body>