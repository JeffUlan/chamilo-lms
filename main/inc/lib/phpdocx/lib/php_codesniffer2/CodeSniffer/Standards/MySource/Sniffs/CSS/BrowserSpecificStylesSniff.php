<?php
/**
 * MySource_Sniffs_CSS_BrowserSpecificStylesSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ForbiddenStylesSniff.php 268254 2008-11-04 05:08:07Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * MySource_Sniffs_CSS_BrowserSpecificStylesSniff.
 *
 * Ensure that browser-specific styles are not used.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class MySource_Sniffs_CSS_BrowserSpecificStylesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');

    /**
     * A list of specific stylsheet suffixes we allow.
     *
     * These stylsheets contain browser specific styles
     * so this sniff ignore them files in the form:
     * *_moz.css and *_ie7.css etc.
     *
     * @var array
     */
    protected $specificStylesheets = array(
                                      'moz',
                                      'ie',
                                      'ie7',
                                      'ie8',
                                      'webkit',
                                     );


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_STYLE);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // Ignore files with browser-specific suffixes.
        $filename  = $phpcsFile->getFilename();
        $breakChar = strrpos($filename, '_');
        if ($breakChar !== false && substr($filename, -4) === '.css') {
            $specific = substr($filename, ($breakChar + 1), -4);
            if (in_array($specific, $this->specificStylesheets) === true) {
                return;
            }
        }

        $tokens  = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        if ($content{0} === '-') {
            $error = 'Browser-specific styles are not allowed';
            $phpcsFile->addError($error, $stackPtr, 'ForbiddenStyle');
        }

    }//end process()


}//end class

?>