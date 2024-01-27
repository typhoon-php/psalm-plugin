<?php

declare(strict_types=1);

namespace Typhoon\PsalmPlugin;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use PHPyh\PsalmTester\PsalmTest as Test;
use PHPyh\PsalmTester\PsalmTester;
use Typhoon\Type\Type;

final class PsalmTest extends TestCase
{
    private ?PsalmTester $psalmTester = null;

    /**
     * @template TType
     * @param Type<TType> $_type
     * @return TType
     */
    public static function extractType(Type $_type): mixed
    {
        /** @var TType */
        return null;
    }

    #[TestWith([__DIR__ . '/psalm/types.phpt'])]
    public function testPhptFiles(string $phptFile): void
    {
        $this->psalmTester ??= PsalmTester::create(
            defaultArguments: '--no-progress --no-diff --config=' . __DIR__ . '/psalm.xml',
        );
        $this->psalmTester->test(Test::fromPhptFile($phptFile));
    }
}
