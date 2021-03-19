<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\TestingException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;
use CDBResult;

class Testing extends Provider
{

    public function __construct(CDatabase $db)
    {
        
        $this->table = 'docsver_testing';

        $this->columns = [
            'user_id' => 'BIGINT UNSIGNED NOT NULL',
            'tries' => 'TINYINT UNSIGNED NOT NULL'
        ];

        $this->indexes = [
            'user_id' => 'UNIQUE INDEX'
        ];

        parent::__construct($db);

    }

    /**
     * Get the testing data.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @return array
     * 
     * @throws Chirontex\DocsVer\Exceptions\TestingException
     */
    public function dataGet(int $user_id) : array
    {

        if ($user_id < 1) throw new TestingException(
            ExceptionsList::COMMON['-2']['message'],
            ExceptionsList::COMMON['-2']['code']
        );

        $select = $this->db->Query(
            "SELECT *
                FROM `".$this->table."` AS t
                WHERE t.user_id = '".$user_id."'",
            true
        );

        if ($select instanceof CDBResult) {

            $row = $select->Fetch();

            return $row === false ? [] : $row;

        } else throw new TestingException(
            ExceptionsList::PROVIDERS['-12']['message'],
            ExceptionsList::PROVIDERS['-12']['code']
        );

    }

    /**
     * Add the testing data.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\TestingException
     */
    public function dataCreate(int $user_id) : self
    {

        if ($user_id < 1) throw new TestingException(
            ExceptionsList::COMMON['-2']['message'],
            ExceptionsList::COMMON['-2']['code']
        );

        if ($this->db->Insert(
            $this->table,
            [
                'user_id' => $user_id,
                'tries' => 3
            ],
            "",
            false,
            "",
            true
        ) === false) throw new TestingException(
            ExceptionsList::PROVIDERS['-13']['message'].
                ' ('.__CLASS__.'::'.__METHOD__.')',
            ExceptionsList::PROVIDERS['-13']['code']
        );

        return $this;

    }

    /**
     * Update the testing data.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @param int $tries
     * Max 3, min 0.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\TestingException
     */
    public function dataUpdate(int $user_id, int $tries) : self
    {

        if ($user_id < 1) throw new TestingException(
            ExceptionsList::COMMON['-2']['message'],
            ExceptionsList::COMMON['-2']['code']
        );

        if ($tries < 0) $tries = 0;
        elseif ($tries > 3) $tries = 3;

        if ($this->db->Update(
            $this->table,
            ['tries' => $tries],
            "user_id = '".$user_id."'",
            "",
            false,
            true
        ) === false) throw new TestingException(
            ExceptionsList::PROVIDERS['-14']['message'].
                ' ('.__CLASS__.'::'.__METHOD__.')',
            ExceptionsList::PROVIDERS['-14']['code'] 
        );

        return $this;

    }

}
