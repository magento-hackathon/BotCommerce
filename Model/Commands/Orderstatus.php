<?php

namespace Hackathon\BotCommerce\Model\Commands;

use Hackathon\BotCommerce\Service\MessageService;

class Orderstatus extends AbstractCommand
{
    /**
     * @var MessageService
     */
    private $messageService;
    
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Get's triggered if one of the words of each line is present
     *
     * @var array
     */
    protected $_triggerwords = [
        ['order','package'],
        ['status','ship']
    ];

    public function executeCommand($body, $words)
    {
        preg_match('/([0-9]{9})/', $body, $incrementMatch);

        $status = $this->messageService->getOrderStatus($incrementMatch[0]);

        if (!is_null($status)) {
            return new \Magento\Framework\Phrase('Your order status is %1', [$status]);
        } else {
            return new \Magento\Framework\Phrase('Could not find your order', []);
        }
    }
}