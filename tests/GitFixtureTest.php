<?php
/**
 * Testing Git.
 * @package GitFixture\Tests
 */

namespace Cognitive\Git\Tests;

use Cognitive\FileSystem\FileSystem;
use Cognitive\Git\GitFixture;

/**
 * Class to test Git
 */
class GitFixtureTest extends \PHPUnit_Framework_TestCase
{
    const COMMIT_HASH_LENGTH = 40;
    /** @var string */
    protected $dirTmp;
    /** @var string */
    protected $dirRoot;

    /** @var FileSystem Instance of the FileSystem class */
    protected $fileSystem;

    /**
     * GitTest constructor.
     * @return void
     */
    public function __construct()
    {
        $this->dirTmp = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'gitFixtureTmpDir';
        $this->fileSystem = new FileSystem();
    }

    /**
     * Init tmp directory.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->dirRoot = realpath(getcwd());
        $this->fileSystem->emptyDirectory($this->dirTmp);
        chdir($this->dirTmp);
    }

    /**
     * Clear tmp directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        chdir($this->dirRoot);
        $this->fileSystem->emptyDirectory($this->dirTmp);
        rmdir($this->dirTmp);
        parent::tearDown();
    }

    /**
     * Testing commit exception.
     *
     * @return void
     */
    public function testCommitExeption()
    {
        $git = new GitFixture();
        $this->setExpectedException('Cognitive\ShellExec\ShellExecException');
        $res = $git->commit('commit message');
        echo $res;
    }

    /**
     * Testing empty commit.
     *
     * @return void
     */
    public function testCommitEmpty()
    {
        $git = new GitFixture();
        $git->createRepo();
        $this->setExpectedExceptionRegExp('Cognitive\ShellExec\ShellExecException', '/nothing to commit/');
        $git->commit('commit message');
    }

    /**
     * Testing success commit.
     *
     * @return void
     */
    public function testCommitSuccess()
    {
        $git = new GitFixture();
        $git->createRepo();
        $git->addFile('test', 'test content');
        $git->commit('commit message');
        $rev = $git->getCurrentRev();
        $this->assertEquals(self::COMMIT_HASH_LENGTH, strlen($rev), 'Check hash');
    }
}
