<?php
    require_once 'init.php';
    require_once 'helpers.php';
    require_once 'functions.php';

    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка подключения: " . $error);
        die();
    }

    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    require 'vendor/autoload.php';
    $dsn = 'smtp://c60eabd512126a:f48119633abff7@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
    $transport = Transport::fromDsn($dsn);

    $user_id = filter_input(INPUT_GET, 'user');

    $is_subscribe = false;

    $sql_subscriber = 'SELECT subscribe_id FROM subscriptions WHERE subscribe_id = ? AND follower_id = ?';
    $result = formSqlRequest($con, $sql_subscriber, [$user_id, $_SESSION['user']['id']]);

    if (mysqli_num_rows($result) > 0) {
        $is_subscribe = true;
    }

    if ($user_id && !$is_subscribe) {
        $sql_subscribe = 'INSERT INTO subscriptions(subscribe_id, follower_id) VALUES(?, ' . $_SESSION['user']['id'] . ')';
        $result = formSqlRequest($con, $sql_subscribe, [$user_id], false);

        $sql_message_subscribe = 'SELECT email, login FROM users WHERE id = ?';
        $result = formSqlRequest($con, $sql_message_subscribe, [$user_id]);
        $message_subscribe = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $sql_message_follower = 'SELECT id, login FROM users WHERE id = ?';
        $result = formSqlRequest($con, $sql_message_follower, [$_SESSION['user']['id']]);
        $message_follower = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $message = new Email();
        $message->to($message_subscribe['email']);
        $message->from("mail@readme.com");
        $message->subject("У вас новый подписчик");
        $message->text("Здравствуйте, ${message_subscribe['login']}.
        На вас подписался новый пользователь ${message_follower['login']}.
        Вот ссылка на его профиль: http://readme/profile.php?user=${message_follower['id']}&tab=posts");
        $mailer = new Mailer($transport);
        try {
            $mailer->send($message);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
        }
        header("Location: profile.php?user=" . $user_id . "&tab=posts");
    }

    if($user_id && $is_subscribe) {
        $sql_subscribe = 'DELETE FROM subscriptions WHERE subscribe_id = ? AND follower_id = ?';
        $result = formSqlRequest($con, $sql_subscribe, [$user_id, $_SESSION['user']['id']], false);
        header("Location: profile.php?user=" . $user_id . "&tab=posts");
    }


