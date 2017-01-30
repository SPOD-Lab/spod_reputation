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

SPODREPUTATION_BOL_Service::getInstance()->initDb();