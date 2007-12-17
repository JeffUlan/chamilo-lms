<?php //$id:$
/**
 * Defines the AICC class, which is meant to contain the aicc items (nuclear elements)
 * @package dokeos.learnpath.aicc
 * @author	Yannick Warnier <ywarnier@beeznest.org>
 * @license	GNU/GPL - See Dokeos license directory for details
 */
/**
 * Defines the "aicc" child of class "learnpath"
 * @package dokeos.learnpath.aicc
 */
require_once('openoffice_document.class.php');

class OpenofficePresentation extends OpenofficeDocument {

    
    function make_lp($files = array()) {
    
    	global $_course;
   
		$previous = 0;
		$i = 0;
		foreach($files as $file){
			$i++;		
			
			// add the png to documents
			$document_id = add_document($_course,$this->created_dir.'/'.$file,'file',filesize($this->base_work_dir.$this->created_dir.'/'.$file),$file);
			api_item_property_update($_course,TOOL_DOCUMENT,$document_id,'DocumentAdded',$_SESSION['_uid'],0,0);
			
			
			// create an html file
			$html_file = $file.'.html';
			$fp = fopen($this->base_work_dir.$this->created_dir.'/'.$html_file, 'w+');
			
			fwrite($fp,
					'<html>
					<head></head>
					<body>
						<img src="'.$this->file_name.'/'.$file.'" />
					</body>
					</html>');
			fclose($fp);
			$document_id = add_document($_course,$this->created_dir.'/'.$html_file,'file',filesize($this->base_work_dir.$this->created_dir.'/'.$html_file),$html_file);
			if ($document_id){	
							
				//put the document in item_property update
				api_item_property_update($_course,TOOL_DOCUMENT,$document_id,'DocumentAdded',$_SESSION['_uid'],0,0);
				
				$infos = pathinfo($file);
				$slide_name = 'slide'.str_repeat('0',2-strlen($i)).$i;
				$previous = learnpath::add_item(0, $previous, 'document', $document_id, $slide_name, '');
				if($this->first_item == 0){
					$this->first_item = $previous;
				}
			}
		}
    }
    
    function add_command_parameters(){
    
    	list($slide_width, $slide_height) = explode('x',api_get_setting('service_ppt2lp','size'));
    	return " -w $slide_width -h $slide_height -d oogie";
    
    }
	    
		
}
?>
