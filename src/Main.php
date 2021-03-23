<?php
/**
 * Doctors Verification 0.2.5 by Dmitry Shumilin
 * License: GNU GPL v3, see LICENSE
 */
namespace Chirontex\DocsVer;

use Chirontex\DocsVer\Providers\Status;
use Chirontex\DocsVer\Providers\Testing;
use Chirontex\DocsVer\Exceptions\MainException;
use Chirontex\DocsVer\Exceptions\ExceptionsList;
use CDatabase;

class Main
{

    protected $db;
    protected $user_id;
    protected $status_provider;
    protected $status_data;
    protected $testing_provider;
    protected $testing_data;

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

        if (empty(
            $this->status_data
        )) $this->status_data = $this->status_provider
        ->statusSet($this->user_id, 0)
        ->statusGet($this->user_id);

        $this->testing_provider = new Testing($this->db);

        $this->testing_data = $this->testing_provider->dataGet($this->user_id);

        $this->testing_data = empty($this->testing_data) ?
            $this->testing_provider
                ->dataCreate($this->user_id)
                ->dataGet($this->user_id) :
            $this->testing_data;

        if (isset($_POST['docsver_questions'])) $this->testingCheck();

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

    /**
     * Check available testing tries and launch
     * the testing or not.
     * 
     * @return $this
     */
    public function testingInit() : self
    {

        date_default_timezone_set('Europe/Moscow');

        $day_passed = (time() - strtotime(
            $this->status_data['modified']
        )) >= 86400;

        if ($day_passed) {
            
            $this->testing_provider->dataUpdate($this->user_id, 3);

            $this->testing_data['tries'] = 3;
        
        }

        if ((int)$this->testing_data['tries'] === 0 &&
            !$day_passed) $this->testingDeny();
        elseif ((int)$this->testing_data['tries'] > 0) {

            /*$this->status_data = $this->status_provider
                ->statusSet($this->user_id, 0)
                ->statusGet($this->user_id);*/

            $this->testing_provider->dataUpdate(
                $this->user_id,
                (int)$this->testing_data['tries'] - 1
            );

            $this->testing_data['tries'] -= 1;

            $this->testingProcess();

        }

        return $this;

    }

    /**
     * Called if the user is not allowed to testing.
     * 
     * @return $this
     */
    protected function testingDeny() : self
    {

        ob_start();

?>
<p>Вы исчерпали суточное количество попыток пройти тест. Попробуйте завтра.</p>
<?php

        echo ob_get_clean();

        return $this;

    }

    /**
     * Select questions and output the test.
     * 
     * @return $this
     */
    protected function testingProcess() : self
    {

        $questions = [];

        for ($i = 0; $i < 3; $i++) {

            do {

                $r = rand(0, count(Questions::ITEMS) - 1);

            } while (array_search($r, $questions) !== false);

            $questions[] = $r;

        }

        ob_start();

?>
<p style="margin-top: 1rem; margin-bottom: 1rem;">Нам необходимо произвести проверку Вашей принадлежности к медицинскому сообществу.<br />Пожалуйста, ответьте на нижеприведённые вопросы в течение трёх минут.</p>
<p>Осталось времени: <span id="docsver-timer">03:00</span></p>
<form action="" method="post">
<?php

        for ($i = 0; $i < count($questions); $i++) {

?>
    <div>
        <h3><?= htmlspecialchars(Questions::ITEMS[$questions[$i]]['title']) ?></h3>
<?php

            foreach (Questions::ITEMS[$questions[$i]]['answers'] as $key => $answer) {

?>
        <p><input type="radio" name="docsver_question_<?= $questions[$i] ?>" id="docsver_question_<?= $questions[$i] ?>" value="<?= $key ?>" required="true"<?= $key === 0 ? 'checked="true"' : '' ?>> <?= htmlspecialchars($answer) ?></p>
<?php

            }

?>
    </div>
<?php

        }

        $questions = implode('-', $questions);

?>
    <input type="hidden" name="docsver_questions" value="<?= $questions ?>">
    <button type="submit">Отправить</button>
</form>
<script src="<?= empty($_SERVER['HTTPS']) ? 'http' : 'https' ?>://<?= $_SERVER['HTTP_HOST'] ?>/doctors-verification/js/script.js?ver=1.0.5"></script>
<?php

        echo ob_get_clean();

        $this->status_data = $this->status_provider->statusSet(
            $this->user_id,
            (int)$this->status_data['status'],
            $questions
        )->statusGet($this->user_id);

        return $this;

    }

    /**
     * Check the answers.
     * 
     * @return $this
     */
    protected function testingCheck() : self
    {

        date_default_timezone_set('Europe/Moscow');

        $check = false;

        if ($_POST['docsver_questions'] ===
            $this->status_data['last_questions']) {

            $check = true;

            if (time() - strtotime($this->status_data['modified']) < 180) {

                $questions = explode('-', $_POST['docsver_questions']);

                foreach ($questions as $key) {

                    if ((int)$_POST['docsver_question_'.$key] !==
                        Questions::ITEMS[$key]['right']) {

                        $check = false;

                        break;

                    }

                }

            } else $check = false;

        }

        if ($check) $this->status_data = $this->status_provider
            ->statusSet($this->user_id, 1)
            ->statusGet($this->user_id);
        elseif ($this->testing_data['tries'] > 0) {

            ob_start();

?>
<p style="color: red;">Вы не прошли тест. Попробуйте ещё раз.</p>
<?php

            echo ob_get_clean();

        }

        return $this;

    }

}
