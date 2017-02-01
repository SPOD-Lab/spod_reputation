<?php

/**
 * Created by PhpStorm.
 * User: darcas
 * Date: 25/01/2017
 * Time: 14:17
 */
class SPODREPUTATION_CMP_Leaderboard extends  OW_Component
{
    private $userId;
    private $leaderboard;
    private $position;

    public function __construct($params)
    {
        $this->userId = (int) $params['userId'];
        $js = "
SPODREPUTATION.showElse = function(score,userId,flag) {
    var params = {score:score,userId:userId,flag:flag};
    detailFloatBox.close();
    detailFloatBox = OW.ajaxFloatBox('SPODREPUTATION_CMP_Leaderboard', {params:params} , {iconClass: 'ow_ic_add', title: '".OW::getLanguage()->text('spodreputation','leaderboard_title')."'});
};
";
        OW::getDocument()->addOnloadScript($js);

        $example = new OW_Example();
        $example->andFieldEqual('userId',$this->userId);
        $this->leaderboard = SPODREPUTATION_BOL_Service::getInstance()->getLeaderboardForUser($this->userId);
        $this->position = SPODREPUTATION_BOL_Service::getInstance()->findUserPosition($this->userId);

        if($params['flag']) {
            if($params['flag']=='prev') {
                $this->leaderboard = SPODREPUTATION_BOL_Service::getInstance()->getPreviousLeaderboard($params['score']);
                $this->position = SPODREPUTATION_BOL_Service::getInstance()->findUserPosition($this->leaderboard[0]->userId);
            }
            else if($params['flag']=='next') {
                $this->leaderboard = SPODREPUTATION_BOL_Service::getInstance()->getNextLeaderboard($params['score']);
                $this->position = SPODREPUTATION_BOL_Service::getInstance()->findUserPosition($this->leaderboard[0]->userId);
            }
        }

        $idList = array();
        foreach ($this->leaderboard as $userRep) {
           $idList[] = $userRep->userId;
        }

        $cmp = new BASE_CMP_AvatarUserList($idList);
        $avatars = $cmp->getAvatarInfo($idList);
        $this->assign('users', $avatars);

        if( !empty($idList) ) {
            $this->addComponent('userList', new BASE_CMP_AvatarUserList($idList));
        }

        $this->assign('leaderboard',$this->leaderboard);
        $this->assign('position',$this->position);
        $this->assign('currentUser',$this->userId);
        $this->assign('loggedUser',OW::getUser()->getId());
        $this->assign('prefix','spodreputation+');
        $this->assign('components_url', SPODPR_COMPONENTS_URL);

    }
}