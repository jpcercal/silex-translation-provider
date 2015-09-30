<?php

namespace Cekurte\Silex\Translation\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class TranslationServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        if (!isset($app['translation.directory'])) {
            throw new \RuntimeException('The translation directory parameter is not registered in this application');
        }

        if (!file_exists($app['translation.directory'])) {
            throw new \RuntimeException('The translation directory not exists');
        }

        $closure = function (Translator $translator, Application $app) use ($app) {

            $translator->addLoader('yaml', new YamlFileLoader());

            if (isset($app['locale'])) {
                $translator->setLocale($app['locale']);
            }

            if (isset($app['locale_fallbacks'])) {
                $translator->setFallbackLocales($app['locale_fallbacks']);
            }

            $iterator = new \DirectoryIterator($app['translation.directory']);

            foreach ($iterator as $item) {
                if (!$item->isDot() && $this->fileIsAllowed($item)) {
                    $translator->addResource('yaml', $item->getPathname(), $this->getLocale($item));
                }
            }

            return $translator;
        };

        $app['translator'] = $app->share($app->extend('translator', $closure));
    }

    /**
     * @param  \DirectoryIterator $currentItem
     *
     * @return bool
     */
    protected function fileIsAllowed(\DirectoryIterator $currentItem)
    {
        $allowExtensions = ['yml', 'yaml'];

        return $currentItem->isFile() && in_array($currentItem->getExtension(), $allowExtensions);
    }

    /**
     * @param  \DirectoryIterator $currentItem
     *
     * @return string
     */
    protected function getLocale(\DirectoryIterator $currentItem)
    {
        return substr($currentItem->getFilename(), 0, (strlen($currentItem->getExtension()) + 1) * -1);
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app->register(new \Silex\Provider\TranslationServiceProvider());
    }
}
