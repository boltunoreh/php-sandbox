<?php
const NUM = 100000;
$user = [
    'name' => 'john',
    'last_name' => 'ivanov',
    'age' => 32,
    'sex' => 'male',
    'weight' => 70,
    'height' => 170,
    'contacts' => [
        'country' => 'Russian Federation',
        'city' => 'St,-Petersburg',
        'address_1' => 'Nevskiy pr. 1',
        'address_2' => '',
        'zip_code' => 190000,
        'phone' => '+7 (999) 666-66-66',
        'email' => 'info@examplde.com',
    ],
];

$p = [
    'event_class' => 'play',
    'event_data_arr' => [
        [
            'extra_data' => [
                'country' => 'Russian Federation',
                'city' => 'St,-Petersburg',
                'zip_code' => 190000,
                'phone' => '+7 (999) 666-66-66',
                'email' => 'info@examplde.com',
            ],
        ],
        [
            'extra_data' => [
                'address_1' => 'Nevskiy pr. 1',
                'address_2' => '',
            ],
        ],
    ],
];
//if ($p[Result::KEY_EVENT_EVENT_CLASS] === Event::EVENT_TYPE_PLAY) {
//    foreach ($p[Result::KEY_EVENT_SUB_EVENTS] as &$sub_event) {
//        $sub_event[Result::KEY_EVENT_EXTRA_DATA] = $sub_event[Result::KEY_EVENT_PLAY_EVENT_EXTRA_DATA];
//    }
//}
//class Result {
//    const KEY_EVENT_EVENT_CLASS = 'event_class';
//
//    const KEY_EVENT_SUB_EVENTS = 'event_data_arr';
//    const KEY_EVENT_EXTRA_DATA = 'extra_event_data';
//    const KEY_EVENT_PLAY_EVENT_EXTRA_DATA = 'extra_data';
//}
//class Event {
//    const EVENT_TYPE_APP = 'app';
//    const EVENT_TYPE_MEDIA = 'media';
//    const EVENT_TYPE_PLAY = 'play';
//}
//die;

### copy
$data = array_fill(0, NUM, $user);

$startTime['copy'] = microtime(true);
foreach ($data as &$item) {
    $item['extra_data'] = $item['contacts'];
}
$endTime['copy'] = microtime(true);

//check
$data[0]['contacts']['country'] = 'USA';
$result['copy'][] = $data[0];


### reference
$data = array_fill(0, NUM, $user);

$startTime['reference'] = microtime(true);
foreach ($data as &$item) {
    $item['extra_data'] = &$item['contacts'];
}
$endTime['reference'] = microtime(true);

//check
$data[0]['contacts']['country'] = 'USA';
$result['reference'][] = $data[0];

### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
    echo json_encode($result[$type][0]) . PHP_EOL . PHP_EOL;
}