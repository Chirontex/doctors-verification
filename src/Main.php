<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer;

use Chirontex\DocsVer\Providers\StatusProvider;
use Chirontex\DocsVer\Exceptions\MainException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;

class Main
{

    protected $user_id;
    protected $status_provider;

    public function __construct(int $user_id, CDatabase $db)
    {
        
        if (empty($user_id)) throw new MainException(
            ExceptionsList::COMMON['user_not_authorized']['message'],
            ExceptionsList::COMMON['user_not_authorized']['code']
        );

        $this->user_id = $user_id;

        $this->status_provider = new StatusProvider($db);

    }

}
