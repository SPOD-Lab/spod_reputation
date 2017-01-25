<?php
/**
 * Created by PhpStorm.
 * User: Dario
 * Date: 23/01/2017
 * Time: 15:20
 */

//$path = OW::getPluginManager()->getPlugin('spodreputation')->getRootDir() . 'langs.zip';
//BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spodreputation');

//BOL_LanguageService::getInstance()->addPrefix('spodreputation', 'SPOD Reputation');
//OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('spodreputation')->getRootDir().'langs.zip', 'spodreputation');

$sql = 'CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'spodreputation_reputation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `reputation` int(100),
  `level` varchar(500),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;';

OW::getDbo()->query($sql);