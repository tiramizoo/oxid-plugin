-- version 0.9.0
CREATE TABLE IF NOT EXISTS oxtiramizooretaillocation (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        OXSHOPID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
                        OXNAME varchar(128) NOT NULL DEFAULT '',
                        OXAPITOKEN varchar(128) NOT NULL DEFAULT ''
                   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS oxtiramizooretaillocationconfig (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        OXVARNAME varchar(128) NOT NULL DEFAULT '',
                        OXVARTYPE varchar(4) NOT NULL DEFAULT '',
                        OXVARVALUE TEXT NOT NULL,
                        OXLASTSYNC datetime NOT NULL,
                        OXRETAILLOCATIONID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
                   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS oxtiramizooarticleextended (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        TIRAMIZOO_ENABLE INT(1) NULL,
                        TIRAMIZOO_USE_PACKAGE INT(1) NULL,
                        OXARTICLEID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
                   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS oxtiramizoocategoryextended (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        TIRAMIZOO_ENABLE INT(1) NOT NULL DEFAULT 1,
                        TIRAMIZOO_WIDTH FLOAT,
                        TIRAMIZOO_HEIGHT FLOAT,
                        TIRAMIZOO_LENGTH FLOAT,
                        TIRAMIZOO_WEIGHT FLOAT,
                        TIRAMIZOO_USE_PACKAGE INT(1),
                        OXCATEGORYID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
                   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS oxtiramizooorderextended (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        TIRAMIZOO_STATUS VARCHAR(255),
                        TIRAMIZOO_TRACKING_URL VARCHAR(1024) NOT NULL,
                        TIRAMIZOO_RESPONSE TEXT NOT NULL,
                        TIRAMIZOO_REQUEST_DATA TEXT NOT NULL,
                        TIRAMIZOO_WEBHOOK_RESPONSE TEXT NOT NULL,
                        TIRAMIZOO_EXTERNAL_ID VARCHAR(40) NOT NULL,
                        OXORDERID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
                   ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS oxtiramizooschedulejob (
                        OXID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL PRIMARY KEY,
                        OXSHOPID char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
                        OXJOBTYPE varchar(32),
                        OXPARAMS text NOT NULL,
                        OXCREATEDAT datetime,
                        OXFINISHEDAT datetime,
                        OXRUNAFTER datetime,
                        OXRUNBEFORE datetime,
                        OXREPEATCOUNTER INT(11) NOT NULL DEFAULT 0,
                        OXEXTERNALID char(32),
                        OXSTATE varchar(32) NOT NULL DEFAULT 'new',
                        OXLASTERROR varchar(32)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
