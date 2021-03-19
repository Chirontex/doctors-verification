<?php
/**
 * Doctors Verification 0.0.7 by Dmitry Shumilin
 * License: GNU GPL v3, see LICENSE
 */
namespace Chirontex\DocsVer;

use Chirontex\DocsVer\Providers\Status;
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
            ExceptionsList::COMMON['-1']['message'],
            ExceptionsList::COMMON['-1']['code']
        );

        $this->user_id = $user_id;

        $this->status_provider = new Status($db);

    }

}
