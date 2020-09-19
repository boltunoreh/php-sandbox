<?php
$messageTemplate = "Dear, %last_name% %first_name%, On %date%, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).";

preg_match('/%\w+%/', $messageTemplate, $matches);
var_dump($matches);

preg_match_all('/%\w+%/', $messageTemplate, $matches);
var_dump($matches);
