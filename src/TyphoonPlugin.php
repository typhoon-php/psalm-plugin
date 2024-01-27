<?php

declare(strict_types=1);

namespace Typhoon\PsalmPlugin;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\Type\Atomic;
use Psalm\Type\Union;
use Typhoon\Type\IntRangeType;
use Typhoon\Type\types;

/**
 * @api
 */
final class TyphoonPlugin implements PluginEntryPointInterface, AfterExpressionAnalysisInterface
{
    /**
     * @var non-empty-array<string, ?Union>
     */
    private static array $constantTypes = [
        'positiveInt' => null,
        'negativeInt' => null,
        'nonPositiveInt' => null,
        'nonNegativeInt' => null,
    ];

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();
        $typeProvider = $event->getStatementsSource()->getNodeTypeProvider();

        if (!$expr instanceof ClassConstFetch
            || $expr->class->getAttribute('resolvedName') !== types::class
            || !$expr->name instanceof Identifier
            || !\array_key_exists($expr->name->name, self::$constantTypes)
        ) {
            return null;
        }

        $typeProvider->setType($expr, self::constantType($expr->name->name));

        return null;
    }

    private static function constantType(string $name): Union
    {
        return self::$constantTypes[$name] ??= match ($name) {
            'positiveInt' => self::intRangeType(min: 1),
            'negativeInt' => self::intRangeType(max: -1),
            'nonPositiveInt' => self::intRangeType(max: 0),
            'nonNegativeInt' => self::intRangeType(min: 0),
        };
    }

    private static function intRangeType(?int $min = null, ?int $max = null): Union
    {
        return new Union([new Atomic\TGenericObject(IntRangeType::class, [
            new Union([new Atomic\TIntRange($min, $max)]),
        ])]);
    }

    public function __invoke(RegistrationInterface $registration, ?\SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }
}
