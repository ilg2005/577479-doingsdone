<?php
const SECONDS_PER_DAY = 86400;
$pageTitle = 'Дела в порядке';

/**
 * Подключает файлы шаблонов
 *
 * @param $name -- имя файла шаблона, включающее путь к файлу
 * @param array $data -- массив используемых в шаблоне переменных
 *
 * @return false|string -- строка содержимого буфера вывода или false
 */
function includeTemplate($name, $data)
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data, EXTR_OVERWRITE);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Проверяет, наступает ли срок сдачи задач через сутки или менее
 *
 * @param string $deadline -- строка со сроком сдачи задачи
 *
 * @return bool -- true, если срок сдачи задачи не более суток
 */
function checkTaskImportant($deadline)
{
    if (strtotime($deadline)) {
        $timeRemaining = strtotime($deadline) - time();
        return ($timeRemaining <= SECONDS_PER_DAY);
    }
}

/**
 * Выводит строку с описанием последней ошибки MySQL, если не получено данных из таблицы MySQL
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param array $result -- массив с данными результирующей таблицы
 */
function checkDatabaseError($link, $result)
{
    if (!$result) {
        print('Ошибка MySQL ' . mysqli_error($link));
        exit();
    }
}

/**
 * Устанавливает соединение с базой данных MySQL
 *
 * @param string $hostName -- строка с именем хоста
 * @param string $userName -- строка с именем пользователя MySQL
 * @param string $pwd -- пароль пользователя MySQL
 * @param string $dbName -- строка с именем базы данных MySQL
 *
 * @return mysqli $link -- объект, представляющий подключение к серверу MySQL
 */
function connect2Database($hostName, $userName, $pwd, $dbName)
{
    $link = mysqli_connect($hostName, $userName, $pwd, $dbName);
    mysqli_set_charset($link, 'utf8');
    checkDatabaseError($link, $link);
    return $link;
}

/**
 * Получает все строки данных из базы данных MySQL в соответствии с запросом
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $sql --  строка SQL запроса с плейсхолдерами вместо значений
 * @param array $data -- массив данных для вставки на место плейсхолдеров
 *
 * @return array|null -- массив всех строк из базы данных на основе подготовленного запроса или null
 */
function fetchData($link, $sql, $data = [])
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    checkDatabaseError($link, $res);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

/**
 * Получает строку данных из базы данных MySQL в соответствии с запросом
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $sql --  строка SQL запроса с плейсхолдерами вместо значений
 * @param array $data -- массив данных для вставки на место плейсхолдеров
 *
 * @return array|null -- массив, соответствующий выбранной строке на основе подготовленного запроса или null
 */
function fetchRow($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($res);
}

/**
 * Проверяет существует ли пользователь с выбранным ID
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $selectedUserID -- строка с ID пользователя
 *
 * @return array|null -- массив, соответствующий выбранной строке на основе подготовленного запроса, или null
 */
function isUserExist($link, $selectedUserID)
{
    $query = 'SELECT * FROM users WHERE id = ? LIMIT 1';
    return fetchRow($link, $query, [$selectedUserID]);
}

/**
 * Получает все проекты авторизованного пользователя
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $selectedUserID -- строка с ID пользователя
 *
 * @return array|null -- массив всех строк из базы данных на основе подготовленного запроса или null
 */
function getProjects($link, $selectedUserID)
{
    $query = 'SELECT p.id, p.name, count(t.id) as task_count FROM projects p LEFT JOIN tasks t ON t.project_id = p.id WHERE p.user_id = ? GROUP BY p.id';
    return fetchData($link, $query, [$selectedUserID]);
}

/**
 *  Отправляет HTTP-заголовок с кодом ошибки 404
 */
function showNotFound()
{
    header('HTTP/1.1 404 Not Found');
    exit();
}

/**
 * Получает (все или специфичные для проекта) задачи авторизованного пользователя
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $selectedUserID -- строка с ID пользователя
 *
 * @return array|null -- массив задач, содержащий все строки из базы данных на основе подготовленного запроса, или null
 */
function getTasks($link, $selectedUserID)
{
    $projectDataByID = null;
    $projectData = 'SELECT * FROM projects WHERE user_id = ? AND id = ?';
    $allTasks = 'SELECT t.id, t.name, t.file_name, t.project_id, DATE_FORMAT(t.deadline, "%d.%m.%Y") AS deadline,  t.is_done FROM tasks t WHERE t.user_id = ?';
    $projectSpecificTasks = $allTasks . ' AND t.project_id = ?';

    if (isset($_GET['project_id'])) {
        if (!is_numeric($_GET['project_id']) || $_GET['project_id'] === '') {
            showNotFound();
        }
        if (!($projectDataByID = fetchData($link, $projectData, [$selectedUserID, $_GET['project_id']]))) {
            showNotFound();
        }
        $tasks = fetchData($link, $projectSpecificTasks, [$selectedUserID, $_GET['project_id']]);
    } else {
        $tasks = fetchData($link, $allTasks, [$selectedUserID]);
    }

    return $tasks;
}

/**
 * Меняет статус задачи в базе данных (выполнена/не выполнена) при клике на чекбокс перед именем задачи
 */
function changeTaskStatusInDatabase()
{
    $connection = connect2Database('localhost', 'root', '', 'doingsdone');

    if (isset($_GET['task_id'], $_GET['check'])) {
        $taskID = $_GET['task_id'];
        $status = $_GET['check'];

        $taskStatusUpdate = 'UPDATE tasks SET is_done = ? WHERE id = ?';
        $stmt = db_get_prepare_stmt($connection, $taskStatusUpdate, [$status, $taskID]);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: /index.php?show_completed=1');
            exit();
        }
    }
}

/**
 * Проверяет существует ли проект с выбранным ID
 *
 * @param string $projectID -- строка с ID проекта
 *
 * @return bool -- true, если существует проект и непустое ID проекта
 */
function isProjectExist($projectID) {
    return (isset($projectID) && $projectID !== '');
}

/**
 * Проверяет соответствие даты заданному формату
 *
 * @param string $format -- строка с заданным форматом данных
 * @param string $date -- строка с датой
 *
 * @return bool -- true, если дата соответствует формату
 */
function isCorrectDateFormat($format, $date)
{
    return (!$date || date_create_from_format($format, $date));
}

/**
 * Проверяет прошла ли дата
 *
 * @param  string $date -- строка с датой
 *
 * @return bool -- true, если дата прошла и не пустая
 */
function checkPastDate($date)
{
    return (strtotime($date) < mktime(0, 0, 0) && $date !== '');
}

/**
 * Получает данные пользователей, имеющих задачи со сроком сдачи сегодня
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 *
 * @return array|null -- массив пользователей и задач, содержащий все строки из базы данных на основе подготовленного запроса, или null
 */
function getUsersWithUrgentTasks($link) {
    $sql = 'SELECT u.id, u.name AS user_name, u.email, t.name AS task_name, t.deadline FROM users u JOIN tasks t ON t.user_id = u.id WHERE STR_TO_DATE(t.deadline,"%Y%m%d") = CURDATE() AND t.is_done = 0';
    return fetchData($link, $sql, []);
}

/**
 * Получает для выбранного пользователя задачи со сроком сдачи сегодня
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $userID  -- строка с ID пользователя
 *
 * @return array|null -- массив задач, содержащий все строки из базы данных на основе подготовленного запроса, или null
 */
function getUrgentTasks($link, $userID)
{
    $sql = 'SELECT t.name AS task_name, t.deadline, t.user_id, u.name, u.email FROM tasks t JOIN users u ON t.user_id = u.id WHERE STR_TO_DATE(t.deadline,"%Y%m%d") = CURDATE() AND t.is_done = 0 AND t.user_id = ?';
    return fetchData($link, $sql, [$userID]);
}

/**
 * Проверяет существование определенной задачи для определенного пользователя и проекта
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $taskName -- строка с именем задачи
 * @param string $userID  -- строка с ID пользователя
 * @param string $projectID -- строка с ID проекта
 *
 * @return array|null -- массив, содержащий строку с задачей из базы данных на основе подготовленного запроса, или null
 */
function checkTaskExist($link, $taskName, $userID, $projectID)
{
    $sql = 'SELECT id FROM tasks WHERE name = ? AND user_id = ? AND project_id = ? LIMIT 1';
    return fetchRow($link, $sql, [$taskName, $userID, $projectID]);
}

/**
 * Проверяет, существует ли определенный проект у определенного пользователя
 *
 * @param $link -- объект, представляющий подключение к серверу MySQL
 * @param string $projectName - строка с названием проекта
 * @param string $userID  -- строка с ID пользователя
 *
 * @return array|null-- массив, содержащий строку с проектом из базы данных на основе подготовленного запроса, или null
 */
function checkProjectExist($link, $projectName, $userID)
{
    $sql = 'SELECT id FROM projects WHERE name = ? AND user_id = ? LIMIT 1';
    $result = fetchRow($link, $sql, [$projectName, $userID]);
    return $result;
}

/**
 * Проверяет валидность введенного пользователем значения e-mail
 *
 * @param string $email -- строка с введенным пользователем значением e-mail
 *
 * @return mixed -- отфильтрованные данные или false, если фильтрация завершилась неудачей
 */
function isEmailValid($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Применяет фильтр, позволяющий сортировать задачи в соответствии с критериями ("все задачи", "повестка дня", "завтра", "просроченные")
 *
 * @param string $userID  -- строка с ID пользователя
 * @param string $projectID -- строка с ID проекта
 * @param array $filter -- ассоциативный массив, содержащий ключи с именами критериев и значения с соответсвующими SQL запросами
 *
 * @return array|null -- массив задач, содержащий строки из базы данных на основе подготовленного запроса, или null
 */
function applyFilter($userID, $projectID, $filter)
{
    $connection = connect2Database('localhost', 'root', '', 'doingsdone');

    if ($projectID) {
        $allSql = 'SELECT * FROM tasks t WHERE t.user_id = ? AND t.project_id = ?';
    } else {
        $allSql = 'SELECT * FROM tasks t WHERE t.user_id = ?';
    }
    $todaySql = $allSql . ' AND t.deadline = CURDATE()';
    $tomorrowSql = $allSql . ' AND t.deadline = CURDATE() + 1';
    $overdueSql = $allSql . ' AND t.deadline < CURDATE() AND t.deadline <> 0 AND t.is_done = 0';

    $filters = [
        'all' => $allSql,
        'today' => $todaySql,
        'tomorrow' => $tomorrowSql,
        'overdue' => $overdueSql
    ];

    if ($projectID) {
        $filteredTasks = fetchData($connection, $filters[$filter], [$userID, $projectID]);
    } else {
        $filteredTasks = fetchData($connection, $filters[$filter], [$userID]);
    }
    return $filteredTasks;
}

