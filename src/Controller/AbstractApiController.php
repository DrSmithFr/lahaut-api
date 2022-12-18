<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use App\Controller\Traits\SerializerAware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    use SerializerAware;

    /**
     * UserController constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }
}
