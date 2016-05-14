<?php namespace Hackaton_BotCommerce;

require_once 'vendor/autoload.php';

use Slack;
use React\EventLoop\Factory as ReactFactory;

$loop = ReactFactory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken('xoxb-42966162917-l8AHXrvRC4nR8tuY6mxMbr1i');

// disconnect after first message
$client->on('message', function ($data) use ($client) {
    $client->getChannelGroupOrDMByID($data['channel'])->then(function (Slack\ChannelInterface $channel) use (
        $client,
        $data
    ) {
        $client->send('Hello from PHP! Your message is: ' . $data['text'], $channel);
    });
});

$client->connect()->then(function () {
    echo "Connected!\n";
});

$loop->run();
