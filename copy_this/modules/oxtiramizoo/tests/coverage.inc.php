<?php

// Add "PHPUnit_Util_Filter::addFileToWhitelist( PATH_TO_FILE )" to add files to coverage
// you can use the 'oxPATH' constant as the path to the shops root

//PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/oxtiramizoo/tests' );

PHPUnit_Util_Filter::addDirectoryToWhitelist( oxPATH . '/modules/oxtiramizoo/' );
PHPUnit_Util_Filter::addFileToFilter(oxPATH . '/modules/oxtiramizoo/api.php');
PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH . '/modules/oxtiramizoo/tests/');
