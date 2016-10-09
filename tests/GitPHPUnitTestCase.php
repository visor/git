<?php
/**
 * Base class for unit test classes with build in Cognitive\Git\GitFixture.
 *
 * @package Cognitive\Git\Tests
 */

namespace Cognitive\Git\Tests;

use Cognitive\Git\GitFixture;
use Cognitive\ShellExec\ShellExec;

/**
 * Class GitPHPUnitTestCase
 */
class GitPHPUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var GitFixture */
    protected $git;
    /** @var ShellExec */
    protected $exec;

    /**
     * ReleaseCheckerTest constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->git = new GitFixture();
        $this->exec = new ShellExec();
    }

    /**
     * Init tmp directory.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->git->createRepo();
    }

    /**
     * Clear tmp directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->git->clearRepo();
    }
}
