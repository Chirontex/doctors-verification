<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\StatusProviderException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;
use CDBResult;

class StatusProvider
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
     * @throws Chirontex\DocsVer\Exceptions\StatusProviderException
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
        ) === false) throw new StatusProviderException(
            ExceptionsList::PROVIDERS['table_creation_failure']['mesage'].
                ' ("'.$this->table.'")',
            ExceptionsList::PROVIDERS['table_creation_failure']['code']
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
    public function statusGet(int $user_id) : int
    {

        if ($user_id < 1) throw new StatusProviderException(
            ExceptionsList::COMMON['invalid_user_id']['message'],
            ExceptionsList::COMMON['invalid_user_id']['code']
        );

        $select = $this->db->Query(
            "SELECT t.status
                FROM `".$this->table."` AS t
                WHERE t.user_id = '".$user_id."'",
            true
        );

        if ($select instanceof CDBResult) {

            $result = 0;

            $row = $select->Fetch();

            if (isset($row['status'])) $result = (int)$row['status'];

            return $result;

        } else throw new StatusProviderException(
            ExceptionsList::PROVIDERS['select_failure']['message'],
            ExceptionsList::PROVIDERS['select_failure']['code']
        );

    }

}
