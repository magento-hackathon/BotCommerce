<?php

namespace Hackathon\BotCommerce\Controller\Slack;

use Hackathon\BotCommerce\Service\CommandrouterService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var CommandrouterService
     */
    private $commandService;

    /**
     * @param CommandrouterService $commandService
     * @param Context $context
     */
    public function __construct(CommandrouterService $commandService, Context $context)
    {
        $this->commandService = $commandService;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $result = $this->commandService->processMessage($data['text']);

        echo \GuzzleHttp\json_encode(['text' => $result]);
    }
}
