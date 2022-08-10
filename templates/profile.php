<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
    <div class="profile__user-wrapper">
        <div class="profile__user user container">
        <div class="profile__user-info user__info">
            <div class="profile__avatar user__avatar">
            <img class="profile__picture user__picture" src="<?= $user['avatar_path'] ?? 'img/userpic-tanya.jpg' ?>" alt="Аватар пользователя">
            </div>
            <div class="profile__name-wrapper user__name-wrapper">
            <span class="profile__name user__name"><?= $user['login'] ?? '' ?></span>
            <time class="profile__user-time user__time" datetime="<?= $user['dt_reg'] ?? '' ?>"><?= set_date($user['dt_reg'])['date_ago'] ?? '' ?>на сайте</time>
            </div>
        </div>
        <div class="profile__rating user__rating">
            <p class="profile__rating-item user__rating-item user__rating-item--publications">
            <span class="user__rating-amount"><?= $publications['total'] ?? '' ?></span>
            <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form($publications['total'], 'публикация', 'публикации', 'публикаций') ?? '' ?></span>
            </p>
            <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
            <span class="user__rating-amount"><?= $subscribers['total'] ?? '' ?></span>
            <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form($subscribers['total'], 'подписчик', 'подписчика', 'подписчиков') ?? '' ?></span>
            </p>
        </div>
        <form action="/subscribe.php?user=<?= $user['id'] ?>" method="get">
        <div class="profile__user-buttons user__buttons">
            <button class="profile__user-button user__button user__button--subscription button button--main" type="submit"><?= $is_subscribe ? 'Отписаться' : 'Подписаться' ?></button>
            <input class="visually-hidden" type="text" name="user" value="<?= $user['id'] ?? '' ?>">
            <a class="profile__user-button user__button user__button--writing button button--green" href="#">Сообщение</a>
        </div>
        </form>
        </div>
    </div>
    <div class="profile__tabs-wrapper tabs">
        <div class="container">
        <div class="profile__tabs filters">
            <b class="profile__tabs-caption filters__caption">Показать:</b>
            <ul class="profile__tabs-list filters__list tabs__list">
            <li class="profile__tabs-item filters__item">
                <a href="profile.php?user=<?= $_GET['user'] ?>&tab=posts" class="profile__tabs-link filters__button <?= $_GET['tab'] === 'posts' ? 'filters__button--active tabs__item--active' : '' ?> tabs__item button">Посты</a>
            </li>
            <li class="profile__tabs-item filters__item">
                <a class="profile__tabs-link filters__button <?= $_GET['tab'] === 'likes' ? 'filters__button--active tabs__item--active' : '' ?> tabs__item button" href="profile.php?user=<?= $_GET['user'] ?>&tab=likes">Лайки</a>
            </li>
            <li class="profile__tabs-item filters__item">
                <a class="profile__tabs-link filters__button <?= $_GET['tab'] === 'subs' ? 'filters__button--active tabs__item--active' : '' ?> tabs__item button" href="profile.php?user=<?= $_GET['user'] ?>&tab=subs">Подписки</a>
            </li>
            </ul>
        </div>
        <div class="profile__tab-content">
            <section class="profile__posts tabs__content <?= $_GET['tab'] === 'posts' ? 'tabs__content--active' : '' ?>">
            <h2 class="visually-hidden">Публикации</h2>
            <?php foreach($posts as $index => $post): ?>
            <article class="profile__post post <?= $post['class'] ?? '' ?>">
                <header class="post__header">
                    <?php if ($post['repost']): ?>
                        <div class="post__author">
                        <a class="post__author-link" href="/profile.php?user=<?= $post['original_author'] . '&tab=posts' ?? '' ?>" title="Автор">
                        <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                        <img class="post__author-avatar" src="<?= $post['avatar_path'] ?? 'img/userpic-tanya.jpg' ?>" alt="Аватар пользователя">
                        </div>
                        <div class="post__info">
                        <b class="post__author-name">Репост: <?= $post['login'] ?? '' ?></b>
                        <time class="post__time" datetime="<?= $post['date_add'] ?? '' ?>"><?= set_date($post['date_add'])['date_ago'] . ' назад' ?? ''  ?></time>
                        </div>
                      </a>
                    </div>
                    <?php endif; ?>
                <h2><a href="/post.php?post=<?= $post['id'] ?? '' ?>"><?= $post['title'] ?? '' ?></a></h2>
                </header>
                <div class="post__main">
                <?php switch($post['type']):
                    case 'quote': ?>
                    <blockquote>
                        <p>
                            <?= htmlspecialchars($post['text'] ?? '') ?>
                        </p>
                        <cite><?= htmlspecialchars($post['cite_author'] ?? '') ?></cite>
                    </blockquote>
                    <?php break; ?>

                    <?php case 'text': ?>
                        <p><?= htmlspecialchars($post['text'] ?? '') ?></p>
                    <?php break; ?>

                    <?php case 'photo': ?>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= htmlspecialchars($post['img'] ?? '') ?>" alt="Фото от пользователя" width="760" height="396">
                        </div>
                    <?php break; ?>

                    <?php case 'video': ?>
                        <div class="post-video__block">
                        <div class="post-video__preview">
                            <img src="img/coast.jpg" alt="Превью к видео" width="760" height="396">
                        </div>
                        <div class="post-video__control">
                            <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
                            <div class="post-video__scale-wrapper">
                            <div class="post-video__scale">
                                <div class="post-video__bar">
                                <div class="post-video__toggle"></div>
                                </div>
                            </div>
                            </div>
                            <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
                        </div>
                        <button class="post-video__play-big button" type="button">
                            <svg class="post-video__play-big-icon" width="27" height="28">
                            <use xlink:href="#icon-video-play-big"></use>
                            </svg>
                            <span class="visually-hidden">Запустить проигрыватель</span>
                        </button>
                        </div>
                    <?php break; ?>

                    <?php case 'link': ?>
                        <div class="post-link__wrapper">
                        <a class="post-link__external" href="<?= htmlspecialchars($post['link'] ?? '') ?>" title="Перейти по ссылке">
                            <div class="post-link__icon-wrapper">
                                <img src="img/cat.jpg" alt="Иконка">
                            </div>
                            <div class="post-link__info">
                                <h3><?= htmlspecialchars($post['title'] ?? '') ?></h3>
                                <span><?= htmlspecialchars($post['link'] ?? '') ?></span>
                            </div>
                            <svg class="post-link__arrow" width="11" height="16">
                                <use xlink:href="#icon-arrow-right-ad"></use>
                            </svg>
                        </a>
                        </div>
                    <?php break; ?>

                    <?php endswitch; ?>
                </div>
                <footer class="post__footer">
                <div class="post__indicators">
                    <div class="post__buttons">
                    <a class="post__indicator post__indicator--likes button" href="/like.php?post=<?= $post['id'] ?? '' ?>" title="Лайк">
                        <svg class="post__indicator-icon" width="20" height="17">
                        <use xlink:href="#icon-heart"></use>
                        </svg>
                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                        <use xlink:href="#icon-heart-active"></use>
                        </svg>
                        <span><?= $post['likes_count'] ?? '' ?></span>
                        <span class="visually-hidden">количество лайков</span>
                    </a>
                    <a class="post__indicator post__indicator--repost button" href="/repost.php?post=<?= $post['id'] ?? '' ?>" title="Репост">
                        <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-repost"></use>
                        </svg>
                        <span><?= $reposts[$index]['repost_count'] ?? '0' ?></span>
                        <span class="visually-hidden">количество репостов</span>
                    </a>
                    </div>
                    <time class="post__time" title="<?= set_date($post['date_add'])['time_title'] ?? '' ?>" datetime="<?= $post['date_add'] ?>"><?= set_date($post['date_add'])['date_ago'] ?? '' ?> назад</time>
                </div>
                <ul class="post__tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                    <li><a href="/search.php?search=%23<?= htmlspecialchars($tag ?? '') ?>">#<?= htmlspecialchars($tag ?? '') ?></a></li>
                    <?php endforeach; ?>
                </ul>
                </footer>
                <div class="comments">
                <a class="comments__button button" href="/post.php?post=<?= $post['id'] ?>">Показать комментарии</a>
                </div>
            </article>
            <?php endforeach; ?>
            </section>

            <section class="profile__likes tabs__content <?= $_GET['tab'] === 'likes' ? 'tabs__content--active' : '' ?>">
            <h2 class="visually-hidden">Лайки</h2>
            <ul class="profile__likes-list">
                <?php foreach ($profile_likes as $like): ?>
                <li class="post-mini post-mini--<?= $like['type'] ?? '' ?> post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                    <a class="user__avatar-link" href="/profile.php?user=<?= $like['id'] . '&tab=posts' ?>">
                        <img class="post-mini__picture user__picture" src="<?= $like['avatar_path'] ?? 'img/userpic-tanya.jpg' ?>" alt="Аватар пользователя">
                    </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                    <a class="post-mini__name user__name" href="/profile.php?user=<?= $like['id'] ?> . '&tab=posts'">
                        <span><?= htmlspecialchars($like['login'] ?? '') ?></span>
                    </a>
                    <div class="post-mini__action">
                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                        <time class="post-mini__time user__additional" datetime="<?= $like['like_date'] ?? '' ?>"><?= set_date($like['like_date'])['date_ago'] ?? '' ?> назад</time>
                    </div>
                    </div>
                </div>
                <div class="post-mini__preview">
                    <a class="post-mini__link" href="/post.php?post=<?= $like['like_post_id'] ?>" title="Перейти на публикацию">

                    <?php switch($post['type']):
                        case 'photo': ?>
                        <div class="post-mini__image-wrapper">
                          <img class="post-mini__image" src="<?= $like['img'] ?? '' ?>" width="109" height="109" alt="Превью публикации">
                        </div>
                        <?php break ?>

                        <?php case 'quote': ?>
                        <svg class="post-mini__preview-icon" width="21" height="20">
                          <use xlink:href="#icon-filter-quote"></use>
                        </svg>
                        <?php break ?>

                        <?php case 'text': ?>
                        <svg class="post-mini__preview-icon" width="20" height="21">
                          <use xlink:href="#icon-filter-text"></use>
                        </svg>
                        <?php break ?>

                        <?php case 'link': ?>
                        <svg class="post-mini__preview-icon" width="21" height="18">
                          <use xlink:href="#icon-filter-link"></use>
                        </svg>
                        <?php break ?>

                        <?php case 'video': ?>
                            <div class="post-mini__image-wrapper">
                          <img class="post-mini__image" src="img/coast-small.png" width="109" height="109" alt="Превью публикации">
                          <span class="post-mini__play-big">
                            <svg class="post-mini__play-big-icon" width="12" height="13">
                              <use xlink:href="#icon-video-play-big"></use>
                            </svg>
                          </span>
                        </div>
                        <?php break ?>

                        <?php endswitch; ?>
                    <span class="visually-hidden"><?= $like['name'] ?? '' ?></span>
                    </a>
                </div>
                </li>
                <?php endforeach; ?>
            </ul>
            </section>

            <section class="profile__subscriptions tabs__content <?= $_GET['tab'] === 'subs' ? 'tabs__content--active' : '' ?>">
            <h2 class="visually-hidden">Подписки</h2>
            <ul class="profile__subscriptions-list">
                <?php foreach($profile_subs as $subscribe): ?>
                <li class="post-mini post-mini--photo post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                    <a class="user__avatar-link" href="/profile.php?user=<?= $subscribe['subscribe_id'] . '&tab=posts' ?? '' ?>">
                        <img class="post-mini__picture user__picture" src="<?= htmlspecialchars($subscribe['avatar_path'] ?? 'img/userpic-tanya.jpg') ?>" alt="Аватар пользователя">
                    </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                    <a class="post-mini__name user__name" href="/profile.php?user=<?= $subscribe['subscribe_id'] . '&tab=posts' ?? '' ?>">
                        <span><?= htmlspecialchars($subscribe['login'] ?? '') ?></span>
                    </a>
                    <time class="post-mini__time user__additional" datetime="<?= $subscribe['dt_reg'] ?? '' ?>"><?= set_date($subscribe['dt_reg'])['date_ago'] ?? '' ?> на сайте</time>
                    </div>
                </div>
                <div class="post-mini__rating user__rating">
                    <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subscribe['posts_count'] ?? '' ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($subscribe['posts_count'], ' публикация', ' публикации', ' публикаций') ?></span>
                    </p>
                    <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subscribe['subscription_count'] ?? '' ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($subscribe['subscription_count'], ' подписчик', ' подписчика', ' подписчиков') ?></span>
                    </p>
                </div>
                <div class="post-mini__user-buttons user__buttons">
                <form action="/subscribe.php?user=<?= $subscribe['id'] ?? '' ?>" method="get">
                <input class="visually-hidden" type="text" name="user" value="<?= $subscribe['subscribe_id'] ?? '' ?>">
                    <button class="post-mini__user-button user__button user__button--subscription button button--main" type="submit"><?= $subscribe['is_sub'] ? 'Отписаться' : 'Подписаться' ?></button>
                </form>
                </div>
                </li>
                <?php endforeach; ?>
            </ul>
            </section>
        </div>
        </div>
    </div>
    </div>
</main>
