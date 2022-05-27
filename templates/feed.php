<main class="page__main page__main--feed">
      <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
      </div>
      <div class="page__main-wrapper container">
        <section class="feed">
          <h2 class="visually-hidden">Лента</h2>
          <div class="feed__main-wrapper">
            <div class="feed__wrapper">
            <?php foreach($posts as $post): ?>
              <article class="feed__post post <?= $post['class'] ?? '' ?>">
                <header class="post__header post__author">
                  <a class="post__author-link" href="#" title="Автор">
                    <div class="post__avatar-wrapper">
                      <img class="post__author-avatar" src="img/<?= htmlspecialchars($post['avatar_path'] ?? '') ?>" alt="Аватар пользователя" width="60" height="60">
                    </div>
                    <div class="post__info">
                      <b class="post__author-name"><?= htmlspecialchars($post['login'] ?? '') ?></b>
                      <span class="post__time"><?= set_post_date($post['date_add'])['date_ago'] ?? '' ?></span>
                    </div>
                  </a>
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
                    <h2><a href="#"><?= $post['title'] ?? '' ?></a></h2>
                        <?= clip_post_text(htmlspecialchars($post['text'] ?? '')) ?>
                    <?php break; ?>

                    <?php case 'photo': ?>
                        <h2><a href="#"><?= $post['title'] ?? '' ?></a></h2>
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
                <footer class="post__footer post__indicators">
                  <div class="post__buttons">
                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                      <svg class="post__indicator-icon" width="20" height="17">
                        <use xlink:href="#icon-heart"></use>
                      </svg>
                      <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                        <use xlink:href="#icon-heart-active"></use>
                      </svg>
                      <span>250</span>
                      <span class="visually-hidden">количество лайков</span>
                    </a>
                    <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                      <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-comment"></use>
                      </svg>
                      <span>25</span>
                      <span class="visually-hidden">количество комментариев</span>
                    </a>
                    <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                      <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-repost"></use>
                      </svg>
                      <span>5</span>
                      <span class="visually-hidden">количество репостов</span>
                    </a>
                  </div>
                <ul class="post__tags">
                    <?php foreach ($feed_hashtags[$post['id']] as $hashtag): ?>
                    <li><a href="#">#<?= $hashtag['hashtag_name'] ?? '' ?></a></li>
                    <?php endforeach; ?>
                </ul>
                </footer>
              </article>
              <?php endforeach; ?>
            </div>
          </div>
          <ul class="feed__filters filters">
            <li class="feed__filters-item filters__item">
              <a class="filters__button <?= !isset($_GET['tab']) || $_GET['tab'] === '' ? 'filters__button--active' : '' ?>" href="/feed.php?user=<?= $_SESSION['user']['id'] ?>&tab">
                <span>Все</span>
              </a>
            </li>
            <?php foreach($types as $type): ?>
            <li class="feed__filters-item filters__item">
              <a class="filters__button filters__button--<?= $type['type'] ?> button <?= isset($_GET['tab']) && $_GET['tab'] === $type['id'] ? 'filters__button--active' : '' ?>" href="/feed.php?user=<?= $_SESSION['user']['id']  ?>&tab=<?= $type['id'] ?>">
                <span class="visually-hidden"><?= $type['name'] ?></span>
                <svg class="filters__icon" width="22" height="18">
                  <use xlink:href="#icon-filter-<?= $type['type'] ?>"></use>
                </svg>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <aside class="promo">
          <article class="promo__block promo__block--barbershop">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
            </p>
            <a class="promo__link" href="#">
              Подробнее
            </a>
          </article>
          <article class="promo__block promo__block--technomart">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Товары будущего уже сегодня в онлайн-сторе Техномарт!
            </p>
            <a class="promo__link" href="#">
              Перейти в магазин
            </a>
          </article>
          <article class="promo__block">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Здесь<br> могла быть<br> ваша реклама
            </p>
            <a class="promo__link" href="#">
              Разместить
            </a>
          </article>
        </aside>
      </div>
    </main>