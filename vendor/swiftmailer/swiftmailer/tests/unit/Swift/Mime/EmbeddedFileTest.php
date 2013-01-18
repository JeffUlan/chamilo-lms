<?php

require_once 'Swift/Mime/EmbeddedFile.php';
require_once 'Swift/Mime/AttachmentTest.php';
require_once 'Swift/Mime/Grammar.php';

class Swift_Mime_EmbeddedFileTest extends Swift_Mime_AttachmentTest
{
  
  public function testNestingLevelIsAttachment()
  { //Overridden
  }
  
  public function testNestingLevelIsEmbedded()
  {
    $file = $this->_createEmbeddedFile($this->_createHeaderSet(),
      $this->_createEncoder(), $this->_createCache()
      );
    $this->assertEqual(
      Swift_Mime_MimeEntity::LEVEL_RELATED, $file->getNestingLevel()
      );
  }
  
  public function testIdIsAutoGenerated()
  {
    $headers = $this->_createHeaderSet(array(), false);
    $this->_checking(Expectations::create()
      -> one($headers)->addIdHeader('Content-ID', pattern('/^.*?@.*?$/D'))
      -> ignoring($headers)
      );
    $file = $this->_createEmbeddedFile($headers, $this->_createEncoder(),
      $this->_createCache()
      );
  }
  
  public function testDefaultDispositionIsAttachment()
  { //Overridden
  }
  
  public function testDefaultDispositionIsInline()
  {
    $headers = $this->_createHeaderSet(array(), false);
    $this->_checking(Expectations::create()
      -> one($headers)->addParameterizedHeader('Content-Disposition', 'inline')
      -> ignoring($headers)
      );
    $file = $this->_createEmbeddedFile($headers, $this->_createEncoder(),
      $this->_createCache()
      );
  }
  
  // -- Private helpers
  
  protected function _createAttachment($headers, $encoder, $cache,
    $mimeTypes = array())
  {
    return $this->_createEmbeddedFile($headers, $encoder, $cache, $mimeTypes);
  }
  
  private function _createEmbeddedFile($headers, $encoder, $cache)
  {
    return new Swift_Mime_EmbeddedFile($headers, $encoder, $cache, new Swift_Mime_Grammar());
  }
  
}
