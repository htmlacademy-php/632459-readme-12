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
?>
