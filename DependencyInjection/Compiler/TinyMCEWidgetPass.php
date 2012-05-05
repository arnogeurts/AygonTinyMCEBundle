<?php


namespace Aygon\TinyMCEBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add a new twig.form.resources
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class TinyMCEWidgetPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // add this bundle to form resources
        $resources = $container->getParameter('twig.form.resources');
        $resources[] = 'AygonTinyMCEBundle:Form:tinymce_layout.html.twig';
        $container->setParameter('twig.form.resources', $resources);
    }
}