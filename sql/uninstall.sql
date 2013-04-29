-- To remove all changes in database by oxTiramizoo plugin, try to run these sql queries

-- Drop Tables
DROP TABLE oxtiramizooretaillocation;
DROP TABLE oxtiramizooretaillocationconfig;
DROP TABLE oxtiramizooarticleextended;
DROP TABLE oxtiramizoocategoryextended;
DROP TABLE oxtiramizooorderextended;
DROP TABLE oxtiramizooschedulejob;

-- DELETE delivery sets
DELETE FROM oxdeliveryset WHERE OXID = 'Tiramizoo';
DELETE FROM oxdelivery WHERE OXID = 'TiramizooStandardDelivery';
DELETE FROM oxdel2delset WHERE OXID = MD5(CONCAT('TiramizooStandardDelivery', 'Tiramizoo'));

-- DELETE all tpl blocks
DELETE FROM oxtplblocks WHERE OXMODULE = 'oxTiramizoo';

-- DELETE all modules vars
DELETE FROM oxconfig WHERE OXMODULE = 'oxTiramizoo';
