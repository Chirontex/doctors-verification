<?php
/**
 * Doctors Verification 0.1.0 by Dmitry Shumilin
 * License: GNU GPL v3, see LICENSE
 */
namespace Chirontex\DocsVer;

use Chirontex\DocsVer\Providers\Status;
use Chirontex\DocsVer\Exceptions\MainException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;

final class Main
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

        $status = $this->status_provider->statusGet($this->user_id);

        if (empty($status) ||
            (int)$status['status'] === 0) $this->testing($status);
        else echo $this->content();

    }

    public function testing(array $status) : self
    {

        echo 'testing';

        return $this;

    }

    public function content() : string
    {

        return 'page content';

    }

}
