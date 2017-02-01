<?php

/**
 * Created by PhpStorm.
 * User: darcas
 * Date: 23/01/2017
 * Time: 15:28
 */
class SPODREPUTATION_BOL_Service
{
    /**
     * Class instance
     *
     * @var SPODREPUTATION_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return $reputationService SPODREPUTATION_BOL_Service
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var SPODREPUTATION_BOL_ReputationDao
     */
    private $reputationDao;

    private function __construct()
    {
        $this->reputationDao = SPODREPUTATION_BOL_ReputationDao::getInstance();
    }

    /**
     * @param $userId int the user's id
     * @return SPODREPUTATION_BOL_Reputation reputation info for the actual user
     */
    public function findByUserId($userId) {
        $id = (int) $userId;
        $example = new OW_Example();
        $example->andFieldEqual('userId',$id);

        $reputationRecord = $this->reputationDao->findObjectByExample($example);
        return $reputationRecord;
    }

    public function getLeaderboardForUser($userId) {
        $userRep = $this->findByUserId($userId);
        $example = new OW_Example();
        $example->andFieldLessOrEqual('reputation',$userRep->reputation);
        $example->setOrder('reputation DESC');
        $example->setLimitClause(0,10);
        return $this->reputationDao->findListByExample($example);
    }

    public function getPreviousLeaderboard($bound) {
        $example = new OW_Example();
        $example->andFieldGreaterThenOrEqual('reputation',$bound);
        $example->setOrder('reputation ASC');
        $example->setLimitClause(0,10);
        return array_reverse($this->reputationDao->findListByExample($example));
    }

    public function getNextLeaderboard($bound) {
        $example = new OW_Example();
        $example->andFieldLessOrEqual('reputation',$bound);
        $example->setOrder('reputation DESC');
        $example->setLimitClause(0,10);
        return $this->reputationDao->findListByExample($example);
    }

    public function findUserPosition($userId) {
        $userRep = $this->findByUserId($userId);
        $example = new OW_Example();
        $example->andFieldGreaterThan('reputation',$userRep->reputation);
        $count = count($this->reputationDao->findListByExample($example));
        return ++$count;
    }

    public function update($reputationValue,$userId) {
        $reputation = $this->findByUserId($userId);
        if($reputation != null) {
            if($reputationValue > $reputation->reputation) {
                $reputation->reputation = $reputationValue;
                switch ($reputationValue) {
                    case $reputationValue<100:
                        $reputation->level = 'Beginner';
                        break;
                    case $reputationValue<200:
                        $reputation->level = 'Advanced Beginner';
                        break;
                    case $reputationValue<400:
                        $reputation->level = 'Competent Performer';
                        break;
                    case $reputationValue<800:
                        $reputation->level = 'Proficient Performer';
                        break;
                    case $reputationValue<1200:
                        $reputation->level = 'Expert';
                        break;
                    case $reputationValue<2400:
                        $reputation->level = 'Master';
                        break;
                    default:
                        $reputation->level = 'GrandMaster';
                        break;
                }
                $this->reputationDao->save($reputation);
            }
        }
    }

    public function initDb() {
        $users = BOL_UserDao::getInstance()->findAll();
        foreach ($users as $user) {
            $reputation = $this->findByUserId($user->id);
            if($reputation == null){
                $reputation = new SPODREPUTATION_BOL_Reputation();
                $reputation->userId = $user->id;
                $reputation->reputation = 0;
                $reputation->level = 'Beginner';
                $reputation->weekReputation = 0;
                $reputation->timestamp = '2000-01-01 00:00:00';
                $this->reputationDao->save($reputation);
            }
            else if( (time() - strtotime($reputation->timestamp)) >= 604800 ) {
                $reputation->weekReputation = 0;
                $reputation->timestamp = date('Y-m-d',time());
                $this->reputationDao->save($reputation);
            }
            SPODREPUTATION_CLASS_Evaluation::getInstance()->evaluate($user->id);
        }
    }
}