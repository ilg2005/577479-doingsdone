    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form"  action="add-project.php" method="post">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?php if(isset($errors['newProjectName']) || isset($errors['newProjectNameRepeat'])) : ?>form__input--error<?php endif; ?>" type="text" name="name" id="project_name" value="<?= $newProjectName; ?>" placeholder="Введите название проекта">
            <?php if(isset($errors['newProjectName'])) : ?>
                <p class="form__message"><?= $errors['newProjectName']; ?></p>
            <?php endif; ?>
            <?php if(isset($errors['newProjectNameRepeat'])) : ?>
                <p class="form__message"><?= $errors['newProjectNameRepeat']; ?></p>
            <?php endif; ?>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
