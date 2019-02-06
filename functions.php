<?php
$includeTemplate = function ($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
};

$countTasks4Projects = function ($tasksArray, $projectName) {
$count = 0;
foreach ($tasksArray as $item) {
($item['projectCategory'] === $projectName) ? ($count++) : '';
}
return $count;
};
?>
