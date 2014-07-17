<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Sites\Zoo\BaseBundle\ZooBaseBundle(),
            new Sites\Jardins\BaseBundle\JardinsBaseBundle(),
            new Sites\Pagodes\BaseBundle\PagodesBaseBundle(),
            new Sites\Hameaux\BaseBundle\HameauxBaseBundle(),
            new Sites\BeauvalNature\BaseBundle\BeauvalNatureBaseBundle(),
            new Sites\Hotels\BaseBundle\HotelsBaseBundle(),
            new Sites\Pro\BaseBundle\ProBaseBundle(),
            new Sites\Groupes\BaseBundle\GroupesBaseBundle(),
            new Sites\CE\BaseBundle\CEBaseBundle(),
            new Sites\Scolaires\BaseBundle\ScolairesBaseBundle(),
            new Sites\Admin\Common\BaseBundle\AdminCommonBaseBundle(),
            new Sites\Installer\BaseBundle\InstallerBaseBundle(),
            new Sites\Admin\Zoo\FototekBundle\AdminZooFototekBundle(),
            new Sites\Admin\Common\MenuManagerBundle\AdminCommonMenuManagerBundle(),
            new Sites\Admin\Zoo\BlogBundle\AdminZooBlogBundle(),
            new Sites\Admin\Common\MediatekBundle\AdminCommonMediatekBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
