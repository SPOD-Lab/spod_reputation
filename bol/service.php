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
}