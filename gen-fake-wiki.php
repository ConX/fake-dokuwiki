#!/usr/bin/env php
<?php

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

if (!defined('DOKU_INC')) define('DOKU_INC', realpath(dirname(__FILE__) . '/../') . '/');
define('NOSESSION', 1);
require_once(DOKU_INC . 'inc/init.php');

require_once '/opt/vendor/autoload.php';

/**
 * Checkout and commit pages from the command line while maintaining the history
 */
class PageCLI extends CLI
{
    protected $force = false;
    protected $username = '';

    /**
     * Register options and arguments on the given $options object
     *
     * @param Options $options
     * @return void
     */
    protected function setup(Options $options)
    {
        /* global */
        $options->registerOption(
            'force',
            'force obtaining a lock for the page (generally bad idea)',
            'f'
        );
        $options->registerOption(
            'num_users',
            'the number of users to generate pages from',
            'u',
            "users"
        );
        $options->registerOption(
            'num_pages',
            'the number of pages to generate',
            'p',
            "pages"
        );
        $options->registerOption(
            'num_namespaces',
            'the number of namespaces to generate',
            'n',
            "namespaces"
        );
        $options->setHelp(
            'Utility to generate fake pages and corrsponding metadata'
        );
    }

    /**
     * Your main program
     *
     * Arguments and options have been parsed when this is run
     *
     * @param Options $options
     * @return void
     */
    protected function main(Options $options)
    {
        $this->force = $options->getOpt('force', false);
        $env_num_users = getenv("FAKE_DW_USERS");
        $env_num_pages = getenv("FAKE_DW_PAGES");
        $env_num_namespaces = getenv("FAKE_DW_NAMESPACES");
        $this->num_users = $options->getOpt('num_users', $env_num_users ? $env_num_users : 10);
        $this->num_pages = $options->getOpt('num_pages', $env_num_pages ? $env_num_pages : 100);
        $this->num_namespaces = $options->getOpt('num_namespaces', $env_num_namespaces ? $env_num_namespaces : 3);

        $faker = Faker\Factory::create();
        // $faker->seed(42);
        $users = array();
        for ($x = 0; $x <= (int) $this->num_users; $x++)
            array_push($users, $faker->userName());

        $namespaces = array();
        for ($x = 0; $x <= (int) $this->num_users; $x++)
            array_push($namespaces, $faker->word());

        echo var_export($users);
        echo var_export($namespaces);
        array_push($namespaces, "");

        for ($x = 0; $x <= (int) $this->num_pages; $x++) {
            $page = $faker->slug(4);
            $message = $faker->sentence();
            $content = $faker->text();
            $namespace = $namespaces[array_rand($namespaces)];

            if ($namespace !== "")
                $page =  $namespace . ":" . $page;

            $user = $users[array_rand($users)];
            echo $x . " adding page " . $page . " by user " . $user . " with message " . $message . "\n";
            $this->commandCommit(
                $content,
                $page,
                $message,
                $user
            );
        }
    }

    /**
     * Save a file as a new page revision
     *
     * @param string $localfile
     * @param string $wiki_id
     * @param string $message
     * @param bool $minor
     */
    protected function commandCommit($pagecontent, $wiki_id, $message, $user)
    {
        $wiki_id = cleanID($wiki_id);
        $message = trim($message);

        $this->username = $user;

        if (!$message) {
            $this->fatal("Summary message required");
        }

        $this->obtainLock($wiki_id);

        saveWikiText($wiki_id, $pagecontent, $message, false);

        $this->clearLock($wiki_id);

        $this->success("$localfile > $wiki_id");
    }

    /**
     * Lock the given page or exit
     *
     * @param string $wiki_id
     */
    protected function obtainLock($wiki_id)
    {
        if ($this->force) $this->deleteLock($wiki_id);

        $_SERVER['REMOTE_USER'] = $this->username;

        if (checklock($wiki_id)) {
            $this->error("Page $wiki_id is already locked by another user");
            exit(1);
        }

        lock($wiki_id);

        if (checklock($wiki_id)) {
            $this->error("Unable to obtain lock for $wiki_id ");
            var_dump(checklock($wiki_id));
            exit(1);
        }
    }

    /**
     * Clear the lock on the given page
     *
     * @param string $wiki_id
     */
    protected function clearLock($wiki_id)
    {
        if ($this->force) $this->deleteLock($wiki_id);

        $_SERVER['REMOTE_USER'] = $this->username;
        if (checklock($wiki_id)) {
            $this->error("Page $wiki_id is locked by another user");
            exit(1);
        }

        unlock($wiki_id);

        if (file_exists(wikiLockFN($wiki_id))) {
            $this->error("Unable to clear lock for $wiki_id");
            exit(1);
        }
    }

    /**
     * Forcefully remove a lock on the page given
     *
     * @param string $wiki_id
     */
    protected function deleteLock($wiki_id)
    {
        $wikiLockFN = wikiLockFN($wiki_id);

        if (file_exists($wikiLockFN)) {
            if (!unlink($wikiLockFN)) {
                $this->error("Unable to delete $wikiLockFN");
                exit(1);
            }
        }
    }
}

// Main
$cli = new PageCLI();
$cli->run();
