# DocsVer 0.2.5

Реализация проверки компетентности пользователя перед предоставлением ему доступа к контенту страницы посредством теста на 3 неповторяющихся вопроса, ограниченного по времени на 3 минуты и 3 попытки в сутки. Написана под 1С-Битрикс. Актуально для сайтов медицинской тематики и изначально разрабатывалось под них.

## Установка

1. Создайте в корне сайта директорию `doctors-verification` и скопируйте в неё содержимое репозитория.

2. Создайте в директории `doctors-verification/src` файл Questions.php — в нём будут содержаться все вопросы, из которых будет составляться тест. Файл должен содержать код подобного вида:

```php
<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer;

class Questions
{

    const ITEMS = [
        [
            'title' => 'Текст вопроса',
            'answers' => [
                'Вариант ответа 1',
                'Вариант ответа 2' // количество вариантов не ограничено
            ],
            'right' => 0 // ключ правильного варианта ответа
        ],
        [
            // Следующий вопрос...
            // Количество вопросов должно быть не меньше 3.
            // Максимум не ограничен.
        ]
    ];

}

```

3. Отредактируйте PHP-код страницы, на которую нужно поместить тест, подобным образом:

```php
<?php

use Chirontex\DocsVer\Main As DocsVer;
use Chirontex\DocsVer\Exceptions\MainException;

require_once __DIR__.'../../bitrix/header.php';
require_once __DIR__.'../../doctors-verification/autoload.php';

$APPLICATION->SetTitle('Заголовок страницы');

try {

    $docsver = new DocsVer((int)$USER->GetID(), $DB);

    if ($docsver->isContentAvailable()) {

?>
<p>Контент страницы.</p>
<?php

    } else $docsver->testingInit();

} catch (MainException $e) {

    if ($e->getCode() === -1) {

?>
<p><a href="/login?backurl=<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">Авторизуйтесь</a> или <a href="/login?register=yes&backurl=<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">зарегистрируйтесь</a>, чтобы просматривать содержимое данной страницы.</p>
<?php

    }

}

require_once __DIR__.'../../bitrix/footer.php';

```
