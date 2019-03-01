<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/index.php?filter=all" class="tasks-switch__item
        <?php if (isset($_GET['filter']) && $_GET['filter'] === 'all'): ?>
        tasks-switch__item--active
        <?php endif; ?>">Все задачи</a>

        <a href="/index.php?filter=today" class="tasks-switch__item
        <?php if (isset($_GET['filter']) && $_GET['filter'] === 'today'): ?>
        tasks-switch__item--active
        <?php endif; ?>">Повестка дня</a>

        <a href="/index.php?filter=tomorrow" class="tasks-switch__item
        <?php if (isset($_GET['filter']) && $_GET['filter'] === 'tomorrow'): ?>
        tasks-switch__item--active
        <?php endif; ?>">Завтра</a>

        <a href="/index.php?filter=overdue" class="tasks-switch__item
        <?php if (isset($_GET['filter']) && $_GET['filter'] === 'overdue'): ?>
        tasks-switch__item--active
        <?php endif; ?>">Просроченные</a>

        <?php if (isset($_GET['filter'])) : ?>
            <?php $filter = htmlspecialchars($_GET['filter']) ?>
            <?php applyFilter($filter); ?>
        <?php endif; ?>

    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed"
               type="checkbox" <?php if (isset($show_complete_tasks)) : ?>
            <?= !$show_complete_tasks ?: 'checked'; ?>
        <?php else : ?>
            checked
        <?php endif; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $task): ?>
        <?php if ($task['is_done'] && !$show_complete_tasks) : ?>
            <?php continue; ?>
        <?php endif; ?>
        <tr class="tasks__item task
            <?php if ($task['is_done']) : ?>
                task--completed
            <?php elseif (checkTaskImportant($task['deadline'])) : ?>
                task--important
            <?php endif; ?>
        ">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" name="task_id"
                           value="<?= $task['id'] ?>"
                        <?php if (isset($_GET['task_id'])) : ?>
                            <?php changeTaskStatusInDatabase(); ?>
                        <?php endif; ?>
                        <?php if ($task['is_done'] && $show_complete_tasks) : ?>
                            checked
                        <?php endif; ?>>
                    <span class="checkbox__text"><?= $task['name']; ?></span>
                </label>
            </td>
            <?php if ($task['file_name']) : ?>
                <td class="task__file">
                    <a class="download-link" href="<?= '/' . $task['file_name']; ?>"></a>
                </td>
            <?php else : ?>
                <td></td>
            <?php endif; ?>

            <td class="task__date"><?= ($task['deadline'] != 0) ? $task['deadline'] : ''; ?>
            </td>

            <td class="task__controls">
            </td>
        </tr>
    <?php endforeach; ?>
</table>
