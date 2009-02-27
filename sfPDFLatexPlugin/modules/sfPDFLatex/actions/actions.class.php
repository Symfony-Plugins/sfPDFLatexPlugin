<?php


class sfPDFLatexActions extends sfActions
{

  /**
   * Hello world test
   */
 public function executeTest()
  {
  	$this->getResponse()->addHttpMeta('Content-Disposition', 'attachment; filename="test.pdf"');
  	
  } 
}
