<?php
/**
 * Testing Git.
 * @package Cognitive\Git\Tests
 */

namespace Cognitive\Git\Tests;

use Cognitive\Git\Git;

/**
 * Class GitTest
 */
class GitTest extends GitPHPUnitTestCase
{
    /**
     * Testing findCherryPick
     *
     * @return void
     */
    public function testFindCherryPick()
    {
        $git = new Git();

        $this->git->addFile('test1', '');
        $rev = $this->git->commit('first commit');
        $this->git->createBranch('branch1');
        $this->git->addFile('test2', '');
        $rev2 = $this->git->commit('second commit');

        $res = $git->findCherryPick('master', $rev2);
        $this->assertEquals(false, $res, 'check no commit in master');

        $res = $git->findCherryPick('branch1', $rev);
        $this->assertEquals($rev, $res, 'check commit the same');

        $this->git->createBranch('branch2', $rev);
        $res = $git->findCherryPick('branch2', $rev2);
        $this->assertEquals(false, $res, 'check no commit in branch2');

        $this->exec->exec("git cherry-pick $rev2");
        $revCherryPick = $this->git->getCurrentRev();

        $res = $git->findCherryPick('branch2', $rev2);
        $this->assertEquals($revCherryPick, $res, 'check cherry pick');
    }
}
