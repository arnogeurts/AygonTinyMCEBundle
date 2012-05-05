<?php

namespace Aygon\TinyMCEBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Aygon\TinyMCEBundle\DependencyInjection\Compiler\TinyMCEWidgetPass;

class AygonTinyMCEBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TinyMCEWidgetPass());
    }
}
