            <main class="content__main">
                <h2 class="content__main-heading">Добавление задачи</h2>

                <form class="form"  action="add.php" method="post" enctype="multipart/form-data">
                    <div class="form__row">
                        <label class="form__label" for="name">Название <sup>*</sup></label>

                        <input class="form__input <?php if($errors['newTaskName']) : ?>form__input--error<?php endif; ?>" type="text" name="name" id="name" value="<?= $newTaskName; ?>" placeholder="Введите название">
                        <?php if($errors['newTaskName']) : ?>
                        <p class="form__message"><?= $errors['newTaskName']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="project">Проект</label>

                        <select class="form__input form__input--select" name="project" id="project">
                            <?php foreach ($projects as $project) : ?>
                            <option value="<?= $project['id']; ?>"
                            <?php if($project['id'] === $newTaskProjectID) : ?>
                                selected
                            <?php endif; ?>
                            ><?= $project['name']; ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="form__row">
                        <label class="form__label" for="date">Дата выполнения</label>

                        <input class="form__input form__input--date <?php if($errors['newTaskDate']) : ?>form__input--error<?php endif; ?>" type="text" name="date" id="date" value="<?= $newTaskDate; ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
                        <?php if($errors['newTaskDate']) : ?>
                            <p class="form__message"><?= $errors['newTaskDate']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="preview">Файл</label>

                        <div class="form__input-file">
                            <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                            <label class="button button--transparent" for="preview">
                                <span>Выберите файл</span>
                            </label>
                        </div>
                    </div>

                    <div class="form__row form__row--controls">
                        <input class="button" type="submit" name="" value="Добавить">
                    </div>
                </form>
            </main>

