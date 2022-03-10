DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL auto_increment,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `event` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS `demande`;
CREATE TABLE `demande` (
  `id` int(11) NOT NULL auto_increment,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `materiel` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

DROP TABLE IF EXISTS `evenements`;
CREATE TABLE `evenements` (
  `num` int(11) NOT NULL auto_increment,
  `id` varchar(5) default NULL,
  `date_evenement` date default NULL,
  `commentaire` varchar(1024) default NULL,
  PRIMARY KEY  (`num`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `membre`;
CREATE TABLE `membre` (
  `id` int(11) NOT NULL auto_increment,
  `login` text NOT NULL,
  `pass_hashed` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resa`;
CREATE TABLE `resa` (
  `id` int(11) NOT NULL auto_increment,
  `objet` varchar(35) default NULL,
  `date_debut_resa` date default NULL,
  `date_fin_resa` date default NULL,
  `nom_camp` varchar(512) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `materiel`;
CREATE TABLE `materiel` (
  `entry` int(11) NOT NULL auto_increment,
  `type` varchar(30) DEFAULT NULL,
  `shortname` varchar(2) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `info` varchar(30) DEFAULT NULL,
  `modele` varchar(256) DEFAULT NULL,
  `date_mise_en_service` date DEFAULT NULL,
  `date_verification` date DEFAULT NULL,
  `utilisable` varchar(10) DEFAULT NULL,
  `etat_general` varchar(20) DEFAULT NULL,
  `commentaire` varchar(1024) DEFAULT NULL,
  PRIMARY KEY  (`entry`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `materiel_list`;
CREATE TABLE `materiel_list` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(30) DEFAULT NULL,
  `shortname` varchar(2) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
