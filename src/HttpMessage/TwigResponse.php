<?php
namespace Panadas\TwigPlugin\HttpMessage;

use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;
use Panadas\Framework\ApplicationAwareTrait;
use Panadas\HttpMessageModule\HtmlResponse;
use Panadas\HttpMessageModule\DataStructure\Cookies;
use Panadas\HttpMessageModule\DataStructure\Headers;
use Panadas\TwigPlugin\DataStructure\TwigResponseParams;

class TwigResponse extends HtmlResponse implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    private $params;

    public function __construct(
        $charset = null,
        Headers $headers = null,
        Cookies $cookies = null,
        TwigResponseParams $params = null
    ) {
        parent::__construct($headers, $content, $charset);

        if (null === $params) {
            $params = new TwigResponseParams();
        }

        $this
            ->setApplication($application)
            ->setParams($params);
    }

    protected function getParams()
    {
        return $this->params;
    }

    protected function setParams(TwigResponseParams $params)
    {
        $this->params = $params;

        return $this;
    }

    protected function getGlobals()
    {
        $application = $this->getApplication();

        $statusCode = $this->getStatusCode();

        return [
             "_application" => [
                "name" => $application->getName(),
                "environment" => $application->getEnvironment(),
                "debugMode" => $application->isDebugMode()
            ],
            "_response" => [
                "status" => [
                    "code" => $statusCode,
                    "message" => static::getStatusMessage($statusCode)
                ]
            ]
        ];

    }

    public function render($template)
    {
        $environment = $this->getApplication()->getServices()->get("twig");
        $environment->setCharset($this->getCharset());

        foreach ($this->getGlobals() as $name => $value) {
            $environment->addGlobal($name, $value);
        }

        return $this->setContent($environment->render($template, $this->getParams()->all()));
    }
}
