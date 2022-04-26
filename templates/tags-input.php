<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="photo-tags">Теги</label>
    <div class="form__input-section <?= $errors['tags'] ? "form__input-section--error" : ""; ?>">
        <input class="adding-post__input form__input" id="photo-tags" type="text" name="tags" value="<?= getPostVal('tags'); ?>" placeholder="Введите теги">
        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
        <h3 class="form__error-title"><?= $input_names['tags'] ?? '' ?></h3>
        <p class="form__error-desc"><?= $errors['tags'] ?></p>
        </div>
    </div>
</div>
