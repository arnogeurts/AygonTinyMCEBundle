<?php

namespace Aygon\TinyMCEBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HtmlTransformer implements DataTransformerInterface
{
    /**
     * Allowed tags, with allowed attributes
     * @var array
     */
    protected $allowed = array(
        'p'         => array(),
        'a'         => array('href', 'title'),
        'br'        => array(),
        'strong'    => array(),
        'em'        => array(),
        'u'         => array(),
        'ul'        => array(),
        'ol'        => array(),
        'li'        => array(),
        'img'       => array('src', 'title', 'alt'),
    );
    
    /**
     * Tags to replace
     * can be based on style using '>' [style]
     * @var array 
     */
    protected $replace = array(
        'b'                                 => 'strong',
        'i'                                 => 'em',
        'span > font-style:italic;'         => 'em',
        'span > font-weight:bold;'          => 'strong',
        'span > text-decoration:underline;' => 'span > text-decoration:underline;',
    );
    
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  string $html
     * @return string
     */
    public function transform($html)
    {
        if(strlen($html) < 1) {
            return '';
        }
        
        $dom = new \DOMDocument('1.1', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        
        $current = libxml_use_internal_errors(true);
        $parsed = @$dom->loadHTML($html);
        libxml_use_internal_errors($current);
        
        if($parsed) {
            $newDom = new \DOMDocument('1.1', 'UTF-8');
            $newDom->validateOnParse = true;
            $newDom->preserveWhiteSpace = false;
            
            $this->walkChildNodes($dom->getElementsByTagName('body')->item(0)->childNodes, $newDom, $newDom);
        
            return $newDom->saveHTML();
        } else {
            return $html;
        }
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $html
     * @return string
     */
    public function reverseTransform($html)
    {
        return $this->transform($html);
    }
    
    /**
     * Walk recursively through elements
     * 
     * @param \DOMNode $element
     * @param \DOMDocument $dom
     * @return \DOMNode
     */
    private function walk(\DOMNode $element, \DOMDocument $dom)
    {
        if($element instanceof \DOMText) {
            return $this->parseText($element, $dom);
        }
        
        if($element instanceof \DOMElement) {
            return $this->parseElement($element, $dom);
        }
        
        return null;
    }
    
    /**
     * Parse DOM text node
     * 
     * @param \DOMText $element
     * @param \DOMDocument $dom
     * @return \DOMText
     */
    private function parseText(\DOMText $element, \DOMDocument $dom)
    {
        if (strlen($element->wholeText) < 1) {
            return null;
        }
        
        return $dom->createTextNode($element->wholeText);
    }
    
    /**
     * Parse DOM element node
     * 
     * @param \DOMElement $element
     * @param \DOMDocument $dom
     * @return \DOMElement
     */
    private function parseElement(\DOMElement $element, \DOMDocument $dom)
    {        
        $newElement = $this->createElement($element, $dom);

        if ($newElement === null) {
            return $element->childNodes;
        }

        $this->setAttributes($newElement, $element);
        
        if($this->walkChildNodes($element->childNodes, $newElement, $dom)) {
            return $newElement; 
        }
        
        return null;
    }
    
    /**
     * Append node to element
     * 
     * @param \DOMNode $element
     * @param \DOMNodeList|\DOMNode $node 
     */
    private function walkChildNodes(\DOMNodeList $list, \DOMNode $element, $dom) 
    {
        $return = false;
        
        foreach ($list as $child) {
            $node = $this->walk($child, $dom);
            
            if ($node instanceof \DOMNode) {
                $element->appendChild($node);
                $return = true;
            } elseif ($node instanceof \DOMNodeList) {
                if($this->walkChildNodes($node, $element, $dom)) {
                    $return = true;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Get element tag based on replacable tags
     * 
     */
    private function createElement(\DOMElement $element, \DOMDocument $dom)
    {
        if (in_array($element->tagName, array_keys($this->allowed))) {
            return $dom->createElement($element->tagName);
        }
        
        foreach ($this->replace as $find => $replace) {
            $exp = explode(' > ', $find);
            $tag = $exp[0];
            $styles = sizeof($exp) > 1 ? array_filter(explode(',', $exp[1])) : array();
            
            if ($element->tagName == $tag) {
                $elmStyle = $element->getAttribute('style');
                $match = true;
                
                
                foreach ($styles as $style) {
                    $regex = '/' . str_replace(':', '\s*\:\s*', $style) . '/';

                    if ( ! preg_match($regex, $elmStyle)) {
                        $match = false;
                        break;
                    }
                }
                
                if ($match) {
                    $exp = explode(' > ', $find);
                    $newTag = $exp[0];
                    $newStyle = sizeof($exp) > 1 ? $exp[1] : null;
                    $element = $dom->createElement($newTag);
                    $element->setAttribute('style', $newStyle);
                    return $element;
                }
            }
        }
        
        
        return null;
    }
    
    /**
     * Set right attributes for new element
     * 
     * @param \DOMElement $new
     * @param \DOMElement $old 
     * @return void
     */
    private function setAttributes(\DOMElement $new, \DOMElement $old)
    {
        if (!in_array($new->tagName, $this->allowed)) {
            return;
        }
        
        foreach ($this->allowed[$new->tagName] as $attribute) {
            $value = $old->getAttribute($attribute);
            if(strlen($value) > 0) {
                $new->setAttribute($attribute, $value);
            }
        }
    }
}