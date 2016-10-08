<?php
/**
 * Class to emulate git repository in unit tests.
 * @package GitFixture
 */

namespace GitFixture;

use ShellExec\ShellExec;
use GitFixture\Util\FileSystem;

/**
 * Class to emulate git.
 */
class Git
{
    /** @var string Git server repo path */
    protected $dirServer = '.gitFixtureDirServer';
    /** @var string Git client repo path */
    protected $dirClient = '.gitFixtureDirClient';
    /** @var string root dir */
    protected $dirRoot;

    /** @var Filesystem Instance of the Filesystem class */
    protected $fileSystem;

    /** @var ShellExec Unit to exec shell command */
    protected $exec;

    /**
     * MockGitRepository constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->exec = new ShellExec();
        $this->fileSystem = new FileSystem();

        $this->dirRoot = getcwd();
        $this->dirServer = $this->dirRoot . DIRECTORY_SEPARATOR . $this->dirServer;
        $this->dirClient = $this->dirRoot . DIRECTORY_SEPARATOR . $this->dirClient;
    }

    /**
     * Get directory client repository.
     *
     * @return string
     */
    public function getDirClient()
    {
        return $this->dirClient;
    }

    /**
     * Get directory server repository.
     *
     * @return string
     */
    public function getDirServer()
    {
        return $this->dirServer;
    }

    /**
     * Get root directory.
     *
     * @return string
     */
    public function getDirRoot()
    {
        return $this->dirRoot;
    }

    /**
     * Create repositories.
     *
     * @return void
     */
    public function createRepo()
    {
        $this->removeRepo();

        chdir($this->dirServer);
        $this->exec->exec('git --bare init', true);

        chdir($this->dirClient);
        $this->exec->exec('git clone ' . $this->dirServer . ' .', true);
        $this->setUserEmail('good@email.com');
        $this->setUserName('Good Name');
    }

    /**
     * Remove repositories.
     *
     * @return void
     */
    public function removeRepo()
    {
        $this->fileSystem->emptyDirectory($this->dirServer);
        $this->fileSystem->emptyDirectory($this->dirClient);
    }

    /**
     * Remove repositories and remove directories.
     *
     * @return void
     */
    public function clearRepo()
    {
        $this->removeRepo();
        rmdir($this->dirServer);
        rmdir($this->dirClient);
    }

    /**
     * Set user email in local rep.
     *
     * @param string $email Email.
     *
     * @return void
     */
    public function setUserEmail($email)
    {
        chdir($this->dirClient);
        $this->exec->exec('git config --local user.email '. $email);
    }

    /**
     * Set user name in local rep.
     *
     * @param string $name User name.
     *
     * @return void
     */
    public function setUserName($name)
    {
        chdir($this->dirClient);
        $this->exec->exec('git config --local user.name "'. $name . '"');
    }

    /**
     * Add file to git index.
     *
     * @param string $fileName File name.
     * @param string $content  File context.
     *
     * @return void
     */
    public function addFile($fileName, $content)
    {
        $this->addFileWithoutIndex($fileName, $content);
        $this->exec->exec("git add $fileName", true);
    }

    /**
     * Add file without put to git index.
     *
     * @param string $fileName File name.
     * @param string $content  File context.
     *
     * @return void
     */
    public function addFileWithoutIndex($fileName, $content)
    {
        chdir($this->dirClient);
        $file = $this->dirClient . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($file, $content);
    }

    /**
     * Commit with message.
     *
     * @param string $message Commit message.
     *
     * @return string
     */
    public function commit($message)
    {
        return $this->exec->exec('git commit -m "' . $message . '"', true);
    }

    /**
     * Get current revision hash.
     *
     * @return string
     */
    public function getCurrentRev()
    {
        chdir($this->dirClient);
        return $this->exec->exec("git rev-parse HEAD", true);
    }

    /**
     * Create branch.
     *
     * @param string      $branchName Branch Name.
     * @param string|null $fromRev    Commit hash to create branch from.
     *
     * @return void
     */
    public function createBranch($branchName, $fromRev = null)
    {
        chdir($this->dirClient);
        $this->exec->exec("git checkout -b $branchName $fromRev", true);
    }

    /**
     * Merge branch.
     *
     * @param string $branchName Branch name.
     * @param string $options    Options.
     *
     * @return void
     */
    public function mergeBranch($branchName, $options = '')
    {
        chdir($this->dirClient);
        $this->exec->exec("git merge $options $branchName", true);
    }

    /**
     * Get file content.
     *
     * @param string $fileName File name.
     *
     * @return string
     */
    public function getFileContent($fileName)
    {
        return file_get_contents($fileName);
    }

    /**
     * Create merge with option no commit.
     *
     * @param string $mergeOptions Options.
     *
     * @return void
     */
    public function createMerge($mergeOptions = '')
    {
        $this->commit('first commit');
        $rev = $this->getCurrentRev();

        $this->createBranch('feature/ETPKIM/8');
        $this->addFile('test1.php', '');
        $this->commit('second commit');

        $this->createBranch('feature/ETPKIM/10', $rev);
        $this->addFile('test2.php', '');
        $this->commit('third commit');

        $this->mergeBranch('feature/ETPKIM/8', $mergeOptions);
    }
}
