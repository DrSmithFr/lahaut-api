<?php

namespace App\Tests\EventSubscriber;

use JsonException;
use App\EventSubscriber\JsonPostSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class JsonPostSubscriberTest extends KernelTestCase
{
    private ?JsonPostSubscriber $service = null;

    protected function setUp(): void
    {
        $this->service = new JsonPostSubscriber();
    }

    public function testNotJsonType(): void
    {
        $request = $this
            ->createMock(Request::class);

        $request
            ->expects(self::once())
            ->method('getContentTypeFormat')
            ->willReturn('xml');

        $request
            ->expects(self::never())
            ->method('getContent');

        $event = new ControllerEvent(
            $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            function () {},
            $request,
            1
        );

        $this->service->convertJsonStringToArray($event);
    }

    public function testBadJsonType(): void
    {
        $request = $this
            ->createMock(Request::class);

        $request
            ->expects(self::once())
            ->method('getContentTypeFormat')
            ->willReturn('json');

        $request
            ->expects(self::exactly(2))
            ->method('getContent')
            ->willReturn('{');

        $event = new ControllerEvent(
            $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            function () {},
            $request,
            1
        );

        $this->expectException(JsonException::class);
        $this->service->convertJsonStringToArray($event);
    }

    public function testGoodJsonType(): void
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects(self::once())
            ->method('getContentTypeFormat')
            ->willReturn('json');

        $request
            ->expects(self::exactly(2))
            ->method('getContent')
            ->willReturn('{"test":true}');

        $param = $this
            ->createMock(ParameterBag::class);

        $param
            ->expects(self::once())
            ->method('replace')
            ->with(['test' => true]);

        $request->request = $param;

        $event = new ControllerEvent(
            $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            function () {},
            $request,
            1
        );

        $this->service->convertJsonStringToArray($event);
    }
}
