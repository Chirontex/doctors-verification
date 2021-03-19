<?php
/**
 * Doctors Verification 0.1.5 by Dmitry Shumilin
 * License: GNU GPL v3, see LICENSE
 */
namespace Chirontex\DocsVer;

use Chirontex\DocsVer\Providers\Status;
use Chirontex\DocsVer\Providers\Testing;
use Chirontex\DocsVer\Exceptions\MainException;
use Chirontex\DocsVer\Exceptions\StatusException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;

class Main
{

    protected $db;
    protected $user_id;
    protected $status_provider;
    protected $status_data;

    public function __construct(int $user_id, CDatabase $db)
    {
        
        if (empty($user_id)) throw new MainException(
            ExceptionsList::COMMON['-1']['message'],
            ExceptionsList::COMMON['-1']['code']
        );

        $this->user_id = $user_id;

        $this->db = $db;

        $this->status_provider = new Status($db);

        $this->status_data = $this->status_provider->statusGet($this->user_id);

    }

    /**
     * Allow to check the content availability.
     * 
     * @return bool
     */
    public function isContentAvailable() : bool
    {

        return !(empty($this->status_data) ||
            (int)$this->status_data['status'] === 0);

    }

    public function testingInit() : self
    {

        $testing = new Testing($this->db);

        $data = $testing->dataGet($this->user_id);

        $data = empty($data) ?
            $testing
                ->dataCreate($this->user_id)
                ->dataGet($this->user_id) :
            $data;

        $this->status_data = $this->status_provider
            ->statusSet($this->user_id, 0)
            ->statusGet($this->user_id);

        echo 'Your status data:<br />';

        var_dump($this->status_data);

        echo '<br />';
        echo 'Your testing data:<br />';

        var_dump($data);

        return $this;

    }

}
