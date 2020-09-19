<?php
$messageTemplate = "Dear, #first #last_name #first_name, On #date, YouTuber Fapplet #last_name #first #first_name #date uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right). Dear, #first #last_name #first_name, On #date. Dear, #first #last_name #first_name, On #date, YouTuber Fapplet #last_name #first #first_name #date uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right). Dear, #first #last_name #first_name, On #date.";
$placeholders = [
    'first' => '#first',
    'first_name' => '#first_name',
    'last_name' => '#last_name',
    'date' => '#date'
];
$users = [];
$messages = [];

for ($i = 0; $i < 100000; $i++) {
    $users[] = [
        'first' => 'mr',
        'first_name' => randomFirstName(),
        'last_name' => randomLastName(),
        'date' => randomDate(),
    ];
}

### STR_REPLACE ARRAY
$startTime['str_replace_array'] = microtime(true);
krsort($placeholders);
foreach ($users as $key => $user) {
    krsort($users[$key]);
    $messages['str_replace_array'][] = str_replace($placeholders, $users[$key], $messageTemplate);
}
$endTime['str_replace_array'] = microtime(true);

### PREG_REPLACE ARRAY
$startTime['preg_replace_array'] = microtime(true);
$patterns = [];
foreach ($placeholders as $placeholder) {
    $patterns[] = "/{$placeholder}(?!\w)/";
}
foreach ($users as $key => $user) {
    $message = $messageTemplate;
    $messages['preg_replace_array'][] = preg_replace($patterns, $users[$key], $message);
}
$endTime['preg_replace_array'] = microtime(true);

### BUILDER
$startTime['builder concat'] = microtime(true);
krsort($placeholders);
$pattern = implode('|', $placeholders);
$parts = preg_split("/($pattern)/", $messageTemplate, -1, PREG_SPLIT_DELIM_CAPTURE);
foreach ($parts as $key => $part) {
    if (preg_match("/$pattern/", $part)) {
        $parts[$key] = [
            'text' => str_replace('#', '', $part),
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
    $messages['builder concat'][] = $message;
}

$endTime['builder concat'] = microtime(true);

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
//    echo $messages[$type][0] . PHP_EOL . PHP_EOL;
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
