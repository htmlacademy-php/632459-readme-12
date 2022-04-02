<?php

    function utf8_wordwrap($string, $width, $break="#!#!")
    {
        $pattern = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
        $replace = '$1'.$break;
        return preg_replace($pattern, $replace, $string);
    }

    function clip_post_text(string $string, int $limit = 300): string
    {
        if (mb_strlen($string) > $limit) {
            $cutted_array = explode("#!#!", utf8_wordwrap($string, $limit, "#!#!"));
            return "<p>" . $cutted_array[0] . "..." . "</p>" . "<a class=\"post-text__more-link\" href=\"#\">Читать далее</a>";
        }
        return "<p>" . $string . "</p>";
    }

    function date_difference($date_1 , $date_2 , $differenceFormat = '%m %d %h %i'): array
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
		$date_intervals = ['months', 'days', 'hours', 'minutes'];
        $date_diff_values = explode(" ", $interval->format($differenceFormat));

		return array_combine($date_intervals, $date_diff_values);
    }

    function set_post_date($date, $short = false): array
	{
        $current_date = date('Y-m-d H:i:s');
		$time_title = date_format(date_create($date), 'd-m-Y H:i');
        $date_array = date_difference($current_date, $date);
        $delta_array = array_filter($date_array);
        $delta_value = array_key_first($delta_array);
        $delta_key = $delta_array[$delta_value];

        if ($delta_value === 'minutes') {
            $date_ago = !$short ? ($delta_key . get_noun_plural_form($delta_key, ' минута', ' минуты', ' минут') . ' назад') : $delta_key . ' мин назад';
        } else if ($delta_value === 'hours') {
            $date_ago = !$short ? ($delta_key . get_noun_plural_form($delta_key, ' час', ' часа', ' часов') . ' назад') : $delta_key . ' ч назад';
        } else if ($delta_value === 'days' && $delta_key < 7) {
            $date_ago = !$short ? ($delta_key . get_noun_plural_form($delta_key, ' день', ' дня', ' дней') . ' назад') : $delta_key . ' д назад';
        } else if ($delta_value === 'days' && $delta_key >= 7) {
			$date_ago = !$short ? (round(($delta_key / 7)) . get_noun_plural_form(round($delta_key / 7), ' неделя', ' недели', ' недель') . ' назад') : round(($delta_key / 7)) . ' нед назад';
		} else {
            $date_ago = !$short ? ($delta_key . get_noun_plural_form($delta_key, ' месяц', ' месяца', ' месяцев') . ' назад') : $delta_key . ' мес назад';
        }

        return [
            'time_title' => $time_title,
            'date_ago' => $date_ago
        ];
    }
