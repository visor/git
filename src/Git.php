<?php
/**
 * Util for work with git.
 *
 * @package Cognitive\Git
 */

namespace Cognitive\Git;

use Cognitive\ShellExec\ShellExec;

/**
 * Useful git command from php method.
 */
class Git
{
    /** @var ShellExec Unit to exec shell command */
    protected $exec;

    /**
     * Git constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->exec = new ShellExec();
    }

    /**
     * Get list of commited files by commit hash.
     *
     * @param string $commit Commit hash.
     *
     * @return array(string)
     */
    public function getCommitedFilesByCommit($commit)
    {
        $result = $this->exec->exec("git show --pretty=\"format:\" --name-only $commit");
        $files = explode("\n", $result);

        $files = array_filter($files, function ($file) {
            return !empty($file);
        });

        $files = array_unique($files);
        return $files;
    }

    /**
     * Find cherry pick commit of $hashCommit in branch $branchName.
     *
     * @param string $branchName Branch name.
     * @param string $commitHash Commit hash.
     *
     * @return string|false Cherry pick commit hash or false.
     */
    public function findCherryPick($branchName, $commitHash)
    {
        $patchId = $this->getCommitPatchId($commitHash);
        if (!$patchId) {
            return false;
        }
        $authorName = $this->exec->exec("git show -s --format='%an' $commitHash", true);
        $authorDate = $this->exec->exec("git show -s --format='%ai' $commitHash", true);
        $candidateHashLines = $this->exec->explodeLinesToArray($this->exec->exec(
            "git rev-list --reverse " .
            "--author='$authorName' --after='$authorDate' $branchName",
            true
        ));
        foreach ($candidateHashLines as $candidateHash) {
            if ($candidateHash) {
                $candidatePatchId = $this->getCommitPatchId($candidateHash);
                if ($patchId === $candidatePatchId) {
                    return $candidateHash;
                }
            }
        }
        return false;
    }

    /**
     * Get patch id by commit hash.
     *
     * @param string $commitHash Commit hash.
     *
     * @return string|false Commit patch id or false.
     */
    public function getCommitPatchId($commitHash)
    {
        $patchId = trim(current(explode(" ", $this->exec->exec("git show $commitHash | git patch-id"))));
        if (empty($patchId)) {
            return false;
        } else {
            return $patchId;
        }
    }
}
