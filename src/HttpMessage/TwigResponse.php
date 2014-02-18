<?php
namespace Panadas\TwigPlugin\HttpMessage;

use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;
use Panadas\Framework\ApplicationAwareTrait;
use Panadas\HttpMessage\HtmlResponse;
use Panadas\HttpMessage\DataStructure\Cookies;
use Panadas\HttpMessage\DataStructure\Headers;
use Panadas\TwigPlugin\DataStructure\TemplateParams;

class TwigResponse extends HtmlResponse implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    private $params;

    public function __construct(
        Application $application,
        $charset = null,
        Headers $headers = null,
        Cookies $cookies = null,
        TemplateParams $params = null
    ) {
        parent::__construct($charset, $headers, $cookies);

        if (null === $params) {
            $params = new TemplateParams();
        }

        $this
            ->setApplication($application)
            ->setParams($params);
    }

    protected function getParams()
    {
        return $this->params;
    }

    protected function setParams(TemplateParams $params)
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
