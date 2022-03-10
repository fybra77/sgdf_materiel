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

if(check_login($_SERVER['PHP_SELF'])==false)
	exit;
?>

<?php
template_debut_bloc_info();
echo '<center>';
// Restauration
function executeQueryFile($filesql) {
	global $serveur, $login_db, $pwd_db, $db_name;
	$db = sqli_connect($serveur, $login_db, $pwd_db, $db_name);
	mysqli_query($db,"SET NAMES 'utf8'");
    $query = file_get_contents($filesql);
    $array = explode(";\n", $query);
    $b = true;
    for ($i=0; $i < count($array) ; $i++) {
        $str = $array[$i];
        if ($str != '') {
             $str .= ';';
             $b &= mysqli_query($db,$str);  
        }  
    }
    return $b;
}

/**
 * Sauvegarde MySQL
 */
class BackupMySQL extends mysqli {
	
	/**
	 * Dossier des fichiers de sauvegardes
	 * @var string
	 */
	protected $dossier;
	
	/**
	 * Nom du fichier
	 * @var string
	 */
	protected $nom_fichier;
	
	/**
	 * Ressource du fichier GZip
	 * @var ressource
	 */
	protected $gz_fichier;
	
	
	/**
	 * Constructeur
	 * @param array $options
	 */
	public function __construct($options = array()) {
		$default = array(
			'host' => ini_get('mysqli.default_host'),
			'username' => ini_get('mysqli.default_user'),
			'passwd' => ini_get('mysqli.default_pw'),
			'dbname' => '',
			'port' => ini_get('mysqli.default_port'),
			'socket' => ini_get('mysqli.default_socket'),
			// autres options
			'dossier' => './save/',
			'nbr_fichiers' => 100	,
			'nom_fichier' => 'backup'
			);
		$options = array_merge($default, $options);
		extract($options);
		
		// Connexion de la connexion DB
		@parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
		if($this->connect_error) {
			$this->message('Erreur de connexion (' . $this->connect_errno . ') '. $this->connect_error);
			return;
		}
		
		// Controle du dossier
		$this->dossier = $dossier;
		if(!is_dir($this->dossier)) {
			$this->message('Erreur de dossier &quot;' . htmlspecialchars($this->dossier) . '&quot;');
			return;
		}
		
		// Controle du fichier
		$this->nom_fichier = $nom_fichier . date('Ymd-His') . '.sql';
		$monfichier = fopen($this->dossier.$this->nom_fichier, 'w+');
		//$this->gz_fichier = @gzopen($this->dossier . $this->nom_fichier, 'w');
		// if(!$this->gz_fichier) {
			// $this->message('Erreur de fichier &quot;' . htmlspecialchars($this->nom_fichier) . '&quot;');
			// return;
		// }
		
		// Demarrage du traitement
		$this->sauvegarder($monfichier);
		fclose($monfichier);
		$this->purger_fichiers($nbr_fichiers);
	}
	
	/**
	 * Message d'information ( commenter le "echo" pour rendre le script invisible )
	 * @param string $message HTML
	 */
	protected function message($message = '&nbsp;') {
		echo '<p style="padding:0; margin:1px 10px; font-family:sans-serif;">'. $message .'</p>';
	}
	
	/**
	 * Protection des quot SQL
	 * @param string $string
	 * @return string
	 */
	protected function insert_clean($string) {
		// Ne pas changer l'ordre du tableau !!!
		$s1 = array( "\\"	, "'"	, "\r", "\n", );
		$s2 = array( "\\\\"	, "''"	, '\r', '\n', );
		return str_replace($s1, $s2, $string);
	}
	
	/**
	 * Sauvegarder les tables
	 */
	protected function sauvegarder($monfichier) {
		$this->message('Sauvegarde...');
		
		// $sql  = '--' ."\n";
		// $sql .= '-- '. $this->nom_fichier ."\n";

		// gzwrite($this->gz_fichier, $sql);
		
		// Liste les tables
		$result_tables = $this->query('SHOW TABLE STATUS');
		if($result_tables && $result_tables->num_rows) {
			while($obj_table = $result_tables->fetch_object()) {
				$this->message('- ' . htmlspecialchars($obj_table->{'Name'}));
				
				// DROP ...
				$sql  = "\n\n";
				$sql .= 'DROP TABLE IF EXISTS `'. $obj_table->{'Name'} .'`' .";\n";

				// CREATE ...
				$result_create = $this->query('SHOW CREATE TABLE `'. $obj_table->{'Name'} .'`');
				if($result_create && $result_create->num_rows) {
					$obj_create = $result_create->fetch_object();
					$sql .= $obj_create->{'Create Table'} .";\n";
					$result_create->free_result();
				}

				// INSERT ...
				$result_insert = $this->query('SELECT * FROM `'. $obj_table->{'Name'} .'`');
				if($result_insert && $result_insert->num_rows) {
					$sql .= "\n";
					while($obj_insert = $result_insert->fetch_object()) {
						$virgule = false;
						
						$sql .= 'INSERT INTO `'. $obj_table->{'Name'} .'` VALUES (';
						foreach($obj_insert as $val) {
							$sql .= ($virgule ? ',' : '');
							if(is_null($val)) {
								$sql .= 'NULL';
							} else {
								$sql .= '\''. $this->insert_clean($val) . '\'';
							}
							$virgule = true;
						} // for
						
						$sql .= ')' .";\n";
						
					} // while
					$result_insert->free_result();
				}
				
				//gzwrite($this->gz_fichier, $sql);
				fputs($monfichier, $sql);
			} // while
			$result_tables->free_result();
		}
		// gzclose($this->gz_fichier);
		$this->message('<a style="color:green;" href="./save/'.htmlspecialchars($this->nom_fichier).'">' . htmlspecialchars($this->nom_fichier) . '</a>');
		
		$this->message('Sauvegarde termin&eacute;e !');
	}
	
	/**
	 * Purger les anciens fichiers
	 * @param int $nbr_fichiers_max Nombre maximum de sauvegardes
	 */
	protected function purger_fichiers($nbr_fichiers_max) {
		$this->message();
		$this->message('Purge des anciens fichiers...');
		$fichiers = array();
		
		// On recupere le nom des fichiers gz
		if($dossier = dir($this->dossier)) {
			while(false !== ($fichier = $dossier->read())) {
				if($fichier != '.' && $fichier != '..') {
					if(is_dir($this->dossier . $fichier)) {
						// Ceci est un dossier ( et non un fichier )
						continue;
					} else {
						// On ne prend que les fichiers se terminant par ".gz"
						if(preg_match('/\.sql$/i', $fichier)) {
							$fichiers[] = $fichier;
						}
					}
				}
			} // while
			$dossier->close();
		}
		
		// On supprime les  anciens fichiers
		$nbr_fichiers_total = count($fichiers);
		if($nbr_fichiers_total >= $nbr_fichiers_max) {
			// Inverser l'ordre des fichiers gz pour ne pas supprimer les derniers fichiers
			rsort($fichiers);
			
			// Suppression...
			for($i = $nbr_fichiers_max; $i < $nbr_fichiers_total; $i++) {
				$this->message('<strong style="color:red;">' . htmlspecialchars($fichiers[$i]) . '</strong>');
				unlink($this->dossier . $fichiers[$i]);
			}
		}
		$this->message('Purge termin&eacute;e !');
	}
	
}

if(($_POST) && isset($_POST['action']))
{
	if($_POST['action'] == 'load_sqlfile')
	{	
		// Vérifie si le fichier a été uploadé sans erreur.
		if(isset($_FILES["sqlfile"]) && $_FILES["sqlfile"]["error"] == 0)
		{
			$filename = $_FILES["sqlfile"]['tmp_name'];
			executeQueryFile($filename);
			info_message_OK('Le fichier : '.$_FILES["sqlfile"]['name'].' a été restauré');
		}

	}
}	
elseif ($_GET && isset($_GET["action"]) )
{
	global $serveur,$login_db, $pwd_db,$db_name;
	if ($_GET["action"] == 'save')
	{
		new BackupMySQL(array(
		'username' => $login_db,
		'passwd' => $pwd_db,
		'dbname' => $db_name,
		'nom_fichier' => $db_name
		//'dossier' => 'save'
		));
		set_loginfo('Sauvegarde de la base de donnée');
	}
	
	if ($_GET["action"] == 'restore')
	{
		// Restaure database
		// cherche le nom du dernie fichier
		$fichiers = array();
		
		// On recupere le nom des fichiers gz
		if($dossier = dir("./save/")) {
			while(false !== ($fichier = $dossier->read())) {
				if($fichier != '.' && $fichier != '..') {
					if(is_dir("./save/".$fichier)) {
						// Ceci est un dossier ( et non un fichier )
						continue;
					} else {
						if(strstr($fichier, $db_name))
						// On ne prend que les fichiers se terminant par ".gz"
						if(preg_match('/\.sql$/i', $fichier)) {
							$fichiers[] = $fichier;
						}
					}
				}
			} // while
			$dossier->close();

		}
		rsort($fichiers);
		// On supprime les  anciens fichiers

		executeQueryFile("./save/".$fichiers[0]);
		echo 'Le fichier :<br>'.$fichiers[0].'<br> a été restauré';
		set_loginfo('Le fichier : '.$fichiers[0].' a été restauré');
	}

}

echo '<br><input class="btn btn-primary" type="button" value="Retour" onClick="javascript:document.location.href=\'parameters.php\'" />';
echo '</center>';
template_fin_bloc_info();

?>

<?php
include 'footer.php'; 
?>		