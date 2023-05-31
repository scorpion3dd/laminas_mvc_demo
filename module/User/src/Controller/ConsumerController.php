<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace User\Controller;

use User\Kafka\ConsumerKafka;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * This controller is Consumer Kafka
 * Class ConsumerController
 * @package User\Controller
 */
class ConsumerController extends AbstractActionController
{
    /** @var ConsumerKafka $consumerKafka */
    private ConsumerKafka $consumerKafka;

    /**
     * ConsumerController constructor
     * @param ConsumerKafka $consumerKafka
     */
    public function __construct(ConsumerKafka $consumerKafka)
    {
        $this->consumerKafka = $consumerKafka;
    }

    /**
     * This is the default "index" action of the controller. It displays the list of users
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $this->consumerKafka->start();

        return new ViewModel([
            'message' => '123'
        ]);
    }
}
