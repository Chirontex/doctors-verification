<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Providers;

use Chirontex\DocsVer\Exceptions\ProviderException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;

abstract class Provider
{

    protected $db;
    protected $table = '';
    protected $columns = [];
    protected $indexes = [];

    public function __construct(CDatabase $db)
    {
        
        $this->db = $db;

        $this->tableCreate();

    }

    /**
     * Create the table.
     * 
     * @return $this
     * 
     * @throws Chirontex\DocsVer\Exceptions\ProviderException
     */
    public function tableCreate() : self
    {

        $fn = function(
            array $entities,
            string $format
        ) : string {

            $result = '';

            foreach ($entities as $key => $value) {

                $result .= ", ".sprintf($format, $key, $value);

            }

            return $result;

        };

        $columns = call_user_func(
            $fn,
            $this->columns,
            "`%1\$s` %2\$s"
        );

        $indexes = call_user_func(
            $fn,
            $this->indexes,
            "%2\$s `%1\$s` (`%1\$s`)"
        );

        if ($this->db->Query(
            "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                `id` BIGINT NOT NULL AUTO_INCREMENT".$columns.",
                PRIMARY KEY (`id`)".$indexes."
            )
            COLLATE='utf8_unicode_ci'
            AUTO_INCREMENT=0"
        ) === false) throw new ProviderException(
            ExceptionsList::PROVIDERS['-11']['message'],
            ExceptionsList::PROVIDERS['-11']['code']
        );

        return $this;

    }

}
