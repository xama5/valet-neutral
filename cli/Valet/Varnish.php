<?php

namespace Valet;

class Varnish extends AbstractService
{
    var $brew;
    var $cli;
    var $files;
    var $site;

    /**
     * Create a new instance.
     *
     * @param  Brew          $brew
     * @param  CommandLine   $cli
     * @param  Filesystem    $files
     * @param  Configuration $configuration
     * @param  Site          $site
     */
    function __construct(
        Brew $brew,
        CommandLine $cli,
        Filesystem $files,
        Configuration $configuration,
        Site $site
    ) {
        $this->cli   = $cli;
        $this->brew  = $brew;
        $this->site  = $site;
        $this->files = $files;
        parent::__construct($configuration);
    }

    /**
     * Install the service.
     *
     * @return void
     */
    function install()
    {
        if ($this->installed()) {
            info('[varnish] already installed');
        } else {
            $this->brew->installOrFail('varnish');
            if (PHP_OS === 'Darwin') {
                $this->cli->quietly('sudo brew services stop varnish');
            }
        }
        $this->setEnabled(self::STATE_ENABLED);
        $this->restart();
    }

    /**
     * Returns wether varnish is installed or not.
     *
     * @return bool
     */
    function installed()
    {
        return $this->brew->installed('varnish');
    }

    /**
     * Restart the service.
     *
     * @return void
     */
    function restart()
    {
        if (!$this->installed() || !$this->isEnabled()) {
            return;
        }

        info('[varnish] Restarting');
        $this->cli->quietlyAsUser('brew services restart varnish');
    }

    /**
     * Stop the service.
     *
     * @return void
     */
    function stop()
    {
        if (!$this->installed()) {
            return;
        }

        info('[varnish] Stopping');
        $this->cli->quietlyAsUser('brew services stop varnish');
    }

    /**
     * Prepare for uninstallation.
     *
     * @return void
     */
    function uninstall()
    {
        $this->stop();
    }
}
