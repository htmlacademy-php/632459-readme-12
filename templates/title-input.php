<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="photo-heading">Заголовок
        <span class="form__input-required">*</span></label>
    <div class="form__input-section <?= $errors['title']
        ? "form__input-section--error" : "" ?>">
        <input class="adding-post__input form__input" id="photo-heading"
               type="text" name="title" value="<?= getPostVal('title') ?? '' ?>"
               placeholder="Введите заголовок">
        <button class="form__error-button button" type="button">!<span
                class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title"><?= $input_names['title'] ??
                '' ?></h3>
            <p class="form__error-desc"><?= $errors['title'] ?? '' ?></p>
        </div>
    </div>
</div>
