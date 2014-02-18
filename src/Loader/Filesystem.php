<?php
namespace Panadas\TwigPlugin\Loader;

use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;
use Panadas\Framework\ApplicationAwareTrait;
use Panadas\TwigPlugin\Environment;

class Filesystem extends \Twig_Loader_Filesystem implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function __construct(Application $application)
    {
        parent::__construct();

        $this
            ->setApplication($application)
            ->setPaths($application->getAbsolutePath(Environment::DIR_TEMPLATES));
    }
}
