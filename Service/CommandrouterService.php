<?php

namespace Hackathon\BotCommerce\Service;

use Hackathon\BotCommerce\Model\Commands\Orderstatus;
use Hackathon\BotCommerce\Wrapper;

class CommandrouterService
{

    protected $scopeConfig;
    protected $objectManager;
    private $orderStatus;


    public function __construct(
        Orderstatus $orderStatus,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->orderStatus = $orderStatus;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $message
     * @return string
     */
    public function processMessage($message)
    {
        $languageIso2 = substr($this->scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ), 0, 2);
        $language = Wrapper::getLanguage($languageIso2);

        $textProcessing = new Wrapper($message, $language);

        $ner = $textProcessing->getNER();

        $filterNERlist = [];
        foreach ($ner as $type) {
            $filterNERlist = array_merge(array_values($type), $filterNERlist);
        }

        $pos = $textProcessing->getPOS($textProcessing::OUTPUT_TAGGED);

        preg_match_all('/(\w+)\/(\w+)/i', $pos['text'], $matches);

        $textTypes = [];
        $i = 0;
        while ($i < count($matches[0])) {
            $type = $matches[2][$i];
            $value = $matches[1][$i];

//            if (in_array($value, $filterNERlist)) {
//                $i++;
//                continue;
//            }

            if (!isset($textTypes[$type])) {
                $textTypes[$type] = [];
            }

            if (in_array($type, ['V'])) {
                $stem = new Wrapper($value, $language);

                $value = $stem->getStem()['text'];
            }

            $textTypes[$type][] = $value;
            $i++;
        }

        $content = $this->matchCommand($textTypes['NN']);
        return implode("\n", $content);
    }

    public function getAllCommands()
    {
        /*
         * @todo make this dynamic with the array defined in a XML node that allows for easily adding more commands
         */
        return [
            'orderstatus' => [
                'class' => Orderstatus::class,
                'priority' => 1
            ],
            'contact' => [
                'class' => '\\Hackathon\\Botcommerce\\Model\\Commands\\Contact',
                'priority' => 3
            ]
        ];
    }

    public function matchCommand($keywords)
    {
        $commands = $this->getAllCommands();
        $content = [];

//        foreach ($commands as $command) {
//            $instance = $this->objectManager->create($command['class']);
//            if ($instance->matchKeywords($keywords)) { // check if the given keywords match the trigger wordts for the command
//                $content[] = $instance->executeCommand();
//            }
//        }

        if ($this->orderStatus->matchKeywords($keywords)) {
            $content[] = $this->orderStatus->executeCommand();
        }

        return $content;
    }
}
