<?php
$messageTemplate = "Dear, %last_name% %first_name%, On %date%, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).";
$placeholders = [
    'first_name' => '%first_name%',
    'last_name' => '%last_name%',
    'date' => '%date%'
];
$users = [];
$messages = [];

for ($i = 0; $i < 100000; $i++) {
    $users[] = [
        'first_name' => randomFirstName(),
        'last_name' => randomLastName(),
        'date' => randomDate(),
    ];
}

### STR_REPLACE
$startTime['str_replace'] = microtime(true);
foreach ($users as $key => $user) {
    $message = $messageTemplate;
    foreach ($placeholders as $param => $placeholder) {
        $message = str_replace($placeholder, $users[$key][$param], $message);
    }
    $messages['str_replace'][] = $message;
}
$endTime['str_replace'] = microtime(true);

### STR_REPLACE ARRAY
$startTime['str_replace_array'] = microtime(true);
foreach ($users as $key => $user) {
    $messages['str_replace_array'][] = str_replace($placeholders, $users[$key], $messageTemplate);
}
$endTime['str_replace_array'] = microtime(true);

### PREG_REPLACE
$startTime['preg_replace'] = microtime(true);
foreach ($users as $key => $user) {
    $message = $messageTemplate;
    foreach ($placeholders as $param => $placeholder) {
        $message = preg_replace("/$placeholder/", $users[$key][$param], $message);
    }
    $messages['preg_replace'][] = $message;
}
$endTime['preg_replace'] = microtime(true);

### PREG_REPLACE ARRAY
$startTime['preg_replace_array'] = microtime(true);
foreach ($users as $key => $user) {
    $messages['preg_replace_array'][] = preg_replace($placeholders, $users[$key], $message);
}
$endTime['preg_replace_array'] = microtime(true);

### STRTR
$startTime['strtr'] = microtime(true);
foreach ($users as $key => $user) {
    $message = $messageTemplate;
    foreach ($placeholders as $param => $placeholder) {
        $message = strtr($message, $placeholder, $users[$key][$param]);
    }
    $messages['strtr'][] = $message;
}
$endTime['strtr'] = microtime(true);

### STRTR ARRAY
$startTime['strtr_array'] = microtime(true);
foreach ($users as $key => $user) {
    $messages['strtr_array'][] = strtr($messageTemplate, array_combine(array_values($placeholders), array_values($users[$key])));
}
$endTime['strtr_array'] = microtime(true);

### BUILDER
$startTime['builder var'] = microtime(true);
$parts = preg_split("/(%first_name%|%date%|%last_name%)/", $messageTemplate, -1, PREG_SPLIT_DELIM_CAPTURE);
foreach ($parts as $key => $part) {
    if (preg_match('/%first_name%|%date%|%last_name%/', $part)) {
        $parts[$key] = [
          'text' => str_replace('%', '', $part),
          'is_placeholder' => true,
        ];
    } else {
      $parts[$key] = [
        'text' => $part,
        'is_placeholder' => false,
      ];
    }
}

foreach ($users as $key => $user) {
    $message = "";
    foreach ($parts as $part) {
        if ($part['is_placeholder']) {
            $message .= $users[$key][$part['text']];
        } else {
            $message .= $part['text'];
        }
    }
    $messages['builder var'][] = $message;
}

$endTime['builder var'] = microtime(true);

### BUILDER
$startTime['builder ob'] = microtime(true);
$parts = preg_split("/(%first_name%|%date%|%last_name%)/", $messageTemplate, -1, PREG_SPLIT_DELIM_CAPTURE);
foreach ($parts as $key => $part) {
    if (preg_match('/%first_name%|%date%|%last_name%/', $part)) {
        $parts[$key] = str_replace('%', '', $part);
    }
}

ob_start();
foreach ($users as $key => $user) {
    foreach ($parts as $part) {
        if (array_key_exists($part, $placeholders)) {
            echo $users[$key][$part];
        } else {
            echo $part;
        }
    }
    $messages['builder ob'][] = ob_get_contents();
    ob_clean();
}
ob_end_clean();

$endTime['builder ob'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
    echo $messages[$type][0] . PHP_EOL . PHP_EOL;
}


// functions
function randomDate()
{
    $start = new DateTime('10 year ago');
    $end = new DateTime();
    $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
    $randomDate = new DateTime();
    $randomDate->setTimestamp($randomTimestamp);
    return $randomDate->format('Y-m-d');
}

function randomFirstName()
{
    $names = [
        'Christopher',
        'Ryan',
        'Ethan',
        'John',
        'Zoey',
        'Sarah',
        'Michelle',
        'Samantha',
    ];

    return $names[mt_rand(0, sizeof($names) - 1)];
}

function randomLastName()
{
    $surnames = array(
        'Walker',
        'Thompson',
        'Anderson',
        'Johnson',
        'Tremblay',
        'Peltier',
        'Cunningham',
        'Simpson',
        'Mercado',
        'Sellers'
    );

    return $surnames[mt_rand(0, sizeof($surnames) - 1)];
}
