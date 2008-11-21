<?php
/*
 * This file is part of the isicsWidgetFormTinyMCEPlugin package.
 * Copyright (c) 2008 ISICS.fr <contact@isics.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * TinyMCE Widget
 *
 * @package isicsWidgetFormTinyMCEPlugin
 * @author Nicolas CHARLOT <nicolas.charlot@isics.fr>
 **/
class isicsWidgetFormTinyMCE extends sfWidgetFormTextarea
{
  
  /**
   * Constructor.
   *
   * Available option:
   *
   *  * tiny_options: Associative array of Tiny MCE options (empty array by default)
   *  * with_gzip   : Enables GZip compression (false by default)
   *
   * @see sfWidgetFormTextarea
   **/    
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('tiny_options', sfConfig::get('app_tiny_mce_default', array()));
    $this->addOption('with_gzip', false);   
  }
  
  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @see sfWidget
   **/    
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $javascript = 'tiny_mce/tiny_mce'.($this->getOption('with_gzip') ? '_gzip' : '');
    sfContext::getInstance()->getResponse()->addJavascript($javascript);

    $options = '';
    foreach ($this->getOption('tiny_options') as $key => $option)
    {
      $options .= ",\n    ".$key.': \''.$option.'\'';
    }
    
    $id = $this->generateId($name, $value);

    $script_gzip_content = <<<JS
//<![CDATA[
  tinyMCE_GZ.init({
    plugins : 'style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,'+ 
              'searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
    themes : 'simple,advanced',
    languages : 'fr',
    disk_cache : true,
    debug : false
  });
//]]>
JS;
    
    $script_content = <<<JS
//<![CDATA[
  tinyMCE.init({
    mode: 'exact',
    elements: '{$id}'{$options}
  });
//]]>
JS;

    return parent::render($name, $value, $attributes, $errors)
          .($this->getOption('with_gzip') ? 
             $this->renderContentTag('script', $script_gzip_content, array('type' => 'text/javascript')): '')
          .$this->renderContentTag('script', $script_content, array('type' => 'text/javascript'));
  }
}