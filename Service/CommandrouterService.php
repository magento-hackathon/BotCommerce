<?php

namespace Hackathon\BotCommerce\Service;

use Hackathon\BotCommerce;

class CommandrouterService
{

    protected $scopeConfig;
    protected $objectManager;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager= $objectManager;
    }

    /**
     * @param string $message
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

            if (in_array($value, $filterNERlist)) {
                $i++;
                continue;
            }

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

        $content = $this->matchCommand($textTypes['N']);
        return implode("\n", $content);
    }

    public function getAllCommands()
    {
        /*
         * @todo make this dynamic with the array defined in a XML node that allows for easily adding more commands
         */
        return [
            'orderstatus' => [
                'class' => '\\Hackathon\\Botcommerce\\Model\\Commands\\Orderstatus',
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

        foreach ($commands as $command) {
            $instance = $this->objectManager->create($command['class']);
            if ($instance->matchKeywords($keywords)) { // check if the given keywords match the trigger wordts for the command
                $content[] = $instance->executeCommand();
            }
        }

        return $content;
    }
}
