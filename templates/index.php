<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed"
               type="checkbox" <?= $show_complete_tasks !== 1 ?: 'checked'; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $task): ?>
        <?php if ($task['isDone'] && !$show_complete_tasks) : ?>
            <?php continue; ?>
        <?php endif; ?>
        <tr class="tasks__item task
            <?php if ($task['isDone']) : ?>
                <?= 'task--completed'; ?>
            <?php elseif (checkTaskImportant($task['deadline'])) : ?>
                <?= 'task--important'; ?>
            <?php endif; ?>
        ">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                        <?php if ($task['isDone'] && $show_complete_tasks === 1) : ?>
                            <?= 'checked'; ?>
                        <?php endif; ?>>
                    <span class="checkbox__text"><?= $task['title']; ?></span>
                </label>
            </td>

            <!--                        <td class="task__file">-->
            <!--                            <a class="download-link" href="#"></a>-->
            <!--                        </td>-->

            <td class="task__date"><?= $task['deadline']; ?></td>
            <td class="task__controls">
            </td>
        </tr>
    <?php endforeach; ?>
</table>
