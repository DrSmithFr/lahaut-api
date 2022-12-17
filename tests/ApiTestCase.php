<?php

declare(strict_types = 1);

namespace App\Tests;

use LogicException;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

abstract class ApiTestCase extends WebTestCase
{
    protected function get(
        string $url,
        array $parameters = []
    ): KernelBrowser {
        $client = static::createClient();
        $client->enableProfiler();

        if (count($parameters)) {
            $url = sprintf('%s/%s', $url, http_build_query($parameters));
        }

        $client->request(
            'GET',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            );

        return $client;
    }

    protected function patch(
        string $url,
        object $object,
        array $group = ['Default']
    ): KernelBrowser {
        return $this->call('POST', $url, $object, $group);
    }

    protected function put(
        string $url,
        object $object,
        array $group = ['Default']
    ): KernelBrowser {
        return $this->call('POST', $url, $object, $group);
    }

    protected function post(
        string $url,
        object $object,
        array $group = ['Default']
    ): KernelBrowser {
        return $this->call('POST', $url, $object, $group);
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed  $object
     * @param array  $group
     *
     * @return KernelBrowser
     */
    protected function call(
        string $method,
        string $url,
        $object,
        array $group = ['Default']
    ): KernelBrowser {
        self::bootKernel();

        try {
            /** @var SerializerInterface $serializer */
            $serializer = self::$container->get(SerializerInterface::class);
        } catch (ServiceNotFoundException $e) {
            throw new LogicException('Serializer not found.');
        }

        $context = (SerializationContext::create())
            ->setSerializeNull(true)
            ->setGroups($group);

        $client = static::createClient();
        $client->enableProfiler();

        $client->request(
            $method,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $serializer->serialize($object, 'json', $context)
        );

        return $client;
    }
}
