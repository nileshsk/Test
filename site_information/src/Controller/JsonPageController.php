<?php


namespace Drupal\site_information\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonPageController extends ControllerBase
{
    protected $configFactory;

    /**
     * JsonPageController constructor.
     * @param ConfigFactoryInterface $configFactory
     */
    public function __construct(ConfigFactoryInterface $configFactory) {
        $this->configFactory = $configFactory;
    }

    /**
     * @param ContainerInterface $container
     * @return ControllerBase|JsonPageController
     */
    public static function create(ContainerInterface $container) {
        $configFactory = $container->get('config.factory');

        return new static($configFactory);
    }

    /**
     * @param $type
     * @param $nid
     * @return JsonResponse
     */
    public function JsonPage($type, $nid) {
        $node = \Drupal\node\Entity\Node::load($nid);

        if ($type != $node->getType() || !$this->checkSiteApiKey()) {
            return new JsonResponse('{
                "error": {
                    "code": 403,
                    "message": "Access Denied"
                }
            }', 403, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($node->get('body')->value, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @return bool
     */
    public function checkSiteApiKey() {
        $site_config = $this->configFactory->get('site_information.site');
        $siteapikey = $site_config->get('siteapikey');
        if ($siteapikey != "No API Key yet" && isset($siteapikey)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}