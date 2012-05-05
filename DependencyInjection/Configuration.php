<?php

namespace Aygon\TinyMCEBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aygon_tiny_mce');
           
        $config = array(
            'script_url'                        => '/bundles/aygontinymce/js/tiny_mce/tiny_mce.js',
            'theme'                             => 'advanced',
            'plugins'                           => 'media,searchreplace,paste',
            'theme_advanced_buttons1'           => 'bold,italic,underline,strikethrough,|,link,unlink,image,code,|,bullist,numlist',
            'theme_advanced_buttons2'           => 'undo,redo,|,search,replace,|,cut,copy,paste,pastetext,pasteword',
            'theme_advanced_buttons3'           => '',
            'theme_advanced_buttons4'           => '',
            'theme_advanced_toolbar_location'   => 'top',
            'theme_advanced_toolbar_align'      => 'left',
            'theme_advanced_statusbar_location' => 'bottom',
        );
        
        $rootNode
            ->children()
                ->scalarNode('script_url')->defaultValue('/bundles/aygontinymce/js/tiny_mce/jquery.tinymce.js')->cannotBeEmpty()->end()
                ->variableNode('config')->defaultValue($config)->end()
            ->end();

        return $treeBuilder;
    }
}
