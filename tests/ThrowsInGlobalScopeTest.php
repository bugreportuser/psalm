<?php
namespace Psalm\Tests;

use Psalm\Config;
use Psalm\Context;

class ThrowsInGlobalScopeTest extends TestCase
{
    /**
     * @expectedException        \Psalm\Exception\CodeException
     * @expectedExceptionMessage UncaughtThrowInGlobalScope
     *
     * @return                   void
     */
    public function testUncaughtDocumentedThrowCall()
    {
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                /**
                 * @throws RangeException
                 * @throws InvalidArgumentException
                 */
                function foo(int $x, int $y) : int {
                    if ($y === 0) {
                        throw new \RangeException("Cannot divide by zero");
                    }

                    if ($y < 0) {
                        throw new \InvalidArgumentException("This is also bad");
                    }

                    return intdiv($x, $y);
                }

                foo(0, 0);'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }

    /**
     * @return                   void
     */
    public function testCaughtDocumentedThrowCall()
    {
        Config::getInstance()->check_for_throws_docblock = true;
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                /**
                 * @throws RangeException
                 * @throws InvalidArgumentException
                 */
                function foo(int $x, int $y) : int {
                    if ($y === 0) {
                        throw new \RangeException("Cannot divide by zero");
                    }

                    if ($y < 0) {
                        throw new \InvalidArgumentException("This is also bad");
                    }

                    return intdiv($x, $y);
                }

                try {
                    foo(0, 0);
                } catch (Exception $e) {}'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }

    /**
     * @return                   void
     */
    public function testUncaughtUndocumentedThrowCall()
    {
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                function foo(int $x, int $y) : int {
                    if ($y === 0) {
                        throw new \RangeException("Cannot divide by zero");
                    }

                    if ($y < 0) {
                        throw new \InvalidArgumentException("This is also bad");
                    }

                    return intdiv($x, $y);
                }

                foo(0, 0);'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }

    /**
     * @expectedException        \Psalm\Exception\CodeException
     * @expectedExceptionMessage UncaughtThrowInGlobalScope
     *
     * @return                   void
     */
    public function testUncaughtDocumentedThrowCallInNamespace()
    {
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                namespace ns;
                /**
                 * @throws RangeException
                 * @throws InvalidArgumentException
                 */
                function foo(int $x, int $y) : int {
                    if ($y === 0) {
                        throw new \RangeException("Cannot divide by zero");
                    }

                    if ($y < 0) {
                        throw new \InvalidArgumentException("This is also bad");
                    }

                    return intdiv($x, $y);
                }

                foo(0, 0);'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }

    /**
     * @expectedException        \Psalm\Exception\CodeException
     * @expectedExceptionMessage UncaughtThrowInGlobalScope
     *
     * @return                   void
     */
    public function testUncaughtThrow()
    {
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                throw new \Exception();'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }

    /**
     * @return                   void
     */
    public function testCaughtThrow()
    {
        Config::getInstance()->check_for_throws_in_global_scope = true;

        $this->addFile(
            'somefile.php',
            '<?php
                try {
                    throw new \Exception();
                } catch (\Exception $e) {}'
        );

        $context = new Context();

        $this->analyzeFile('somefile.php', $context);
    }
}
