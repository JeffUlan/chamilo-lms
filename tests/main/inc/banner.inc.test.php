<?php

class TestBanner extends UnitTestCase{

	public function TestBanner(){
		$this->UnitTestCase('Banners library - main/inc/banner.inc.test.php');
	}
	public function testGetTabs(){
		global $_course, $_user;
		ob_start();
    	require_once(api_get_path(SYS_CODE_PATH).'inc/banner.inc.php');
    	ob_end_clean();
        $res = get_tabs();
		$this->assertTrue(is_array($res));
		$this->assertTrue($res);
		//var_dump($res);
	}

}
?>
