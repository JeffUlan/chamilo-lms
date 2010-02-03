<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: reporter.php 1702 2008-03-25 00:08:04Z lastcraft $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/scorer.php');
/**#@-*/

/**
 *    Sample minimal test displayer. Generates only
 *    failure messages and a pass count.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class HtmlReporter extends SimpleReporter {
    var $_character_set;

    /**
     *    Does nothing yet. The first output will
     *    be sent on the first test start. For use
     *    by a web browser.
     *    @access public
     */
    function HtmlReporter($character_set = 'ISO-8859-1') {
        $this->SimpleReporter();
        $this->_character_set = $character_set;
    }

    /**
     *    Paints the top of the web page setting the
     *    title to the name of the starting test.
     *    @param string $test_name      Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
        $this->sendNoCacheHeaders();
        print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
        echo '<html style="height:100%;">';
        echo '<head>';
        echo '<title>Test Suite</title>';
        echo '</head>';
       	echo '<body style="padding:0px;margin:0px;height:100%;">';
       	echo '<div id="wrapper" style="width:90%;padding-left:5%;padding-bottom:10%;">';//wrapper
       	echo '<div id="header1">';//header1
        echo '<div id="header3" style="min-height:100px;background:url(img/header-logo.png) no-repeat left;"><br/>';//header3
        echo '<div id="header4" style="float:right;font-size:25px;color:#01933E;"><br/>';//header4
        echo ''. date('Y - m - d').'';
        echo '</div>';//fin header 4
        echo '</div><br/>';//fin header 3
        echo '</div>';//fin header 1
        echo '<div id="header2" style="width:100%;border:2px solid #DDDDDD;">';//header2     
        echo '<div id="content2" style="background-image:url(img/bg-header4.png);background-repeat:repeat-x";>
			<h2><div style="font-size:18px;font-family:Tahoma,Geneva,sans-serif;color:#01933E;text-decoration:none;">These results showing how many functions have been tested in the code of chamilo 1.8.6.2</div></h2><br/>         		    
				<div style="font-size:12px;font-family:Tahoma,Geneva,sans-serif;color:#01933E;">
				<p>This page showing the different types results than have been tested and showing the lines the problems.</p>  
				<ol><li>This results show the amount of the tests than have been tested.</li>
				<li>This results show the amount of the tests than have passed.</li>
				<li>This results show the amount of the tests than have failed.</li>
				<li>This results show the amount of exceptions in the tests.</li></ol>
				<p>You can always find more information about simpletest on the website: http://www.lastcraft.com </p>
				<p>"Only a well software like Chamilo, is perfect when has been tested by excellent Testing Engineers".</p>
				</div></div>';//content2    			
		echo '</div><br/>';//fin header2
		//flush();
    }
    
    /**
     *    Send the headers necessary to ensure the page is
     *    reloaded on every request. Otherwise you could be
     *    scratching your head over out of date test data.
     *    @access public
     *    @static
     */
    function sendNoCacheHeaders() {
        if (! headers_sent()) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
    }

    /**
     *    Paints the CSS. Add additional styles here.
     *    @return string            CSS code as text.
     *    @access protected
     */
    function _getCss() {
        return ".fail { background-color: inherit; color: red; }" .
                ".pass { background-color: inherit; color: green; }" .
                " pre { background-color: lightgray; color: inherit; }";
                
    }

    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : "green");
        echo '<div id="header5" style="background-color: '.$colour.';color: white;font-weight:bold;">';//header 5
        print "<style type=\"text/css\">\n";
        print $this->_getCss() . "\n";
        print "</style>\n";
        echo ' Test cases complete: ';
        echo $this->getTestCaseProgress() . '/' . $this->getTestCaseCount().'<br/><br/>';
        echo  'Passes: ' . $this->getPassCount() . '  ';
        echo  'Fails: ' . $this->getFailCount() . '  ';
        echo  'Exceptions: ' . $this->getExceptionCount() . ' ';
 		echo '</div>';//fin header5
 		echo '</div>';//fin wrapper
 		echo '<div id="footer1" style= "height:6em;background-image:url(img/bg-footer.gif);background-repeat:repeat-x ">';
        echo '</div>';//fin footer 1
        echo '</body>';
        echo '</html>';		        
    }
	
	

    /**
     *    Paints the test failure with a breadcrumbs
     *    trail of the nesting test suites below the
     *    top level test.
     *    @param string $message    Failure message displayed in
     *                              the context of the other tests.
     *    @access public
     */
    function paintFail($message) {
        parent::paintFail($message);
        print "<span class=\"fail\">Fail</span>: ";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print implode(" -&gt; ", $breadcrumb);
        print " -&gt; " . $this->_htmlEntities($message) . "<br />\n";
    }

    /**
     *    Paints a PHP error.
     *    @param string $message        Message is ignored.
     *    @access public
     */
    function paintError($message) {
        parent::paintError($message);
        print "<span class=\"fail\">Exception</span>: ";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print implode(" -&gt; ", $breadcrumb);
        print " -&gt; <strong>" . $this->_htmlEntities($message) . "</strong><br />\n";
    }

    /**
     *    Paints a PHP exception.
     *    @param Exception $exception        Exception to display.
     *    @access public
     */
    function paintException($exception) {
        parent::paintException($exception);
        print "<span class=\"fail\">Exception</span>: ";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print implode(" -&gt; ", $breadcrumb);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        print " -&gt; <strong>" . $this->_htmlEntities($message) . "</strong><br />\n";
    }

    /**
     *    Prints the message for skipping tests.
     *    @param string $message    Text of skip condition.
     *    @access public
     */
    function paintSkip($message) {
        parent::paintSkip($message);
        print "<span class=\"pass\">Skipped</span>: ";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print implode(" -&gt; ", $breadcrumb);
        print " -&gt; " . $this->_htmlEntities($message) . "<br />\n";
    }

    /**
     *    Paints formatted text such as dumped variables.
     *    @param string $message        Text to show.
     *    @access public
     */
    function paintFormattedMessage($message) {
        print '<pre>' . $this->_htmlEntities($message) . '</pre>';
    }

    /**
     *    Character set adjusted entity conversion.
     *    @param string $message    Plain text or Unicode message.
     *    @return string            Browser readable message.
     *    @access protected
     */
    function _htmlEntities($message) {
        return htmlentities($message, ENT_COMPAT, $this->_character_set);
    }
}

/**
 *    Sample minimal test displayer. Generates only
 *    failure messages and a pass count. For command
 *    line use. I've tried to make it look like JUnit,
 *    but I wanted to output the errors as they arrived
 *    which meant dropping the dots.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class TextReporter extends SimpleReporter {

    /**
     *    Does nothing yet. The first output will
     *    be sent on the first test start.
     *    @access public
     */
    function TextReporter() {
        $this->SimpleReporter();
    }

    /**
     *    Paints the title only.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
        if (! SimpleReporter::inCli()) {
            header('Content-type: text/plain');
        }
        print "$test_name\n";
        flush();
    }

    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        if ($this->getFailCount() + $this->getExceptionCount() == 0) {
            print "OK\n";
        } else {
            print "FAILURES!!!\n";
        }
        print "Test cases run: " . $this->getTestCaseProgress() .
                "/" . $this->getTestCaseCount() .
                ", Passes: " . $this->getPassCount() .
                ", Failures: " . $this->getFailCount() .
                ", Exceptions: " . $this->getExceptionCount() . "\n";
    }

    /**
     *    Paints the test failure as a stack trace.
     *    @param string $message    Failure message displayed in
     *                              the context of the other tests.
     *    @access public
     */
    function paintFail($message) {
        parent::paintFail($message);
        print $this->getFailCount() . ") $message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }

    /**
     *    Paints a PHP error or exception.
     *    @param string $message        Message to be shown.
     *    @access public
     *    @abstract
     */
    function paintError($message) {
        parent::paintError($message);
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }

    /**
     *    Paints a PHP error or exception.
     *    @param Exception $exception      Exception to describe.
     *    @access public
     *    @abstract
     */
    function paintException($exception) {
        parent::paintException($exception);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }

    /**
     *    Prints the message for skipping tests.
     *    @param string $message    Text of skip condition.
     *    @access public
     */
    function paintSkip($message) {
        parent::paintSkip($message);
        print "Skip: $message\n";
    }

    /**
     *    Paints formatted text such as dumped variables.
     *    @param string $message        Text to show.
     *    @access public
     */
    function paintFormattedMessage($message) {
        print "$message\n";
        flush();
    }
}

/**
 *    Runs just a single test group, a single case or
 *    even a single test within that case.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class SelectiveReporter extends SimpleReporterDecorator {
    var $_just_this_case = false;
    var $_just_this_test = false;
    var $_on;

    /**
     *    Selects the test case or group to be run,
     *    and optionally a specific test.
     *    @param SimpleScorer $reporter    Reporter to receive events.
     *    @param string $just_this_case    Only this case or group will run.
     *    @param string $just_this_test    Only this test method will run.
     */
    function SelectiveReporter(&$reporter, $just_this_case = false, $just_this_test = false) {
        if (isset($just_this_case) && $just_this_case) {
            $this->_just_this_case = strtolower($just_this_case);
            $this->_off();
        } else {
            $this->_on();
        }
        if (isset($just_this_test) && $just_this_test) {
            $this->_just_this_test = strtolower($just_this_test);
        }
        $this->SimpleReporterDecorator($reporter);
    }

    /**
     *    Compares criteria to actual the case/group name.
     *    @param string $test_case    The incoming test.
     *    @return boolean             True if matched.
     *    @access protected
     */
    function _matchesTestCase($test_case) {
        return $this->_just_this_case == strtolower($test_case);
    }

    /**
     *    Compares criteria to actual the test name. If no
     *    name was specified at the beginning, then all tests
     *    can run.
     *    @param string $method       The incoming test method.
     *    @return boolean             True if matched.
     *    @access protected
     */
    function _shouldRunTest($test_case, $method) {
        if ($this->_isOn() || $this->_matchesTestCase($test_case)) {
            if ($this->_just_this_test) {
                return $this->_just_this_test == strtolower($method);
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     *    Switch on testing for the group or subgroup.
     *    @access private
     */
    function _on() {
        $this->_on = true;
    }

    /**
     *    Switch off testing for the group or subgroup.
     *    @access private
     */
    function _off() {
        $this->_on = false;
    }

    /**
     *    Is this group actually being tested?
     *    @return boolean     True if the current test group is active.
     *    @access private
     */
    function _isOn() {
        return $this->_on;
    }

    /**
     *    Veto everything that doesn't match the method wanted.
     *    @param string $test_case       Name of test case.
     *    @param string $method          Name of test method.
     *    @return boolean                True if test should be run.
     *    @access public
     */
    function shouldInvoke($test_case, $method) {
        if ($this->_shouldRunTest($test_case, $method)) {
            return $this->_reporter->shouldInvoke($test_case, $method);
        }
        return false;
    }

    /**
     *    Paints the start of a group test.
     *    @param string $test_case     Name of test or other label.
     *    @param integer $size         Number of test cases starting.
     *    @access public
     */
    function paintGroupStart($test_case, $size) {
        if ($this->_just_this_case && $this->_matchesTestCase($test_case)) {
            $this->_on();
        }
        $this->_reporter->paintGroupStart($test_case, $size);
    }

    /**
     *    Paints the end of a group test.
     *    @param string $test_case     Name of test or other label.
     *    @access public
     */
    function paintGroupEnd($test_case) {
        $this->_reporter->paintGroupEnd($test_case);
        if ($this->_just_this_case && $this->_matchesTestCase($test_case)) {
            $this->_off();
        }
    }
}

/**
 *    Suppresses skip messages.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NoSkipsReporter extends SimpleReporterDecorator {

    /**
     *    Does nothing.
     *    @param string $message    Text of skip condition.
     *    @access public
     */
    function paintSkip($message) { }
}
?>