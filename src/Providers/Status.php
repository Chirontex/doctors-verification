<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\StatusException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;
use CDBResult;

class Status extends Provider
{

    public function __construct(CDatabase $db)
    {

        $this->table = 'docsver_status';

        $this->columns = [
            'user_id' => 'BIGINT NOT NULL',
            'status' => 'TINYINT NOT NULL DEFAULT 0',
            'modified' => 'DATETIME NOT NULL'
        ];

        $this->indexes = [
            'user_id' => 'UNIQUE INDEX'
        ];

        parent::__construct($db);

    }

    /**
     * Get the verification status.
     * 
     * @param int $user_id
     * 
     * @return int
     * 
     * @throws Chirontex\DocsVer\Exceptions\StatusProviderException
     */
    public function statusGet(int $user_id) : array
    {

        if ($user_id < 1) throw new StatusException(
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

        } else throw new StatusException(
            ExceptionsList::PROVIDERS['-12']['message'],
            ExceptionsList::PROVIDERS['-12']['code']
        );

    }

    /**
     * Set the status.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @param int $status
     * If status < 1, it will be === 0.
     * Otherwise === 1.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\StatusProviderException
     */
    public function statusSet(int $user_id, int $status) : self
    {

        return empty($this->statusGet($user_id)) ?
            $this->statusInsert($user_id, $status) :
            $this->statusUpdate($user_id, $status);

    }

    /**
     * Insert the status.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @param int $status
     * If status < 1, it will be === 0.
     * Otherwise === 1.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\StatusProviderException
     */
    protected function statusInsert(int $user_id, int $status) : self
    {

        if ($user_id < 1) throw new StatusException(
            ExceptionsList::COMMON['-2']['message'],
            ExceptionsList::COMMON['-2']['code']
        );

        if ($status < 1) $status = 0;
        else $status = 1;

        if ($this->db->Insert(
            $this->table,
            [
                'user_id' => $user_id,
                'status' => $status,
                'modified' => date("Y-m-d H:i:s")
            ],
            "",
            false,
            "",
            true
        ) === false) throw new StatusException(
            ExceptionsList::PROVIDERS['-13']['message'].
                ' ('.__CLASS__.'::'.__METHOD__.')',
            ExceptionsList::PROVIDERS['-13']['code']
        );

        return $this;

    }

    /**
     * Update the status.
     * 
     * @param int $user_id
     * User ID cannot be lesser than 1.
     * 
     * @param int $status
     * If status < 1, it will be === 0.
     * Otherwise === 1.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\StatusProviderException
     */
    protected function statusUpdate(int $user_id, int $status) : self
    {

        if ($user_id < 1) throw new StatusException(
            ExceptionsList::COMMON['-2']['message'],
            ExceptionsList::COMMON['-2']['code']
        );

        if ($status < 1) $status = 0;
        else $status = 1;

        if ($this->db->Update(
            $this->table,
            [
                'status' => $status,
                'modified' => date("Y-m-d H:i:s")
            ],
            "user_id = '".$user_id."'",
            "",
            false,
            true
        ) === false) throw new StatusException(
            ExceptionsList::PROVIDERS['-14']['message'].
                ' ('.__CLASS__.'::'.__METHOD__.')',
            ExceptionsList::PROVIDERS['-14']['code']
        );

        return $this;

    }

}
