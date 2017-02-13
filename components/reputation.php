<?php

/**
 * Created by PhpStorm.
 * User: darcas
 * Date: 27/01/2017
 * Time: 10:41
 */
class SPODREPUTATION_CMP_Reputation extends  BASE_CLASS_Widget
{
    private $userId;
    private $reputationRecord;

    public function __construct(BASE_CLASS_WidgetParameter $paramObject)
    {
        parent::__construct();

        $this->userId =  $paramObject->additionalParamList['entityId'] != null ? $paramObject->additionalParamList['entityId'] : OW::getUser()->getId();

        if(OW::getPluginManager()->isPluginActive('ode') &&
            OW::getPluginManager()->isPluginActive('spodpublic') &&
            OW::getPluginManager()->isPluginActive('spodpr') &&
            OW::getPluginManager()->isPluginActive('cocreation') &&
            OW::getPluginManager()->isPluginActive('newsfeed') &&
            OW::getPluginManager()->isPluginActive('mailbox')
        ){
            SPODREPUTATION_CLASS_Evaluation::getInstance()->evaluate($this->userId);
        }

        $this->reputationRecord = SPODREPUTATION_BOL_Service::getInstance()->findByUserId($this->userId);

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spodreputation')->getStaticJsUrl() . 'spodreputation.js', 'text/javascript');
        $js = "
SPODREPUTATION.showLeaderboard = function(userId) {
    var params = {userId:userId};
    detailFloatBox = OW.ajaxFloatBox('SPODREPUTATION_CMP_Leaderboard', {userId:params} , {iconClass: 'ow_ic_add', title: '".OW::getLanguage()->text('spodreputation','leaderboard_title')."'});
};
";
        OW::getDocument()->addOnloadScript($js);

    }

    public static function getStandardSettingValueList() // If you redefine this method, you will be able to set default values for the standard widget settings.
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('spodreputation','widget_title'),
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_ICON => self::ICON_FLAG
        );
    }

    public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility
    {
        return self::ACCESS_MEMBER;
    }

    public function onBeforeRender()
    {
        $this->assign('reputationRecord',$this->reputationRecord);
        $this->assign('stars',SPODREPUTATION_CLASS_Evaluation::getInstance()->setLevelStar($this->reputationRecord->level));
    }
}