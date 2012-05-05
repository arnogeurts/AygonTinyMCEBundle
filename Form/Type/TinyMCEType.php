<?php

namespace Aygon\TinyMCEBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Aygon\TinyMCEBundle\Form\DataTransformer\HtmlTransformer;

class TinyMCEType extends AbstractType
{
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $options = $this->getDefaultOptions(array());
        $builder->setAttribute('configs', $options['configs']);
        
        $transformer = new HtmlTransformer();
        $builder->appendClientTransformer($transformer);
    }
    
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('configs', $form->getAttribute('configs'));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'configs'   => $this->config
        );
    }

    public function getParent(array $options)
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'tinymce';
    }
}