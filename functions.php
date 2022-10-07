<?php

/**
* Переносит строку по заданному кол-ву символов
* с учётом кодировки
* @param string $string Строка для переноса
* @param int $width Количество символов
* @param string $break Символы переноса строки
*
* @return string Строка
*/
function utf8Wordwrap(string $string, int $width, string $break = "#!#!"): string
{
    $pattern = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
    $replace = '$1'.$break;

    return preg_replace($pattern, $replace, $string);
}

/**
 * Обрезает строку в параграфе, если его длина больше лимита,
 * добавляет ссылку на пост
 * @param string $string Строка
 * @param int $post_id Пост
 * @param int $limit Максимальная длина строки
 *
 * @return string Параграф или параграф со ссылкой на пост «Читать далее»
 */
function clipPostText(string $string, int $post_id, int $limit = 300): string
{
    if (mb_strlen($string) > $limit) {
        $cutted_array = explode("#!#!", utf8Wordwrap($string, $limit, "#!#!"));

        return "<p>".$cutted_array[0]."..."."</p>"
            ."<a class=\"post-text__more-link\" href=\"/post.php?post=".$post_id
            ."\">"."Читать далее</a>";
    }

    return "<p>".$string."</p>";
}

/**
 * Обрезает сообщение в диалоге до указанного лимита
 * @param string $string Сообщение
 * @param int $limit Максимальная длина сообщения
 *
 * @return string Сообщение
 */

function clipMessageText(string $string, int $limit = 10): string
{
    if (mb_strlen($string) > $limit) {
        $cutted_array = explode("#!#!", utf8Wordwrap($string, $limit, "#!#!"));

        return $cutted_array[0]."...";
    }

    return $string;
}

/**
 * Возвращает кол-во непрочитанных сообщений от пользователя
 * @param $con Соединение с БД
 *
 * @return int Количество сообщений
 */
function getUnreadMessages($con)
{
    $sql_unread
        = ' SELECT COUNT(new) as unread FROM messages WHERE reciever_id = ?';
    $result = formSqlRequest($con, $sql_unread, [$_SESSION['user']['id']]);
    $unread = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $unread[0]['unread'];
}

/**
 * Возвращает интервал между двумя датами в формате «ключ-значение»
 * @param string $date_1 Первая дата
 * @param string $date_2 Вторая дата
 * @param $differenceFormat Формат разницы значений
 *
 * @return array Массив с интервалом двух дат
 */
function dateDifference(
    $date_1,
    $date_2,
    $differenceFormat = '%y %m %d %h %i %s'
): array {
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    $interval = date_diff($datetime1, $datetime2);
    $date_intervals = [
        'years',
        'months',
        'days',
        'hours',
        'minutes',
        'seconds',
    ];
    $date_diff_values = explode(" ", $interval->format($differenceFormat));

    return array_combine($date_intervals, $date_diff_values);
}

/**
 * Устанавливает дату, прошедшую с момента отправки сообщения
 * @param string $date Дата публикации
 * @param array $month_list Список месяцев
 *
 * @return string Дата
 */
function setMessageDate(string $date, array $month_list)
{
    $current_date = date('Y-m-d H:i:s');
    $date_array = dateDifference($current_date, $date);
    $delta_array = array_filter($date_array);
    $delta_value = array_key_first($delta_array);

    if ($delta_value === 'years') {
        return date_format(date_create($date), 'Y').' г';
    }

    if ($delta_value === 'months') {
        $date_with_month = date_format(date_create($date), 'd%m');
        $month = explode('%', $date_with_month);

        return date_format(date_create($date), 'd ')
            .$month_list[$month[1]];
    }

    return date_format(date_create($date), 'H:i');
}


/**
 * Устанавливает дату момента публикации «для машин» и «для людей»
 * @param string $date Дата публикации
 * @param bool $short Укороченный формат даты
 *
 * @return array Массив с двумя датами
 */
function setDate(string $date, bool $short = false): array
{
    $current_date = date('Y-m-d H:i:s');
    $time_title = date_format(date_create($date), 'd-m-Y H:i');
    $date_array = dateDifference($current_date, $date);
    $delta_array = array_filter($date_array);
    $delta_value = array_key_first($delta_array);
    $delta_key = $delta_array[$delta_value];

    if ($delta_value === 'seconds') {
        $date_ago = !$short ? ($delta_key.get_noun_plural_form(
                $delta_key,
                ' секунда ',
                ' секунды ',
                ' секунд '
            )) : $delta_key.' с ';
    } elseif ($delta_value === 'minutes') {
        $date_ago = !$short ? ($delta_key.get_noun_plural_form(
                $delta_key,
                ' минута ',
                ' минуты ',
                ' минут '
            )) : $delta_key.' мин ';
    } elseif ($delta_value === 'hours') {
        $date_ago = !$short ? ($delta_key.get_noun_plural_form(
                $delta_key,
                ' час ',
                ' часа ',
                ' часов '
            )) : $delta_key.' ч ';
    } elseif ($delta_value === 'days' && $delta_key < 7) {
        $date_ago = !$short ? ($delta_key.get_noun_plural_form(
                $delta_key,
                ' день ',
                ' дня ',
                ' дней '
            )) : $delta_key.' д ';
    } elseif ($delta_value === 'days' && $delta_key >= 7) {
        $date_ago = !$short ? (round(($delta_key / 7))
            .get_noun_plural_form(
                round($delta_key / 7),
                ' неделя ',
                ' недели ',
                ' недель '
            )) : round(($delta_key / 7)).' нед ';
    } elseif ($delta_value === 'months') {
        $date_ago = !$short ? ($delta_key
            .get_noun_plural_form(
                $delta_key,
                ' месяц ',
                ' месяца ',
                ' месяцев '
            )) : $delta_key.' мес ';
    } else {
        $date_ago = !$short ? ($delta_key
            .get_noun_plural_form(
                $delta_key,
                ' год ',
                ' года ',
                ' лет '
            )) : $delta_key.' г ';
    }

    return [
        'time_title' => $time_title,
        'date_ago'   => $date_ago,
    ];
}

/**
 * Формирует и выполняет запрос к БД и возвращает результат при необходимости
 * @param mysqli $link Параметры соединения
 * @param string Запрос
 * @param array $params Параметры запроса
 * @param bool $get_data Если необходимо получить результат
 *
 * @return $result Результат запроса
 */
function formSqlRequest(
    mysqli $link,
    string $request,
    array $params,
    bool $get_data = true
) {
    if (!$params) {
        return mysqli_query($link, $request);
    }

    $stmt = db_get_prepare_stmt($link, $request, $params);
    $result = mysqli_stmt_execute($stmt);

    if ($get_data) {
        $result = mysqli_stmt_get_result($stmt);
    }

    if (!$result) {
        $error = mysqli_error($link);
        print("Ошибка подключения: ".$error);
        die();
    }

    return $result;
}

/**
 * Возвращает имя инпута, содержащего контент поста
 * @param string Инпут
 *
 * @return string Имя инпута
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? "";
}

/**
 * Производит расчёты для пагинации
 * @param string $items_count Общее количество постов
 * @param int $page_items Количество постов на странице
 * @param string $cur_page Текущая страница
 *
 * @return array Массив значений для пагинации
 */
function getPaginationPages(
    string $items_count,
    int $page_items,
    string $cur_page
): array {
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;

    return [
        'pages' => range(1, $pages_count),
        'pages_count' => $pages_count,
        'offset' => $offset,
    ];
}

/**
 * Переводит строку из snake_case в camelCase
 * @param string $input Строка
 *
 * @return string Строка в camelCase
 */
function snakeToCamel(string $input)
{
    return ucfirst(
        str_replace(' ', '', ucwords(str_replace('_', ' ', $input)))
    );
}

/**
 * Возвращает имя функции валидации
 * @param string Название валидации
 *
 * @return string Имя функции
 */
function getValidationFunctionName(string $name): string
{
    $name = snakeToCamel($name);

    return "validate$name";
}

function validateForm(
    array $inputArray,
    array $validationRules,
    $dbConnection
): array {
    $errors = [];

    foreach ($validationRules as $input => $rules) {
        $rules = explode("|", $rules);


        foreach ($rules as $rule) {
            $ruleParameters = explode(":", $rule);
            $ruleName = $ruleParameters[0];
            $ruleName = getValidationFunctionName($ruleName);
            $parameters = [];

            if (count($ruleParameters) === 2) {
                $parameters = explode(",", $ruleParameters[1]);
            }

            if (function_exists($ruleName)) {
                if (!empty($errors[$input])) {
                    continue;
                }
                $errors[$input] = call_user_func_array(
                    $ruleName,
                    array_merge(
                        [$inputArray, $input, $dbConnection],
                        $parameters
                    )
                );
            }
        }
    }

    return array_filter($errors);
}

function getHashtags($inputArray, $dbConnection, $newPostId)
{
    if (!empty($inputArray['tags'])) {
        $tags = array_filter(explode(" ", $inputArray['tags']));
        $tags_array = [];

        foreach ($tags as $tag) {
            $request = 'SELECT id FROM hashtags WHERE name = ?';
            $res = formSqlRequest($dbConnection, $request, [$tag]);
            $result = mysqli_fetch_array($res)['id'];
            if (!$result) {
                $request = 'INSERT INTO hashtags SET name = ?';
                formSqlRequest($dbConnection, $request, [$tag], false);
                $new_tag_id = mysqli_insert_id($dbConnection);
                array_push($tags_array, $new_tag_id);
            }
            array_push($tags_array, $result);
        }

        foreach ($tags_array as $tag) {
            $request = 'INSERT INTO post_tags SET post_id = ?, hashtag_id = ?';
            formSqlRequest(
                $dbConnection,
                $request,
                [$newPostId, $tag],
                false
            );
        }
    }

    return null;
}

function getUploadedFile($inputArray, $field)
{
    if (file_exists($inputArray[$field]['tmp_name'])
        || is_uploaded_file(
            $inputArray[$field]['tmp_name']
        )
    ) {
        $tmp_name = $inputArray[$field]['tmp_name'];
        $path_info = pathinfo($inputArray[$field]['name']);
        $file_ext = $path_info['extension'];
        $file_name = uniqid('', true).'.'.$file_ext;
        $file_path = 'uploads/';
        move_uploaded_file($tmp_name, $file_path.$file_name);

        return $file_path.$file_name;
    }

    return null;
}

function getUrlContent($inputArray)
{
    $path = 'uploads/';
    $file = file_get_contents($inputArray['img_url']);
    $file_type = getRemoteMimeType($inputArray['img_url']);
    $file_type_array = explode('/', $file_type);
    $file_ext = $file_type_array[1];
    $file_name = uniqid('', true).'.'.$file_ext;
    file_put_contents($path.$file_name, $file);

    return $path.$file_name;
}

function getTypeId($types, $filter_type)
{
    foreach ($types as $type) {
        if ($type['type'] === $filter_type) {
            return intval($type['id']);
        }

        return 2;
    }
}

function getCurrentUrl()
{
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);

    return $url = $url[0];
}

// Функции валидации

function validateRequired(array $inputArray, string $field): ?string
{
    if (isset($inputArray[$field]) && empty($inputArray[$field])) {
        return 'Это поле должно быть заполнено';
    }

    return null;
}

function validateExists(
    array $inputArray,
    string $field,
    $dbConnection,
    $dbtable,
    $dbfield
): ?string {
    if (!isset($inputArray[$field])) {
        return null;
    }

    $request = 'SELECT '.$dbfield.' FROM '.$dbtable.' WHERE '.$dbfield.' = ?';
    $result = formSqlRequest($dbConnection, $request, [$inputArray[$field]]);

    return mysqli_num_rows($result) > 0 ? null
        : 'Выбранного значения нет в базе';
}

function validateUnique(
    array $inputArray,
    string $field,
    $dbConnection,
    $dbtable,
    $dbfield
): ?string {
    if (!isset($inputArray[$field])) {
        return null;
    }

    $request = 'SELECT '.$dbfield.' FROM '.$dbtable.' WHERE '.$dbfield.' = ?';
    $result = formSqlRequest($dbConnection, $request, [$inputArray[$field]]);

    return mysqli_num_rows($result) > 0
        ? 'Выбранное значение уже существует в базе' : null;
}

function validateString(array $inputArray, string $field): ?string
{
    if (!isset($inputArray[$field])) {
        return null;
    }

    return is_string($inputArray[$field]) ? null
        : 'Значение должно быть строкой';
}

function validateNumber(array $inputArray, string $field): ?string
{
    if (isset($inputArray[$field])) {
        return filter_input(INPUT_POST, $field, FILTER_VALIDATE_INT) ? null
            : 'Значение должно быть числом';
    }

    return null;
}

function validateDate(array $inputArray, string $field): ?string
{
    if (isset($inputArray[$field])) {
        return strtotime($field) ? null : 'Неверный формат даты';
    }

    return null;
}

function validateEmail(array $inputArray, string $field): ?string
{
    if (isset($inputArray[$field])) {
        return filter_var($inputArray[$field], FILTER_VALIDATE_EMAIL) ? null
            : 'Введите корректный email';
    }

    return null;
}

function validateUrl(array $inputArray, string $field): ?string
{
    if (!empty($inputArray[$field])) {
        return filter_var($inputArray[$field], FILTER_VALIDATE_URL) ? null
            : 'Введите корректную ссылку';
    }

    return null;
}

function validateIn(
    array $inputArray,
    string $field,
    $dbConnection,
    ...$values
): ?string {
    if (!isset($inputArray[$field])) {
        return null;
    }

    return in_array($inputArray[$field], $values) ? null
        : "Значение поля $field должно быть одним из ".implode(', ', $values);
}

function validateTags(array $inputArray, string $field): ?string
{
    if (!empty($inputArray[$field])) {
        $tags = explode(" ", $inputArray[$field]);

        return count($tags) > 1 ? null
            : "Введите одно или больше слов, разделенных пробелом";
    }

    return null;
}

function validateVideoUrl(array $inputArray, string $field): ?string
{
    if (!empty($inputArray[$field])) {
        return check_youtube_url($inputArray[$field]) ? null
            : "Видео по такой ссылке не найдено. Проверьте ссылку на видео";
    }

    return null;
}

function validateUploadedFile(array $inputArray, string $field): ?string
{
    if (isset($inputArray[$field])
        && (file_exists(
                $inputArray[$field]['tmp_name']
            )
            || is_uploaded_file($inputArray[$field]['tmp_name']))
    ) {
        $tmp_name = $inputArray[$field]['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type === "image/gif" || $file_type === "image/png"
            || $file_type === "image/jpeg"
        ) {
            return null;
        }

        return "Загрузите файл в формате gif, png или jpeg";
    }

    if (!isset($inputArray[$field])
        || !file_exists(
            $inputArray[$field]['tmp_name']
        )
    ) {
        return null;
    }

    return null;
}

function validateMin(
    array $inputArray,
    string $field,
    $dbConnection,
    $min
): ?string {
    if (isset($inputArray[$field])) {
        $type = gettype($inputArray[$field]);

        if ($type === 'string') {
            return iconv_strlen($inputArray[$field]) < $min ? "Введите минимум "
                .$min." символов" : null;
        }

        if ($type === 'integer') {
            return $inputArray[$field] < $min ? 'Минимальное значение: '.$min
                : null;
        }

        if ($type === 'array') {
            return count($inputArray[$field]) < $min
                ? 'Минимальное количество элементов: '.$min : null;
        }
    }

    return null;
}

function validateMax(
    array $inputArray,
    string $field,
    $dbConnection,
    $max
): ?string {
    if (isset($inputArray[$field])) {
        $type = gettype($inputArray[$field]);

        if ($type === 'string') {
            return iconv_strlen($inputArray[$field]) > $max
                ? "Максимальная длина ".$max." символов" : null;
        }

        if ($type === 'integer') {
            return $inputArray[$field] > $max ? 'Максимальное значение: '.$max
                : null;
        }

        if ($type === 'array') {
            return count($inputArray[$field]) > $max
                ? 'Максимальное количество элементов: '.$max : null;
        }
    }

    return null;
}

function validateRequiredIfValue(
    array $inputArray,
    string $field,
    $dbConnection,
    $value,
    ...$values
): ?string {
    if (isset($inputArray[$field])) {
        if (empty($inputArray[$value])) {
            return "Поле $value должно быть заполнено";
        }

        if (!in_array($inputArray[$value], $values)) {
            return null;
        }

        if (empty($inputArray[$field])) {
            return "Это поле должно быть заполнено";
        }
    }

    return null;
}

function validateUrlContent(array $inputArray, string $field): ?string
{
    if (empty($inputArray[$field])) {
        return null;
    }

    if (in_array(
        getRemoteMimeType($inputArray[$field]),
        ['image/jpeg', 'image/png', 'image/gif']
    )
    ) {
        return null;
    }

    return 'Ссылка должна быть корректной, файл должен быть в формате png, jpeg, gif';
}

function validateRequiredUnless(
    array $inputArray,
    string $field,
    $dbConnection,
    $firstFieldName,
    $secondFieldName
): ?string {
    if (isset($inputArray[$secondFieldName]['tmp_name'])
        && !file_exists(
            $inputArray[$secondFieldName]['tmp_name']
        )
        && empty($inputArray[$firstFieldName])
    ) {
        $secondFieldName = "«Файл»";
        $firstFieldName = "«Ссылка из интернета»";

        return "Это поле должно быть заполнено, если не заполнено поле $firstFieldName";
    }

    return null;
}


function getRemoteMimeType($url)
{
    $url = filter_var($url, FILTER_VALIDATE_URL);
    if (!$url) {
        return null;
    }


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);

    # get the content type
    return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
}

function validatePassword(
    array $inputArray,
    string $field,
    $dbConnection,
    $password,
    $repeat
): ?string {
    if (!empty($inputArray[$password]) && !empty($inputArray[$repeat])) {
        if ($inputArray[$password] !== $inputArray[$repeat]) {
            return "Пароли должны совпадать";
        }

        return null;
    }

    return null;
}

function validateVerify(
    array $inputArray,
    string $field,
    $dbConnection,
    $login,
    $dbtable,
    $dbfieldLogin,
    $dbfieldPassword
): ?string {
    if (isset($inputArray[$field])) {
        if (!empty($inputArray[$login])) {
            $request = 'SELECT '.$dbfieldPassword.' FROM '.$dbtable.' WHERE '
                .$dbfieldLogin.' = ?';
            $result = formSqlRequest(
                $dbConnection,
                $request,
                [$inputArray[$login]]
            );

            $password = mysqli_fetch_array($result, MYSQLI_ASSOC)['password'];
            if (password_verify($inputArray[$field], $password)) {
                return null;
            }

            return "Пароль не совпадает";
        }

        return null;
    }

    return null;
}
