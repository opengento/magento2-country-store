<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\CountryStore\Test\Unit\Model\Country;

use Magento\Framework\ObjectManagerInterface;
use Opengento\CountryStore\Api\CountryResolverInterface;
use Opengento\CountryStore\Model\Resolver\ResolverFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * @covers \Opengento\CountryStore\Model\ResolverFactory
 */
class ResolverFactoryTest extends TestCase
{
    private MockObject|ObjectManagerInterface $objectFactory;
    private ResolverFactory $resolverFactory;

    protected function setUp(): void
    {
        $this->objectFactory = $this->getMockForAbstractClass(ObjectManagerInterface::class);

        $this->resolverFactory = new ResolverFactory(
            $this->objectFactory,
            [
                'resolverA' => 'Vendor\Module\Model\ResolverA',
                'resolverB' => 'Vendor\Module\Model\ResolverB',
            ]
        );
    }

    /**
     * @dataProvider resolverData
     */
    public function testGet(array $resolvers, array $resolverCodes): void
    {
        $count = count($resolverCodes);
        $this->objectFactory->expects($this->exactly($count))
            ->method('get')
            ->willReturnMap($resolvers);

        $lastResolver = null;
        foreach ($resolverCodes as $resolverCode => $resolver) {
            $currentResolver = $this->resolverFactory->get($resolverCode);
            $this->assertSame($resolver, $currentResolver);
            if ($lastResolver) {
                $this->assertNotSame($lastResolver, $currentResolver);
            }
            $lastResolver = $currentResolver;
        }
    }

    public function resolverData(): array
    {
        $resolverA = $this->getMockForAbstractClass(CountryResolverInterface::class);
        $resolverB = $this->getMockForAbstractClass(CountryResolverInterface::class);

        return [
            [
                [
                    ['Vendor\Module\Model\ResolverA', $resolverA],
                    ['Vendor\Module\Model\ResolverB', $resolverB],
                ],
                compact('resolverA', 'resolverB')
            ]
        ];
    }
}
