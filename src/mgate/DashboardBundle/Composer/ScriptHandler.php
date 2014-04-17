<?php


namespace mgate\DashboardBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

class ScriptHandler
{
    /**
     * @param $event CommandEvent A instance
     */
    public static function runCmd(CommandEvent $event)
    {
       return;
    }
}
