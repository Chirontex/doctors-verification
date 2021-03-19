<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\StatusException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;
use CDBResult;

class Status
{

    protected $db;
    protected $table;

    public function __construct(CDatabase $db)
    {
        
        $this->db = $db;

        $this->table = 'docsver_status';

        $this->tableCreate();

    }

    /**
     * Create the provider table.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\StatusException
     */
    public function tableCreate() : self
    {

        if ($this->db->Query(
            "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT NOT NULL,
                `status` TINYINT NOT NULL DEFAULT 0,
                `modified` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `user_id` (`user_id`)
            )
            COLLATE='utf8_unicode_ci'
            AUTO_INCREMENT=0",
            true
        ) === false) throw new StatusException(
            ExceptionsList::PROVIDERS['-11']['mesage'].
                ' ("'.$this->table.'")',
            ExceptionsList::PROVIDERS['-11']['code']
        );

        return $this;

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

}
