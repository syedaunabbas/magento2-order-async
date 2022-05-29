<?php

/**
 * @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Custom\AsyncOrderFullFillment\Helper\AsyncOrderFullFillment;

class ShowErpTransmissionList extends Command
{

    const SUCCESS_FAIL_FLAG = 'success';

    /** @var AsyncOrderFullFillment */
    private $_helper;

    /**
     * NonVehicleCategoryUrlRewrite constructor.
     * @param F3DataHelper $f3DataHelper
     * @param null $name
     */
    public function __construct(
        AsyncOrderFullFillment $helper,
        $name = null
    ) {
        parent::__construct($name);
        $this->_helper = $helper;
    }

    protected function configure()
    {
        $commandoptions = [new InputOption(self::SUCCESS_FAIL_FLAG, null, InputOption::VALUE_REQUIRED, 1)];

        $this->setName('erp:tranmission');
        $this->setDescription('Show list of successfull or failed transmission attempts');
        $this->setDefinition($commandoptions);

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $successOrFailedFlag = ($input->getOption(self::SUCCESS_FAIL_FLAG)) ? $input->getOption(self::SUCCESS_FAIL_FLAG) : 0;

        $queuedResult = $this->_helper->getTransmissionsCollection($successOrFailedFlag);

        echo '<pre>';
        foreach ($queuedResult as $item) { // print transmission list
            print_r($item->getData());
        }
    }
}
