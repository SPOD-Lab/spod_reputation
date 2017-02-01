<?php

/**
 * Created by PhpStorm.
 * User: darcas
 * Date: 25/01/2017
 * Time: 11:36
 */
class SPODREPUTATION_CLASS_Evaluation
{
    private static $classInstance;

    public static function getInstance()
    {
        if(self::$classInstance === null)
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    protected function __construct() {

    }

    public function evaluate($userId) {
        $reputationValue = 0;
        //var_dump(intval( $this->getLikes($userId) * SPODREPUTATION_CLASS_Constants::LIKE_WEIGHT )." like\n");
        $reputationValue += intval( $this->getLikes($userId)  * SPODREPUTATION_CLASS_Constants::LIKE_WEIGHT );
        //var_dump(intval( ($this->getCommentsOnPosts($userId) + count( $this->getCommentsOnComments($userId)) ) * SPODREPUTATION_CLASS_Constants::COMMENT_WEIGHT )." comm\n");
        $reputationValue += intval( ($this->getCommentsOnPosts($userId) + count( $this->getCommentsOnComments($userId)) ) * SPODREPUTATION_CLASS_Constants::COMMENT_WEIGHT );
        //var_dump(intval( count( $this->getPersonalMessages($userId) ) * SPODREPUTATION_CLASS_Constants::MESSAGE_WEIGHT )." message\n");
        $reputationValue += intval( count( $this->getPersonalMessages($userId) ) * SPODREPUTATION_CLASS_Constants::MESSAGE_WEIGHT );
        //var_dump(intval( count( $this->getUserFollower($userId) ) * SPODREPUTATION_CLASS_Constants::FOLLOWER_WEIGHT )." follow\n");
        $reputationValue += intval( count( $this->getUserFollower($userId) ) * SPODREPUTATION_CLASS_Constants::FOLLOWER_WEIGHT );
        //var_dump(intval( $this->getAgoraInfo($userId) )." agora\n");
        $reputationValue += intval( $this->getAgoraInfo($userId) );
        //var_dump(intval( count( $this->getDataletDatasetUserBased($userId)) * SPODREPUTATION_CLASS_Constants::DATALET_COCREATED_DATASET )." datalet su dataset cocreato da user\n");
        $reputationValue += intval( count( $this->getDataletDatasetUserBased($userId)) * SPODREPUTATION_CLASS_Constants::DATALET_COCREATED_DATASET );
        //var_dump(intval( count( $this->getCocreationRoomJoined($userId)) * SPODREPUTATION_CLASS_Constants::ROOM_JOINED_WEIGHT)." room joined\n");
        $reputationValue += intval( count( $this->getCocreationRoomJoined($userId)) * SPODREPUTATION_CLASS_Constants::ROOM_JOINED_WEIGHT);
        //var_dump(intval( count( $this->getDataletComment($userId)) * SPODREPUTATION_CLASS_Constants::DATALET_COMMENT_WEIGHT)." datalet in comm\n");
        //$reputationValue += intval( count( $this->getDataletComment($userId)) * SPODREPUTATION_CLASS_Constants::DATALET_COMMENT_WEIGHT); //not correctly evalued
        //var_dump(intval( count( $this->getPostUserWall($userId)) * SPODREPUTATION_CLASS_Constants::ONMYWALL_WEIGHT)." wall\n");
        $reputationValue += intval( count( $this->getPostUserWall($userId)) * SPODREPUTATION_CLASS_Constants::ONMYWALL_WEIGHT);

        SPODREPUTATION_BOL_Service::getInstance()->update($reputationValue,$userId);
    }

    /**
     * get all likes received by the user
     * @param $userId int user's id
     */
    protected function getLikes($userId) {
        $service = NEWSFEED_BOL_Service::getInstance();
        $actions = $service->findActionsByUserId($userId);
        $likes=0;
        foreach ($actions as $action){
            $likes += count($service->findEntityLikes($action->entityType, $action->entityId));
        }
        return $likes;
    }

    /**
     * get all other users post on user's personal wall
     */
    protected function getPostUserWall($userId){
        $postOnWall = array();
        $actions = NEWSFEED_BOL_ActionDao::getInstance()->findAll();
        $username = BOL_UserService::getInstance()->getUserName($userId);
        foreach ($actions as $action) {
            $data = json_decode($action->data);
            if($data->{'context'}->{'label'}) {
                if(!strcasecmp($data->{'context'}->{'label'},$username)) {
                    $postOnWall[] = $action;
                }
            }
        }
        return $postOnWall;
    }


    /**
     * get all comments received by the user
     * @param $userId int user's id
     */
    protected function getCommentsOnPosts($userId)
    {
        $example = new OW_Example();
        $example->andFieldNotEqual('userId',$userId);
        $commentsNotUser = BOL_CommentDao::getInstance()->findListByExample($example);
        $commentEntities = array();
        foreach ($commentsNotUser as $comment) {
            $commentEntities[] = BOL_CommentEntityDao::getInstance()->findById($comment->commentEntityId);
        }
        $newsfeedStatus = array();
        foreach ($commentEntities as $commentEntity) {
            if($commentEntity->entityType == 'user-status') {
                $newsfeedStatus[] = NEWSFEED_BOL_ActionDao::getInstance()->findAction($commentEntity->entityType,$commentEntity->entityId);
            }
        }
        $count = 0;
        foreach ($newsfeedStatus as $status) {
            $data = json_decode($status->data);
            if($data->{'data'}->{'userId'} == $userId) {
                $count += 1;
            }
        }
        return $count;

    }

    /**
     * get all comments received under all the user's comment
     * @param $userId int user's id
     */
    protected function getCommentsOnComments($userId) {
        //stub
        return array();
    }

    /**
     * get all personal messages received by other users
     * @param $userId int user's id
     */
    protected function getPersonalMessages($userId) {
        $example = new OW_Example();
        $example->andFieldEqual('interlocutorId',$userId);
        $example->andFieldNotEqual('initiatorId',$userId);
        $conversationsReceived = MAILBOX_BOL_ConversationDao::getInstance()->findListByExample($example);
        return $conversationsReceived;
    }

    /**
     * get all the followers of the user
     * @param $userId int user's id
     */
    protected function getUserFollower($userId) {
        $service = NEWSFEED_BOL_Service::getInstance();
        $followerList = $service->findFollowList('user',$userId,'everybody');
        return $followerList;
    }

    /**
     * get all info about user's rooms
     * @param $userId int user's id
     * @return $count int sum of view opendata and comment
     */
    protected function getAgoraInfo($userId) {
        $service = SPODPUBLIC_BOL_Service::getInstance();
        $rooms = $service->getPublicRoomsByOwner($userId);
        $count = 0;
        foreach ($rooms as $room) {
            $count += intval($room->views) * SPODREPUTATION_CLASS_Constants::AGORA_VIEW_WEIGHT; //count public room views
            $count += intval($room->comments) * SPODREPUTATION_CLASS_Constants::AGORA_COMMENT_WEIGHT; //count public room comments
            $count += intval($room->opendata) * SPODREPUTATION_CLASS_Constants::AGORA_OPENDATA_WEIGHT; //count public room attachment and datalet
        }
        return $count;
    }

    /**
     * get all the datalets created with user's cocreated datasets
     * @param $userId int user's id
     * @return array datalets based on user's datasets
     */
    protected function getDataletDatasetUserBased($userId) {
        $dataletBasedOnUserDataset = array();
        $service = COCREATION_BOL_Service::getInstance();
        $rooms = $service->getAllRooms();
        $datalets = ODE_BOL_Service::getInstance()->getAll();
        foreach ($datalets as $datalet) {
            if( $datalet->ownerId != $userId) {
                $params = json_decode($datalet->params);
                foreach ($rooms as $room) {
                    if($service->isMemberJoinedToRoom($userId,$room->id)) {
                        if(preg_match("/(http:\/\/)[0-9]{3}[.][0-9]{3}[.][0-9]{3}[.][0-9]{3}\/(.)*\/?room_id=".$room->id."/", $params->{'data-url'}, $output_array)) {
                            $dataletBasedOnUserDataset[] = $datalet;
                        }
                    }
                }
            }
        }
        return $dataletBasedOnUserDataset;
    }

    /**
     * get all the cocreation rooms joined by the user
     * @param $userId int user's id
     * @return array cocreation rooms joined by the user
     */
    protected function getCocreationRoomJoined($userId) {
        $roomsJoined = array();
        $service = COCREATION_BOL_Service::getInstance();
        $rooms = $service->getAllRooms();
        foreach ($rooms as $room) {
            if($room->ownerId != $userId) {
                if ($service->isMemberJoinedToRoom($userId,$room->id)) {
                    $roomsJoined[] = $room;
                }
            }
        }
        return $roomsJoined;
    }

    protected function getDataletComment($userId) {
        $dataletsInComments = array();
        $example = new OW_Example();
        $example->andFieldNotEqual('userId',$userId);
        $comments = BOL_CommentDao::getInstance()->findListByExample($example);

        foreach ($comments as $comment) {
            if (ODE_BOL_Service::getInstance()->getDataletByPostId($comment->id,'comment') != null)
                $dataletsInComments[] = ODE_BOL_Service::getInstance()->getDataletByPostId($comment->id,'comment');
        }

        return $dataletsInComments;
    }

    public function setLevelColor($level) {
        switch ($level) {
            case 'Beginner':
                return '#0099ff';
            case 'Advanced Beginner':
                return '#3333cc';
            case 'Competent Performer':
                return '#66ff33';
            case 'Proficient Performer':
                return '#990066';
            case 'Expert':
                return '#cc9900';
            case 'Master':
                return '#ffff00';
            case 'GrandMaster':
                return '#ff0000';
            default:
                return '#ffffff';
        }
    }

    public function setLevelStar($level) {
        switch ($level) {
            case 'Beginner':
                return 1;
            case 'Advanced Beginner':
                return 2;
            case 'Competent Performer':
                return 3;
            case 'Proficient Performer':
                return 4;
            case 'Expert':
                return 5;
            case 'Master':
                return 6;
            case 'GrandMaster':
                return 7;
            default:
                return 0;
        }
    }
}