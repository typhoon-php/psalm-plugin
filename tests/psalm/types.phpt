--FILE--
<?php

namespace Typhoon\PsalmPlugin;

use Typhoon\Type\types;

$_positiveInt = PsalmTest::extractType(types::positiveInt);
/** @psalm-check-type-exact $_positiveInt = \positive-int */

$_negativeInt = PsalmTest::extractType(types::negativeInt);
/** @psalm-check-type-exact $_negativeInt = \negative-int */

$_nonPositiveInt = PsalmTest::extractType(types::nonPositiveInt);
/** @psalm-check-type-exact $_nonPositiveInt = \non-positive-int */

$_nonNegativeInt = PsalmTest::extractType(types::nonNegativeInt);
/** @psalm-check-type-exact $_nonNegativeInt = \non-negative-int */

--EXPECT--
