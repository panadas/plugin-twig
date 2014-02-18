<?php
namespace Panadas\TwigPlugin;

use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;
use Panadas\Framework\ApplicationAwareTrait;

class Environment extends \Twig_Environment implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    const DIR_TEMPLATES = "views";
    const DIR_CACHE = "var/cache/twig";

    public function __construct(
        Application $application,
        \Twig_LoaderInterface $loader = null,
        array $options = []
    ) {
        if (!array_key_exists("debug", $options)) {
            $options["debug"] = $application->isDebugMode();
        }

        if (!array_key_exists("cache", $options)) {
            if (!$options["debug"]) {
                $options["cache"] = $application->getAbsolutePath(static::DIR_CACHE);
            } else {
                $options["cache"] = false;
            }
        }

        parent::__construct($loader, $options);

        if ($options["debug"]) {
            $this->addExtension(new \Twig_Extension_Debug());
        }

        $this->addFunction(new \Twig_SimpleFunction("route", [$this, "handleRoute"]));

        $this->setApplication($application);
    }

    protected function handleRoute($name, array $placeholders = [])
    {
        return $this->getApplication()->getServices()->get("router")->get($name, $placeholders);
    }
}
