<?php
/**
 * Created by PhpStorm.
 * User: Dario
 * Date: 23/01/2017
 * Time: 15:20
 */
$widgetService = BOL_ComponentAdminService::getInstance();

$widget = $widgetService->addWidget('SPODREPUTATION_CMP_Reputation', true);
$widgetPlace = $widgetService->addWidgetToPlace($widget, BOL_ComponentService::PLACE_PROFILE);
$widgetService->addWidgetToPosition($widgetPlace, BOL_ComponentService::SECTION_LEFT, 1);