<!DOCTYPE html>
        <html lang="ru">
            <head>
                <meta charset="utf-8">
                <meta name="robots" content="noindex, nofollow" />
                <title>Данные из Планфикс</title>
                <!-- ⚠️ тут можно поправить надпись  -->

            </head>

            <style>
            .number {
                /* display: flex; */
                justify-content: center;
                align-items: center;
                font-size: 20px;
                font-family: -apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif;
            }
            </style>

        <body>

    <?php

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    //// ⚠️ тут нужно заполнить все поля своими значениями

    $planfixAccount = 'your-company';
    $planfixAccountUrl = 'https://your-company.planfix.ru';
    $secret = 'parole'; // выдуманный пароль − минимальная защита от произвольных срабытываний
    // $user = '1'; // айдишник(general) из урла пользователя в Планфикс

    // Управление аккаунтом > Доступ к API > XML API
    $APIkey = '...'; // API key
    $APItoken = '...'; // сделать токен, разрешенные функции - task.getList

    $xmlApiUrl = 'https://apiru.planfix.ru/xml/'; // Адрес для API запросов
                // https://apiru.planfix.ru/xml/ - для аккаунтов расположенных в России
                // https://api.planfix.com/xml/ - для аккаунтов расположенных в Европе
                // https://api-sg.planfix.com/xml/ - для аккаунтов расположенных в Азии
                // https://api-us.planfix.com/xml/ - для аккаунтов расположенных в Америке

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////


    // из урла получаем данные по  параметрам
    // https://your-website.ru/some-folder/task-in-filter.php?secret=parole&filter=123456

    $filter=htmlspecialchars($_GET['filter']);
    $parole=htmlspecialchars($_GET['secret']);
    $user=htmlspecialchars($_GET['user']);
    // [TODO] тут добавить проверку наличия параметров

    if ($parole == $secret) {

        //запрос к Планфикс https://planfix.com/ru/help/ПланФикс_API_task.getList
        $xmlRequest = <<<XML
          <request method="task.getList">
          <account>{$planfixAccount}</account>
            <user>
              <general>{$user}</general>
            </user>
            <target>{$filter}</target>
            <pageCurrent>0</pageCurrent>
          </request>
        XML;

        $ch = curl_init();
        $headers = ['Content-type: text/plain; charset=utf-8'];
        curl_setopt_array($ch, [
            CURLOPT_URL => $xmlApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $xmlRequest,
            CURLOPT_USERPWD => $APIkey .':' . $APItoken ,
            CURLOPT_HTTPHEADER => $headers

        ]);
        $output = curl_exec($ch);
        curl_close($ch);
        $xml = new SimpleXMLElement($output);
        if (!$xml || $xml['status']=='error')
            echo "🤡 Ошибка";
        else if ($xml['status']=='ok') {

            echo '<div class="number">';
            echo '<span>Задач в фильтре:&nbsp;</span>'; // ⚠️ тут можно отредактировать текст
            echo $xml->tasks['totalCount'];
            echo '<span>&nbsp;<a href="' . $planfixAccountUrl . '/?action=tasks&filter=' . $filter . '" style="font-size: 0.8rem;">(фильтр задач)</a></span>'; // ⚠️ тут можно отредактировать текст
            echo '</div>';

        }
    }
    ?>
    </body>
</html>
