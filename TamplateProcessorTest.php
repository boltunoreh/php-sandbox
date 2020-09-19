<?php
$messageTemplate = "Dear, #first #last_name #first_name, on #date, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).";
$users = getUsers(100000);
$messages = [];


### constructMessage
$templateProcessor = TemplateProcessor::getInstance(1);
$startTime['constructMessage'] = microtime(true);

$templateProcessor->setMessage($messageTemplate);
foreach ($users as $replacements) {
    $messages['constructMessage'][] = $templateProcessor->constructMessage($replacements);
}

$endTime['constructMessage'] = microtime(true);


### prepareAnConstructMessage
$templateProcessor = TemplateProcessor::getInstance(1);

$startTime['prepareAndConstructMessage'] = microtime(true);
foreach ($users as $replacements) {
    $messages['prepareAndConstructMessage'][] = $templateProcessor->prepareAndConstructMessage($messageTemplate, $replacements);
}

$endTime['prepareAndConstructMessage'] = microtime(true);


### constructMessage for 4 messages
$users = getUsers(100000 / 4);
$messageTemplates = [
    "Dear, #last_name #first_name, on #first #date, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).",
    "Dear, #first #first_name #last_name, on #date, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).",
    "Dear, #last_name #first #first_name, on #date, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).",
    "Dear, #first #last_name #first_name, on #date, YouTuber Fapplet uploaded a montage parody video titled \"MLG Sample Text,\" gaining more than 149,000 views and 430 comments in the next nine months (shown below, left). On October 3rd, YouTuber Chuul0 uploaded a video titled \"Sample Text.mp4,\" which features a variety of a montage parody tropes set to the tune of the 1982 pop song \"Africa\" by Toto (shown below, right).",
];

$templateProcessor = TemplateProcessor::getInstance(1);
$startTime['constructMessage for 4 messages'] = microtime(true);

foreach ($messageTemplates as $messageTemplate) {
    $templateProcessor->setMessage($messageTemplate);
    foreach ($users as $replacements) {
        $messages['constructMessage for 4 messages'][] = $templateProcessor->constructMessage($replacements);
    }
}

$endTime['constructMessage for 4 messages'] = microtime(true);


### Echo results
foreach ($endTime as $type => $time) {
    echo "Total $type time: " . ($endTime[$type] - $startTime[$type]) . " sec" . PHP_EOL;
    echo $messages[$type][0] . PHP_EOL . PHP_EOL;
}

// functions
function getUsers($number)
{
    $users = [];
    for ($i = 0; $i < $number / 2; $i++) {
        $users[] = [
            'first' => 'mr',
            'first_name' => randomFirstName(),
            'last_name' => randomLastName(),
            'date' => randomDate(),
        ];
        $users[] = [
            'column_1' => 'mr (clmn)',
            'column_2' => randomFirstName() . '(clmn)',
            'column_3' => randomLastName() . '(clmn)',
        ];
    }
    shuffle($users);
    return $users;
}

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

class TemplateProcessor
{
    /**
     * @var int
     */
    private $partner_id;

    /**
     * @var NotificationHashtagsConfigManager
     */
    private $configManager;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array[]
     */
    private $parts;

    /**
     * @var array
     */
    private $hashtagConfigs = [];

    /**
     * Returns template processor instance
     *
     * @param $partner_id
     * @return TemplateProcessor
     * @throws Exception
     */
    public static function getInstance($partner_id)
    {
        static $instances = array();
        if (!isset($instances[$partner_id])) {
            $instances[$partner_id] = new static($partner_id);
        }
        return $instances[$partner_id];
    }

    private function __construct($partner_id)
    {
        $this->partner_id = (int)$partner_id;
        if ($this->partner_id <= 0) {
            throw new Exception('Failed to create TemplateProcessor instance: partner_id is mandatory');
        }
        $this->configManager = NotificationHashtagsConfigManager::getInstance($partner_id);
    }

    /**
     * Returns partner id
     *
     * @return int
     */
    public function getPartnerId()
    {
        return $this->partner_id;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        if ($message === $this->message) {
            return;
        }
        $this->message = $message;
        if ($hashtagConfigs = $this->getHashtagConfigs($message)) {
            $hashtags = array_keys($hashtagConfigs);
            krsort($hashtags);
            $pattern = implode('|', array_values($hashtags));
            $this->parts = preg_split("/($pattern)/", $message, -1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($this->parts as $key => $part) {
                if (preg_match("/$pattern/", $part)) {
                    $this->parts[$key] = [
                        'text' => $part,
                        'is_placeholder' => true,
                    ];
                } else {
                    $this->parts[$key] = [
                        'text' => $part,
                        'is_placeholder' => false,
                    ];
                }
            }
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getHashtagConfigs()
    {
        if ($this->message === null) {
            throw new Exception('message is not set');
        }
        if ($this->hashtagConfigs === []) {
            if (preg_match_all('/#\w+(?!\w)/', $this->message, $matches)) {
                $hashtagConfigs = $this->configManager->getMultipleTagsConfig($matches[0]);
                foreach ($hashtagConfigs as $hashtag => $hashtagConfig) {
                    $this->hashtagConfigs['#' . $hashtag] = $hashtagConfig;
                }
            }
        }
        return $this->hashtagConfigs;
    }

    /**
     * @param string $message
     * @return string
     */
    public function detectMessageType()
    {
        if ($hashtagConfigs = $this->getHashtagConfigs()) {
            /** @var  $hashtagConfig */
            foreach ($hashtagConfigs as $hashtagConfig) {
                if ($hashtagConfig['personalization_type_id'] === QDB_NjPersonalizationType::personal) {
                    return QDB_NjPersonalizationType::personal;
                }
            }
            return QDB_NjPersonalizationType::grouping;
        }
        return QDB_NjPersonalizationType::_static;
    }

    /**
     * @param array $replacements
     * @return string
     */
    public function constructMessage($replacements)
    {
        if ($hashtagConfigs = $this->getHashtagConfigs()) {
            $preparedReplacements = [];
            foreach ($hashtagConfigs as $hashtag => $hashtagConfig) {
                if (isset($replacements[$hashtagConfig['name']])) {
                    $preparedReplacements[$hashtag] = $replacements[$hashtagConfig['name']];
                } else if (isset($replacements[$hashtagConfig['expr']])) {
                    $preparedReplacements[$hashtag] = $replacements[$hashtagConfig['expr']];
                } else {
                    $preparedReplacements[$hashtag] = $hashtagConfig['default'];
                }
            }
            $message = "";
            foreach ($this->parts as $part) {
                if ($part['is_placeholder']) {
                    $message .= $preparedReplacements[$part['text']];
                } else {
                    $message .= $part['text'];
                }
            }
            return $message;
        }
        return $this->message;
    }

    /**
     * @param string $message
     * @param array $replacements
     * @return string
     */
    public function prepareAndConstructMessage($message, $replacements)
    {
        $this->setMessage($message);
        return $this->constructMessage($replacements);
    }
}

class QDB_NjPersonalizationType
{
    const _static = 1;
    const personal = 2;
    const grouping = 3;
}

class NotificationHashtagsConfigManager
{
    /**
     * Returns configuration manager instance
     *
     * @return NotificationHashtagsConfigManager
     * @throws Exception
     */
    public static function getInstance($partner_id)
    {
        static $instances = array();
        if (!isset($instances[$partner_id])) {
            $instances[$partner_id] = new static($partner_id);
        }
        return $instances[$partner_id];
    }

    private function __construct($partner_id)
    {
    }

    /**
     * Returns tag configuration array or FALSE if not exists
     *
     * @param array $tag_names
     *          - array of tag names
     * @param boolean $force_reload
     *          - set TRUE to reload from DB
     * @return array ($tag_name=>$tag_config_arr_if_exists)
     * @throws Exception
     */
    public function getMultipleTagsConfig(array $tag_names, $force_reload = false)
    {
        return [
            'first' => [
                'id' => 1,
                'name' => 'first',
                'partner_id' => 1,
                'personalization_type_id' => 2,
                'expr' => 'column_1',
                'is_mandatory' => true,
                'default' => 'Default First',
            ],
            'first_name' => [
                'id' => 2,
                'name' => 'first_name',
                'partner_id' => 1,
                'personalization_type_id' => 2,
                'expr' => 'column_2',
                'is_mandatory' => true,
                'default' => 'Default First Name',
            ],
            'last_name' => [
                'id' => 3,
                'name' => 'last_name',
                'partner_id' => 1,
                'personalization_type_id' => 2,
                'expr' => 'column_3',
                'is_mandatory' => true,
                'default' => 'Default Last Name',
            ],
            'date' => [
                'id' => 4,
                'name' => 'date',
                'partner_id' => 1,
                'personalization_type_id' => 2,
                'expr' => 'column_4',
                'is_mandatory' => true,
                'default' => 'Default Date',
            ],
        ];
    }
}
