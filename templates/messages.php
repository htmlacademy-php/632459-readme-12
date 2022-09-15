<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs" style="<?= empty($dialogs_users) ? 'display: block;' : '' ?>">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts" style="<?= empty($dialogs_users) ? 'width: auto; margin-left: 0' : '' ?>">
            <ul class="messages__contacts-list tabs__list">
                <?php if(empty($dialogs_users)): ?>
                <p style="width: auto; font-size: 24px; margin: 100px 0 0 25%;">У вас пока нет активных диалогов</p>
                <?php endif; ?>
                <?php if(!empty($dialogs_users)): ?>
                <?php foreach ($dialogs_users as $user): ?>
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab tabs__item tabs__item--active <?= $user['id'] === $first_user ? 'messages__contacts-tab--active' : '' ?> " href="/messages.php?user=<?= $user['id'] ?? '' ?>">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" style="width: 100%" src="<?= $user['avatar_path'] ?? '' ?>" alt="Аватар пользователя">
                            <?php if ($user['unread']): ?>
                            <i class="messages__indicator"><?= $user['unread'] ?? '' ?></i>
                            <?php endif; ?>
                        </div>
                        <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= $user['login'] ?? '' ?>
                  </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    <?php if($user['sender'] === $_SESSION['user']['id']): ?>
                                    <?= 'Вы: ' . htmlspecialchars(clipMessageText($user['last_text'])) ?? '' ?>
                                    <?php endif; ?>

                                    <?php if($user['sender'] !== $_SESSION['user']['id']): ?>
                                        <?= htmlspecialchars(clipMessageText($user['last_text'])) ?? '' ?>
                                    <?php endif; ?>
                                </p>
                                <time class="messages__preview-time" datetime="<?= $user['last_date'] ?? '' ?>">
                                    <?= setMessageDate($user['last_date'], $month_list) ?? '' ?>
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
        <div class="messages__chat">
            <div class="messages__chat-wrapper">
                <ul class="messages__list tabs__content tabs__content--active">
                    <?php foreach ($chat_messages as $message): ?>
                    <li class="messages__item <?= $message['sender_id'] === $_SESSION['user']['id']
                        ? 'messages__item--my' : '' ?>">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="<?= $message['avatar_path'] ?? 'img/userpic-tanya.jpg' ?>" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    <?= htmlspecialchars($message['login'])  ?? '' ?>
                                </a>
                                <time class="messages__time" datetime="<?= setDate($message['date_add'])['datetime'] ?? '' ?>">
                                    <?= setDate($message['date_add'])['date_ago'] ?? '' ?>назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                           <?= htmlspecialchars($message['text'] )?? '' ?>
                        </p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="comments">
                <form class="comments__form form" action="#" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="<?= $_SESSION['user']['avatar_path'] ?? 'img/userpic-tanya.jpg' ?>" alt="Аватар пользователя">
                    </div>
                    <div class="form__input-section <?= $errors ? 'form__input-section--error' : '' ?>">
                <textarea class="comments__textarea form__textarea form__input"
                          placeholder="Ваше сообщение" name="message"></textarea>
                        <label class="visually-hidden">Ваше сообщение</label>
                        <button class="form__error-button button" type="button">!</button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Ошибка валидации</h3>
                            <p class="form__error-desc"><?= $errors['message'] ?? '' ?></p>
                        </div>
                    </div>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>
