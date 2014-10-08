<?php
/* For licensing terms, see /license.txt */

/**
 * ExerciseResult class: This class allows to instantiate an object
 * of type ExerciseResult
 * which allows you to export exercises results in multiple presentation forms
 * @package chamilo.exercise
 * @author Yannick Warnier
*/
class ExerciseResult
{
	private $exercises_list = array(); //stores the list of exercises
	private $results = array(); //stores the results
    public $includeAllUsers = false;

    /**
     * @param bool $includeAllUsers
     */
    public function setIncludeAllUsers($includeAllUsers)
    {
        $this->includeAllUsers = $includeAllUsers;
    }

    /**
     * Gets the results of all students (or just one student if access is limited)
     *
     * @param string $document_path The document path (for HotPotatoes retrieval)
     * @param int $user_id User ID. Optional. If no user ID is provided, we take all the results. Defauts to null
     * @param int $filter
     * @param int $exercise_id
     * @param null $hotpotato_name
     * @return bool
     */
    public function getExercisesReporting(
        $document_path,
        $user_id = null,
        $filter = 0,
        $exercise_id = 0,
        $hotpotato_name = null
    ) {
		$return = array();

        $TBL_EXERCISES = Database::get_course_table(TABLE_QUIZ_TEST);
        $TBL_TABLE_LP_MAIN = Database::get_course_table(TABLE_LP_MAIN);

        $TBL_USER = Database::get_main_table(TABLE_MAIN_USER);
        $TBL_TRACK_EXERCISES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $TBL_TRACK_HOTPOTATOES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_HOTPOTATOES);
        $TBL_TRACK_ATTEMPT_RECORDING = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);

        $cid = api_get_course_id();
        $course_id = api_get_course_int_id();
        $user_id = intval($user_id);
        $sessionId = api_get_session_id();
        $session_id_and = ' AND te.session_id = ' . $sessionId . ' ';
        $exercise_id = intval($exercise_id);
        $hotpotato_name = Database::escape_string($hotpotato_name);

        if (!empty($exercise_id)) {
            $session_id_and .= " AND exe_exo_id = $exercise_id ";
        }

		if (empty($user_id)) {
            $user_id_and = null;
			$sql = "SELECT ".(api_is_western_name_order() ? "firstname as userpart1, lastname userpart2" : "lastname as userpart1, firstname as userpart2").",
			            official_code,
                        ce.title as extitle,
                        te.exe_result as exresult ,
                        te.exe_weighting as exweight,
                        te.exe_date as exdate,
                        te.exe_id as exid,
                        email as exemail,
                        te.start_date as exstart,
                        steps_counter as exstep,
                        exe_user_id as excruid,
                        te.exe_duration as duration,
                        te.orig_lp_id as orig_lp_id,
                        tlm.name as lp_name
                FROM $TBL_EXERCISES  AS ce
                INNER JOIN $TBL_TRACK_EXERCISES AS te ON (te.exe_exo_id = ce.id)
                INNER JOIN $TBL_USER  AS user ON (user.user_id = exe_user_id)
                LEFT JOIN $TBL_TABLE_LP_MAIN AS tlm ON tlm.id = te.orig_lp_id AND tlm.c_id = ce.c_id
                WHERE   ce.c_id = $course_id AND
                        te.status != 'incomplete' AND
                        te.exe_cours_id='" . Database :: escape_string($cid) . "'  $user_id_and  $session_id_and AND
                        ce.active <>-1";
            $hpsql="SELECT ".(api_is_western_name_order() ? "firstname as userpart1, lastname userpart2" : "lastname as userpart1, firstname as userpart2").",
                    official_code,
                    email,
                    tth.exe_name,
                    tth.exe_result,
                    tth.exe_weighting,
                    tth.exe_date,
                    tth.exe_user_id as excruid
                    FROM $TBL_TRACK_HOTPOTATOES tth, $TBL_USER tu
                    WHERE   tu.user_id=tth.exe_user_id AND
                            tth.exe_cours_id = '" . Database :: escape_string($cid) . "' AND
                            tth.exe_name = '$hotpotato_name'
                    ORDER BY tth.exe_cours_id ASC, tth.exe_date DESC";

		} else {
            $user_id_and = ' AND te.exe_user_id = ' . api_get_user_id() . ' ';
			// get only this user's results
            $sql="SELECT ".(api_is_western_name_order() ? "firstname as userpart1, lastname userpart2" : "lastname as userpart1, firstname as userpart2").",
                    official_code,
                    ce.title as extitle,
                    te.exe_result as exresult,
                    te.exe_weighting as exweight,
                    te.exe_date as exdate,
                    te.exe_id as exid,
                    email as exemail,
                    te.start_date as exstart,
                    steps_counter as exstep,
                    exe_user_id as excruid,
                    te.exe_duration as duration,
                    ce.results_disabled as exdisabled,
                    te.orig_lp_id as orig_lp_id,
                    tlm.name as lp_name
                    FROM $TBL_EXERCISES  AS ce
                    INNER JOIN $TBL_TRACK_EXERCISES AS te ON (te.exe_exo_id = ce.id)
                    INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
                    LEFT JOIN $TBL_TABLE_LP_MAIN AS tlm ON tlm.id = te.orig_lp_id AND tlm.c_id = ce.c_id
                    WHERE
                        ce.c_id = $course_id AND
                        te.status != 'incomplete' AND
                        te.exe_cours_id='" . Database :: escape_string($cid) . "'  $user_id_and $session_id_and AND
                        ce.active <>-1 AND
                    ORDER BY userpart2, te.exe_cours_id ASC, ce.title ASC, te.exe_date DESC";

            $hpsql = "SELECT '', exe_name, exe_result , exe_weighting, exe_date, exe_user_id as excruid
                        FROM $TBL_TRACK_HOTPOTATOES
                        WHERE
                            exe_user_id = '" . $user_id . "' AND
                            exe_cours_id = '" . Database :: escape_string($cid) . "' AND
                            tth.exe_name = '$hotpotato_name'
                        ORDER BY exe_cours_id ASC, exe_date DESC";
		}

		$results = array();
		$resx = Database::query($sql);
		while ($rowx = Database::fetch_array($resx,'ASSOC')) {
            $results[] = $rowx;
		}

		$hpresults = array();
		$resx = Database::query($hpsql);
	    while ($rowx = Database::fetch_array($resx,'ASSOC')) {
            $hpresults[] = $rowx;
		}

        $filter_by_not_revised = false;
        $filter_by_revised = false;

		if ($filter) {
			switch ($filter) {
				case 1 :
                    $filter_by_not_revised = true;
                    break;
				case 2 :
                    $filter_by_revised = true;
                    break;
				default :
                    null;
			}
		}

		//Print the results of tests
        $userWithResults = array();
		if (is_array($results) && empty($hotpotato_name)) {
			for ($i = 0; $i < sizeof($results); $i++) {
				$revised = false;

				//revised or not
				$sql_exe = "SELECT exe_id FROM $TBL_TRACK_ATTEMPT_RECORDING
							WHERE author != '' AND exe_id = ".Database :: escape_string($results[$i]['exid'])." LIMIT 1";
				$query = Database::query($sql_exe);

				if (Database :: num_rows($query) > 0)
                    $revised = true;

                if ($filter_by_not_revised && $revised) {
                    continue;
                }

                if ($filter_by_revised && !$revised) {
                    continue;
                }

				$return[$i] = array();

				if (empty($user_id)) {
                    $return[$i]['official_code']   = $results[$i]['official_code'];
					$return[$i]['first_name']   = $results[$i]['userpart1'];
					$return[$i]['last_name']    = $results[$i]['userpart2'];
					$return[$i]['user_id']      = $results[$i]['excruid'];
					$return[$i]['email']        = $results[$i]['exemail'];
				}
				$return[$i]['title'] = $results[$i]['extitle'];
				$return[$i]['start_date'] = api_get_local_time($results[$i]['exstart']);
                $return[$i]['end_date'] = api_get_local_time($results[$i]['exdate']);
                $return[$i]['duration'] = $results[$i]['duration'];
				$return[$i]['result'] = $results[$i]['exresult'];
				$return[$i]['max'] = $results[$i]['exweight'];
                $return[$i]['status'] = $revised ? get_lang('Validated') : get_lang('NotValidated');
                $return[$i]['lp_id'] = $results[$i]['orig_lp_id'];
                $return[$i]['lp_name'] = $results[$i]['lp_name'];

                $userWithResults[$results[$i]['excruid']] = 1;
			}
		}

        $isHotPotato = false;

		// Print the Result of Hotpotatoes Tests
		if (is_array($hpresults) && !empty($hpresults)) {
            $isHotPotato = true;

			for ($i = 0; $i < sizeof($hpresults); $i++) {
				$return[$i] = array();
				$title = GetQuizName($hpresults[$i]['exe_name'], $document_path);
				if ($title =='') {
					$title = basename($hpresults[$i]['exe_name']);
				}
				if (empty($user_id)) {
				    $return[$i]['email'] = $hpresults[$i]['email'];
					$return[$i]['first_name'] = $hpresults[$i]['userpart1'];
					$return[$i]['last_name'] = $hpresults[$i]['userpart2'];
				}
				$return[$i]['title'] = $title;
				$return[$i]['start_date']  = api_get_local_time($results[$i]['exstart']);
                $return[$i]['end_date']    = api_get_local_time($results[$i]['exdate']);
                $return[$i]['duration']    = $results[$i]['duration'];

				$return[$i]['result'] = $hpresults[$i]['exe_result'];
				$return[$i]['max'] = $hpresults[$i]['exe_weighting'];

                $userWithResults[$results[$i]['excruid']] = 1;
			}
		}

        if ($this->includeAllUsers) {
            $latestId = count($return);
            $userWithResults = array_keys($userWithResults);
            if (empty($sessionId)) {
                $students = CourseManager::get_user_list_from_course_code($cid);
            } else {
                $students = CourseManager::get_user_list_from_course_code($cid, $sessionId);
            }

            if (!empty($students)) {
                foreach ($students as $student) {
                    if (!in_array($student['user_id'], $userWithResults)) {
                        $i = $latestId;
                        $isWestern = api_is_western_name_order();
                        if ($isHotPotato) {
                            $return[$i] = array();

                            if (empty($user_id)) {
                                $return[$i]['email'] = $student['email'];
                                if ($isWestern) {
                                    $return[$i]['first_name'] = $student['firstname'];
                                    $return[$i]['last_name'] = $student['lastname'];
                                } else {
                                    $return[$i]['first_name'] = $student['lastname'];
                                    $return[$i]['last_name'] = $student['firstname'];
                                }
                            }
                            $return[$i]['title'] = null;
                            $return[$i]['start_date'] = null;
                            $return[$i]['end_date'] = null;
                            $return[$i]['duration'] = null;
                            $return[$i]['result'] = null;
                            $return[$i]['max'] = null;

                        } else {
                            if (empty($user_id)) {
                                $return[$i]['official_code']   = $student['official_code'];
                                if ($isWestern) {
                                    $return[$i]['first_name']   = $student['firstname'];
                                    $return[$i]['last_name']    = $student['lastname'];
                                } else {
                                    $return[$i]['first_name']   = $student['lastname'];
                                    $return[$i]['last_name']    = $student['firstname'];
                                }

                                $return[$i]['user_id']      = $student['user_id'];
                                $return[$i]['email']        = $student['email'];
                            }
                            $return[$i]['title'] = null;
                            $return[$i]['start_date'] = null;
                            $return[$i]['end_date'] = null;
                            $return[$i]['duration'] = null;
                            $return[$i]['result'] = null;
                            $return[$i]['max'] = null;
                            $return[$i]['status'] = get_lang('NotAttempted');
                            $return[$i]['lp_id'] = null;
                            $return[$i]['lp_name'] = null;
                        }
                        $latestId++;
                    }
                }
            }
        }

		$this->results = $return;

		return true;
	}

	/**
	 * Exports the complete report as a CSV file
	 * @param	string		Document path inside the document tool
	 * @param	integer		Optional user ID
	 * @param	boolean		Whether to include user fields or not
	 * @return	boolean		False on error
	 */
    public function exportCompleteReportCSV(
        $document_path = '',
        $user_id = null,
        $export_user_fields = false,
        $export_filter = 0,
        $exercise_id = 0,
        $hotpotato_name = null
    ) {
		global $charset;
		$this->getExercisesReporting($document_path, $user_id, $export_filter, $exercise_id, $hotpotato_name);

		$filename = 'exercise_results_'.date('YmdGis').'.csv';
		if(!empty($user_id)) {
			$filename = 'exercise_results_user_'.$user_id.'_'.date('YmdGis').'.csv';
		}
		$data = '';
        if (api_is_western_name_order()) {
            if(!empty($this->results[0]['first_name'])) {
                $data .= get_lang('FirstName').';';
            }
            if(!empty($this->results[0]['last_name'])) {
                $data .= get_lang('LastName').';';
            }
        } else {
            if(!empty($this->results[0]['last_name'])) {
                $data .= get_lang('LastName').';';
            }
            if(!empty($this->results[0]['first_name'])) {
                $data .= get_lang('FirstName').';';
            }
        }
        $officialCodeInList = api_get_configuration_value('show_official_code_exercise_result_list');
        if ($officialCodeInList) {
            $data .= get_lang('OfficialCode').';';
        }

        $data .= get_lang('Email').';';
        $data .= get_lang('Groups').';';

		if ($export_user_fields) {
			//show user fields section with a big th colspan that spans over all fields
			$extra_user_fields = UserManager::get_extra_fields(0,1000,5,'ASC',false, 1);
			$num = count($extra_user_fields);
			foreach($extra_user_fields as $field) {
				$data .= '"'.str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($field[3]), ENT_QUOTES, $charset)).'";';
			}
		}

		$data .= get_lang('Title').';';
		$data .= get_lang('StartDate').';';
        $data .= get_lang('EndDate').';';
        $data .= get_lang('Duration'). ' ('.get_lang('MinMinutes').') ;';
		$data .= get_lang('Score').';';
		$data .= get_lang('Total').';';
        $data .= get_lang('Status').';';
        $data .= get_lang('ToolLearnpath').';';
		$data .= "\n";

		//results
		foreach ($this->results as $row) {

            if (api_is_western_name_order()) {
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['first_name']), ENT_QUOTES, $charset)).';';
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['last_name']), ENT_QUOTES, $charset)).';';
            } else {
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['last_name']), ENT_QUOTES, $charset)).';';
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['first_name']), ENT_QUOTES, $charset)).';';
            }

            if ($officialCodeInList) {
                $data .= $row['official_code'].';';
            }

            $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['email']), ENT_QUOTES, $charset)).';';
            $data .= str_replace("\r\n",'  ',implode(", ", GroupManager :: get_user_group_name($row['user_id']))).';';

			if ($export_user_fields) {
				//show user fields data, if any, for this user
				$user_fields_values = UserManager::get_extra_user_data($row['user_id'],false,false, false, true);
				foreach($user_fields_values as $value) {
					$data .= '"'.str_replace('"','""',api_html_entity_decode(strip_tags($value), ENT_QUOTES, $charset)).'";';
				}
			}

			$data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($row['title']), ENT_QUOTES, $charset)).';';
			$data .= str_replace("\r\n",'  ',$row['start_date']).';';
            $data .= str_replace("\r\n",'  ',$row['end_date']).';';
            $data .= str_replace("\r\n",'  ',$row['duration']).';';
			$data .= str_replace("\r\n",'  ',$row['result']).';';
			$data .= str_replace("\r\n",'  ',$row['max']).';';
            $data .= str_replace("\r\n",'  ',$row['status']).';';
            $data .= str_replace("\r\n",'  ',$row['lp_name']).';';
			$data .= "\n";
		}

		//output the results
		$len = strlen($data);
		header('Content-type: application/octet-stream');
		header('Content-Type: application/force-download');
		header('Content-length: '.$len);
		if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: filename= '.$filename);
		} else {
			header('Content-Disposition: attachment; filename= '.$filename);
		}
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			header('Pragma: ');
			header('Cache-Control: ');
			header('Cache-Control: public'); // IE cannot download from sessions without a cache
		}
		header('Content-Description: '.$filename);
		header('Content-transfer-encoding: binary');
		echo $data;
		return true;
	}

	/**
	 * Exports the complete report as an XLS file
	 * @return	boolean		False on error
	 */
    public function exportCompleteReportXLS(
        $document_path = '',
        $user_id = null,
        $export_user_fields = false,
        $export_filter = 0,
        $exercise_id = 0,
        $hotpotato_name = null
    ) {
		global $charset;
		$this->getExercisesReporting($document_path, $user_id, $export_filter, $exercise_id, $hotpotato_name);
		$filename = 'exercise_results_'.date('YmdGis').'.xls';
		if (!empty($user_id)) {
			$filename = 'exercise_results_user_'.$user_id.'_'.date('YmdGis').'.xls';
		}
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(api_get_path(SYS_ARCHIVE_PATH));
		$workbook->setVersion(8); // BIFF8

		$workbook->send($filename);
		$worksheet =& $workbook->addWorksheet('Report '.date('YmdGis'));
		$worksheet->setInputEncoding(api_get_system_encoding());
		$line = 0;
		$column = 0; //skip the first column (row titles)

		// check if exists column 'user'
		$with_column_user = false;
		foreach ($this->results as $result) {
			if (!empty($result['last_name']) && !empty($result['first_name'])) {
				$with_column_user = true;
				break;
			}
		}

        $officialCodeInList = api_get_configuration_value('show_official_code_exercise_result_list');

		if ($with_column_user) {
            if (api_is_western_name_order()) {
    			$worksheet->write($line,$column,get_lang('FirstName'));
    			$column++;
                $worksheet->write($line,$column,get_lang('LastName'));
                $column++;
            } else {
                $worksheet->write($line,$column,get_lang('LastName'));
                $column++;
                $worksheet->write($line,$column,get_lang('FirstName'));
                $column++;
            }

            if ($officialCodeInList) {
                $worksheet->write($line, $column, get_lang('OfficialCode'));
                $column++;
            }

		    $worksheet->write($line,$column,get_lang('Email'));
		    $column++;
		}
	    $worksheet->write($line,$column,get_lang('Groups'));
	    $column++;

		if ($export_user_fields) {
			//show user fields section with a big th colspan that spans over all fields
			$extra_user_fields = UserManager::get_extra_fields(0,1000,5,'ASC',false, 1);

			//show the fields names for user fields
			foreach ($extra_user_fields as $field) {
				$worksheet->write($line,$column,api_html_entity_decode(strip_tags($field[3]), ENT_QUOTES, $charset));
				$column++;
			}
		}

		$worksheet->write($line,$column, get_lang('Title'));
		$column++;
		$worksheet->write($line,$column, get_lang('StartDate'));
        $column++;
        $worksheet->write($line,$column, get_lang('EndDate'));
        $column++;
        $worksheet->write($line,$column, get_lang('Duration').' ('.get_lang('MinMinutes').')');
		$column++;
		$worksheet->write($line,$column, get_lang('Score'));
		$column++;
		$worksheet->write($line,$column, get_lang('Total'));
		$column++;
        $worksheet->write($line,$column, get_lang('Status'));
		$column++;
        $worksheet->write($line,$column, get_lang('ToolLearnpath'));
		$line++;

		foreach ($this->results as $row) {
			$column = 0;

            if ($with_column_user) {
                if (api_is_western_name_order()) {
                    $worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['first_name']), ENT_QUOTES, $charset));
                    $column++;
                    $worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['last_name']), ENT_QUOTES, $charset));
                    $column++;
                } else {
                    $worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['last_name']), ENT_QUOTES, $charset));
                    $column++;
                    $worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['first_name']), ENT_QUOTES, $charset));
                    $column++;
                }

                if ($officialCodeInList) {
                    $worksheet->write($line, $column,api_html_entity_decode(strip_tags($row['official_code']), ENT_QUOTES, $charset));
                    $column++;
                }

                $worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['email']), ENT_QUOTES, $charset));
                $column++;
			}

            $worksheet->write($line,$column,api_html_entity_decode(strip_tags(implode(", ", GroupManager :: get_user_group_name($row['user_id']))), ENT_QUOTES, $charset));
            $column++;

			if ($export_user_fields) {
				//show user fields data, if any, for this user
				$user_fields_values = UserManager::get_extra_user_data($row['user_id'],false,false, false, true);
				foreach($user_fields_values as $value) {
					$worksheet->write($line,$column, api_html_entity_decode(strip_tags($value), ENT_QUOTES, $charset));
					$column++;
				}
			}

			$worksheet->write($line,$column,api_html_entity_decode(strip_tags($row['title']), ENT_QUOTES, $charset));
			$column++;
			$worksheet->write($line,$column,$row['start_date']);
            $column++;
			$worksheet->write($line,$column,$row['end_date']);
            $column++;
			$worksheet->write($line,$column,$row['duration']);
			$column++;
			$worksheet->write($line,$column,$row['result']);
			$column++;
			$worksheet->write($line,$column,$row['max']);
			$column++;
            $worksheet->write($line,$column,$row['status']);
			$column++;
            $worksheet->write($line,$column,$row['lp_name']);
			$line++;
		}
		//output the results
		$workbook->close();
		return true;
	}
}
