<?php

namespace Aygon\TinyMCEBundle\Twig\Extension;

class TinyMCEExtension extends \Twig_Extension
{
    /**
     * Url to TinyMCE script
     * @var string
     */
    private $script_url;
    
    /**
     * Construct TinyMCE Extension
     * 
     * @param string $script_url 
     * @return void
     */
    public function __construct($script_url)
    {
        $this->script_url = $script_url;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'tinymce'        => new \Twig_Function_Method($this, 'getTinyMCE', array('is_safe' => array('html'))),
        );
    }

    /**
     * Get script tag for TinyMCE 
     * 
     * @return string 
     */
    public function getTinyMCE()
    {
        return sprintf('<script src="%s" type="text/javascript"></script>', $this->script_url);
    }

    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'tinymce';
    }
}