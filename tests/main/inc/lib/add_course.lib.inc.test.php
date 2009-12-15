<?php
require_once(api_get_path(LIBRARY_PATH).'add_course.lib.inc.php');
require_once(api_get_path(LIBRARY_PATH).'debug.lib.inc.php');
require_once(api_get_path(LIBRARY_PATH).'course.lib.php');

class TestAddCourse extends UnitTestCase {

	function TestAddCourse() {
        $this->UnitTestCase('Courses creation tests');
    }
	

    function TestCreateCourse(){
		global $_configuration;
		$course_datos = array(
				'wanted_code'=> 'COD16',
				'title'=>'prueba01',
				'tutor_name'=>'R. J. Wolfagan',
				'category_code'=>'2121',
				'course_language'=>'english',
				'course_admin_id'=>'1211',
				'db_prefix'=> $_configuration['db_prefix'],
				'firstExpirationDelay'=>'112'
				);
		$res = create_course($course_datos['wanted_code'], $course_datos['title'],
							 $course_datos['tutor_name'], $course_datos['category_code'],
							 $course_datos['course_language'],$course_datos['course_admin_id'],
							 $course_datos['db_prefix'], $course_datos['firstExpirationDelay']);
		$this->assertTrue(is_bool($res));
		
	}
   
    function testRegisterCourse() {
	    global $_configuration;
         $course = array(
		    'courseSysCode'=> 'COD16',
		    'courseScreenCode' =>'COD16',
		    'courseRepository' =>'C16',
		    'courseDbName' => $_configuration['db_prefix'].'ARITM',
		    'titular' =>'R. Wofgar',
		    'category' =>'Math',
		    'title' =>'metodologia de calculo diferencial',
		    'course_language' =>'English',
		    'uidCreator'=> '212',
			);
	    $res = register_course($course['courseSysCode'],$course['courseScreenCode'],
	 					   $course['courseRepository'],$course['courseDbName'],
	 					   $course['titular'],$course['category'],$course['title'],
	 					   $course['course_language'],$course['uidCreator'],
	 					   null,null
	 					   );
	   
	    $this->assertTrue($res === 0);
	    $res = CourseManager::delete_course($course['courseSysCode']);
    	
    }
     
    function TestGenerateCourseCode(){
    	global $charset;
    	$course_title = 'matemáticas';
    	$res = generate_course_code($course_title);
    	$this->assertTrue($res);
	}


	function TestDefineCourseKeys(){
		global $prefixAntiNumber, $_configuration;
		$wantedCode = generate_course_code($wantedCode);
		$res = define_course_keys(generate_course_code($wantedCode), null, null, null,null, null);
		$this->assertTrue($res);
	}
	
	public function TestDeleteCourse(){				
		$code = 'COD16';				
		$res = CourseManager::delete_course($code);			
		$path = api_get_path(SYS_PATH).'archive';		
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {				
				if (strpos($file,$code)!==false) {										
					if (is_dir($path.'/'.$file)) {						
						rmdirr($path.'/'.$file);						
					}				
				}				
			}
			closedir($handle);
		}
	}
	
/*  // 26 excepciones
	function TestPrepareCourseRepository(){ 
		umask(0);
		$perm = '0777';
		$courseRepository = 'C16';
		$courseId = 'COD16';
		$res = prepare_course_repository($courseRepository, $courseId);
		$res1 = CourseManager::delete_course('C16');
		
		
		
		
		$this->assertTrue($res===0);
	} 
// Problemas con este archivo - falta analizar esta función
	function TestUpdateDbCourse(){
		global $_configuration;
		$dbcourse = array('courseDbName'=> 'COD16');
		$res = update_Db_course($dbcourse['courseDbName']);
		$this->assertTrue($res===0);
		//var_dump($res);
	}
*/

	function TestBrowseFolders(){
		$browse = array('path'=>'','file'=>'','media'=>'');
		$res = browse_folders($browse['path'], $browse['files'],$browse['media']);
		$this->assertFalse($res);
	}
	/*
	// 1 excepcion
	function TestSortPictures(){
		$picture = array('files'=>'science.jpg', 'type'=>'jpg');
		$res = sort_pictures($picture['file'],$picture['type']);
		$this->assertTrue(is_array($res));
		//var_dump($res);
	}
	*/
	/*
	function TestFillCourseRepository(){
		$courseRepository = 'COD16';
		$res = fill_course_repository($courseRepository);
		$this->assertTrue($res);
	}
*/
	function TestLang2db(){
		$string = 'dsdzxcwqd';
		$res = lang2db($string);
		$this->assertTrue($res);
	}

	function TestFillDbCourse(){
		global $_configuration, $clarolineRepositoryWeb, $_user;
		$courseDbName = $_configuration['table_prefix'].$courseDbName.$_configuration['db_glue'];
		$courseRepository = (api_get_path(SYS_COURSE_PATH).$courseRepository . "/dropbox/.htaccess");
		$language = 'english';
		$language_interface = $language;
		$default_document_array = array();
		$sys_course_path = api_get_path(SYS_COURSE_PATH);
		$courseDbName = $_configuration['db_prefix'].$courseDbName.$_configuration['db_glue'];
		$courseRepository = 'sdffsdf';
		$language = 'english';
		$default_document_array ='1212121';
		$res = fill_Db_course($courseDbName, $courseRepository, $language,array());
		$this->assertTrue($res === 0);
	}
	

	
	function TestString2Binary(){
		$variable = true;
		$res = string2binary($variable);
		$this->assertTrue($res);
	}

	function TestCheckArchive(){
		$pathToArchive ='';
		$res = checkArchive($pathToArchive);
		$this->assertTrue($res === TRUE);
	}
	
	
/*	 // 1 excepcion de permisos
	function TestReadPropertiesInArchive(){
		ob_start();
		$archive='/archive.ini';
		$uid = api_get_user_id();
		printVar(dirname($archive), "Zip : ");
		$perm = '777';
		$res = readPropertiesInArchive($archive, $isCompressed = TRUE);
		if(is_array($res)){
	 		$this->assertTrue(is_array($res));
	 	}else{ 
	 		$this->assertNull($res);
	 	}
	 	ob_end_clean();
		//var_dump($res);
		}
*/
}
