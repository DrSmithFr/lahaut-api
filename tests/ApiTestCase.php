<?php

declare(strict_types=1);

namespace App\Tests;

use Exception;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function patch(
        string $url,
        mixed $object,
        array $group = ['Default']
    ): Crawler {
        return $this->call('PATCH', $url, $object, $group);
    }

    protected function put(
        string $url,
        mixed $object,
        array $group = ['Default']
    ): Crawler {
        return $this->call('PUT', $url, $object, $group);
    }

    protected function post(
        string $url,
        mixed $object,
        array $group = ['Default']
    ): Crawler {
        return $this->call('POST', $url, $object, $group);
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed  $object
     * @param array  $group
     *
     * @return Crawler
     * @throws Exception
     */
    protected function call(
        string $method,
        string $url,
        mixed $object,
        array $group = ['Default']
    ): Crawler {
        $container = self::getContainer();

        try {
            $serializer = $container->get(SerializerInterface::class);
        } catch (ServiceNotFoundException $e) {
            throw new LogicException('Serializer not found.');
        }

        $context = (SerializationContext::create())
            ->setSerializeNull(true)
            ->setGroups($group);

        $json = $serializer->serialize($object, 'json', $context);

        $this->client->enableProfiler();

        return $this->client->request(
            $method,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
    }

    protected function getResponse(): Response
    {
        return $this->client->getResponse();
    }
}
