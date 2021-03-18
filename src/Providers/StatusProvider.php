<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\StatusProviderException;
use CDatabase;

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
            "CREATE TABLE `".$this->table."` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT NOT NULL,
                `status` TINYINT NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `user_id` (`user_id`)
            )
            COLLATE='utf8_unicode_ci'
            AUTO_INCREMENT=0",
            true
        ) === false) throw new StatusProviderException(
            'Table "'.$this->table.'" creation failed.',
            -11
        );

        return $this;

    }

}
