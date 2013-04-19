CREATE TABLE IF NOT EXISTS `oxtiramizooconfig` (
  `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `OXVARNAME` varchar(64) NOT NULL DEFAULT '',
  `OXVARTYPE` varchar(4) NOT NULL DEFAULT '',
  `OXVARVALUE` blob NOT NULL,
  `OXLASTSYNC` datetime NOT NULL,
  `OXGROUP` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `oxtiramizooconfig` ADD PRIMARY KEY (  `OXID` );


CREATE TABLE IF NOT EXISTS `oxtiramizooretaillocation` (
  `OXID` int(11) NOT NULL AUTO_INCREMENT,
  `OXSHOPID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `OXNAME` varchar(64) NOT NULL DEFAULT '',
  `OXAPITOKEN` varchar(4) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `oxtiramizooconfig` ADD PRIMARY KEY (  `OXID` );


CREATE TABLE IF NOT EXISTS `oxtiramizooreataillocationconfig` (
  `OXID` int(11) NOT NULL AUTO_INCREMENT,
  `OXSHOPID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `OXVARNAME` varchar(64) NOT NULL DEFAULT '',
  `OXVARTYPE` varchar(4) NOT NULL DEFAULT '',
  `OXVARVALUE` blob NOT NULL,
  `OXLASTSYNC` datetime NOT NULL,
  `OXRETAILLOCATIONID` int(11)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `oxtiramizooconfig` ADD PRIMARY KEY (  `OXID` );



-- 3 typy zadań [pobranie konfiguracji codziennie, pobranie] 

CREATE TABLE IF NOT EXISTS `oxtiramizoojobs` (
  `OXID` int(11) NOT NULL AUTO_INCREMENT,
  `OXJOBTYPE` varchar(32),
  `OXPARAMS` text NOT NULL DEFAULT '',
  `OXCREATEDAT` datetime, 
  `OXRUNAFTER` datetime,
  `OXRUNAFTER` datetime,
  `OXRUNNINGCOUNTER` INT(11) NOT NULL DEFAULT 0,  
  `OXEXTERNALID` char(32),
  `OXSTATE` varchar(4) NOT NULL DEFAULT 'new'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `oxtiramizoojobs` ADD PRIMARY KEY (  `OXID` );


