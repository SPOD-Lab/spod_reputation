<?php
/**
 * Created by PhpStorm.
 * User: Dario
 * Date: 23/01/2017
 * Time: 15:20
 */
$preference = BOL_PreferenceService::getInstance()->findPreference('spodpr_components_url');
$spodpr_components_url = empty($preference) ? "http://deep.routetopa.eu/deep-components/" : $preference->defaultValue;
define("SPOD_COMPONENTS_URL", $spodpr_components_url);

if(OW::getPluginManager()->isPluginActive('ode') &&
    OW::getPluginManager()->isPluginActive('spodpublic') &&
    OW::getPluginManager()->isPluginActive('spodpr') &&
    OW::getPluginManager()->isPluginActive('cocreation') &&
    OW::getPluginManager()->isPluginActive('newsfeed') &&
    OW::getPluginManager()->isPluginActive('mailbox')
){
    SPODREPUTATION_BOL_Service::getInstance()->initDb();
}