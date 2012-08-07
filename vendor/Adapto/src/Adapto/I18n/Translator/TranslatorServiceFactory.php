<?php

namespace Adapto\I18n\Translator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator.
 *
 * @category   Adapto
 * @package    Adapto_I18n
 * @subpackage Translator
 */
class TranslatorServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Configure the translator
        $config = $serviceLocator->get('Configuration');
        $trConfig = isset($config['translator']) ? $config['translator'] : array();
        $translator = Translator::factory($trConfig);
        return $translator;
    }
}
