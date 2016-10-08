<?php
/**
 * Testing ShellExec.
 * @package GitFixture\Tests
 */

namespace GitFixture\Tests\Util;

use GitFixture\Util\ShellExec;

/**
 * Class to test Git
 */
class ShellExecTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test ShellExecExeption.
     *
     * @return void
     */
    public function testShellExecExeption()
    {
        $shellExec = new ShellExec();
        $this->setExpectedExceptionRegExp('GitFixture\Util\ShellExecException', '/Command not found/');
        $shellExec->exec('CommandNotFound', true);
    }
}
