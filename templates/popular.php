<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link sorting__link--active" href="#">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип
                    контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= !isset($_GET['tab'])
                        || $_GET['tab'] === '' ? 'filters__button--active'
                            : '' ?>" href="/popular.php?page=1&tab">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php
                    foreach ($types as $type): ?>
                        <li class="popular__filters-item filters__item">
                            <a class="filters__button filters__button--<?= $type['type'] ?> button <?= isset($_GET['tab'])
                            && $_GET['tab'] === $type['id']
                                ? 'filters__button--active' : '' ?>"
                               href="/popular.php?page=1&tab=<?= $type['id'] ?>">
                                <span
                                    class="visually-hidden"><?= $type['name'] ?></span>
                                <svg class="filters__icon" width="22"
                                     height="18">
                                    <use
                                        xlink:href="#icon-filter-<?= $type['type'] ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php
                    endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">

            <?php
            foreach ($popular_posts as $index => $post): ?>
                <article class="popular__post post <?= $post['class'] ?? '' ?>">
                    <header class="post__header">
                        <h2><a href="/post.php?post=<?= $post['id'] ??
                            '' ?>"><?= htmlspecialchars(
                                    $post['title'] ?? ''
                                ) ?></a></h2>
                    </header>

                    <div class="post__main">
                        <?php
                        switch ($post['class']):

                            case 'post-photo': ?>
                                <div class="post-photo__image-wrapper">
                                    <img src="<?= $post['img'] ?? '' ?>"
                                         alt="Фото от пользователя" width="360"
                                         height="240">
                                </div>
                                <?php
                                break ?>

                            <?php
                            case 'post-quote': ?>
                                <blockquote>
                                    <p>
                                        <?= htmlspecialchars(
                                            $post['text'] ?? ''
                                        ) ?>
                                    </p>
                                    <cite><?= htmlspecialchars(
                                            $post['cite_author'] ?? ''
                                        ) ?></cite>
                                </blockquote>
                                <?php
                                break ?>

                            <?php
                            case 'post-text': ?>
                                <p><?= clipPostText(
                                        htmlspecialchars($post['text'] ?? ''),
                                        $post['id']
                                    ) ?></p>
                                <?php
                                break ?>

                            <?php
                            case 'post-video': ?>
                                <div class="post-video__block">
                                    <div class="post-video__preview">
                                        <img src="img/coast-medium.jpg"
                                             alt="Превью к видео" width="360"
                                             height="188">
                                    </div>
                                    <a href="post-details.html"
                                       class="post-video__play-big button">
                                        <svg class="post-video__play-big-icon"
                                             width="14" height="14">
                                            <use
                                                xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                        <span class="visually-hidden">Запустить проигрыватель</span>
                                    </a>
                                </div>
                                <?php
                                break ?>

                            <?php
                            case 'post-link': ?>
                                <div class="post-link__wrapper">
                                    <a class="post-link__external"
                                       href="http://<?= htmlspecialchars(
                                           $post['link'] ?? ''
                                       ) ?>" title="Перейти по ссылке">
                                        <div class="post-link__info-wrapper">
                                            <div
                                                class="post-link__icon-wrapper">
                                                <img
                                                    src="https://www.google.com/s2/favicons?domain=vitadental.ru"
                                                    alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= htmlspecialchars(
                                                        $post['title'] ?? ''
                                                    ) ?></h3>
                                            </div>
                                        </div>
                                        <span><?= htmlspecialchars(
                                                $post['text'] ?? ''
                                            ) ?></span>
                                    </a>
                                </div>
                                <?php
                                break ?>

                            <?php
                        endswitch; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link"
                               href="/profile.php?user=<?= $post['user_id']
                               .'&tab=posts' ?? '' ?>" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar"
                                         src="<?= $post['avatar_path'] ??
                                         'img/userpic-tanya.jpg' ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= htmlspecialchars(
                                            $post['login'] ?? ''
                                        ) ?></b>
                                    <time class="post__time" title="<?= setDate(
                                                                            $post['date_add']
                                                                        )['time_title']
                                    ?? '' ?>"
                                          datetime="<?= $post['date_add'] ?>"><?= setDate(
                                                                                      $post['date_add']
                                                                                  )['date_ago']
                                        ?? '' ?>назад
                                    </time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button"
                                   href="/like.php?post=<?= $post['id'] ??
                                   '' ?>" title="Лайк">
                                    <svg class="post__indicator-icon" width="20"
                                         height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg
                                        class="post__indicator-icon post__indicator-icon--like-active"
                                        width="20" height="17">
                                        <use
                                            xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span><?= $post['likes_count'] ??
                                        '' ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button"
                                   href="#" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19"
                                         height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= $post['comments_count'] ??
                                        '' ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php
            endforeach; ?>
        </div>
        <?php
        if ($pages_count > 1): ?>
            <div class="popular__page-links">
                <?php
                if (!empty($_GET['tab'])): ?>
                    <a class="popular__page-link button button--gray popular__page-link--prev"
                       href="<?= $cur_page > 1 ? '/popular.php?page='.($cur_page
                               - 1).'&tab='.$_GET['tab'] : '#' ?>">Предыдущая
                        страница</a>
                    <a class="popular__page-link button button--gray popular__page-link--next"
                       href="<?= $cur_page < $pages_count ? '/popular.php?page='
                           .($cur_page + 1).'&tab='.$_GET['tab'] : '#' ?>">Следующая
                        страница</a>
                <?php
                endif; ?>
                <?php
                if (!isset($_GET['tab']) || empty($_GET['tab'])): ?>
                    <a class="popular__page-link button button--gray popular__page-link--prev"
                       href="<?= $cur_page > 1 ? '/popular.php?page='.($cur_page
                               - 1) : '#' ?>">Предыдущая страница</a>
                    <a class="popular__page-link button button--gray popular__page-link--next"
                       href="<?= $cur_page < $pages_count ? '/popular.php?page='
                           .($cur_page + 1) : '#' ?>">Следующая страница</a>
                <?php
                endif; ?>
            </div>
        <?php
        endif; ?>
    </div>
</section>
