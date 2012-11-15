# --- new oxorder field
ALTER TABLE `oxorder` ADD `TIRAMIZOO_TRACKING_URL` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `oxorder` ADD `TIRAMIZOO_STATUS` TINYINT NOT NULL ;
ALTER TABLE `oxorder` ADD `TIRAMIZOO_PARAMS` TEXT NOT NULL ;


-- ALTER TABLE `oxarticles` DROP `OXTIRAMIZOOENABLE`;
ALTER TABLE `oxarticles` ADD `OXTIRAMIZOOENABLE` INT(1) NOT NULL DEFAULT 0;


# --- binding deliveries with delivery rules
INSERT INTO `oxdel2delset` (`OXID`, `OXDELID`, `OXDELSETID`) VALUES
(MD5(CONCAT('tiramizoo', 'tiramizoo')), 'tiramizoo', 'tiramizoo');


# --- find shop id
SET @shopid = IF( ( SELECT oxedition='EE' FROM oxshops LIMIT 1 ), 1, 'oxbaseshop' );

# --- delivery set
INSERT INTO `oxdeliveryset` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`) VALUES
('tiramizoo', @shopid, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Tiramizoo', 'Tiramizoo', '', '', 10);

# --- delivery rules
INSERT INTO `oxdelivery` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`) VALUES
('tiramizoo', @shopid, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Tiramizoo', 'Tiramizoo', '', '', 'abs', 0.1, 'p', 0, 999999, 0, 1717, 0);
