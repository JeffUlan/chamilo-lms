
<div id="content">

<table class="tableList" id="tableList" cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<th width="5"><a href="#" class="check_all" id="tickAll" title="<?php echo TIP_SELECT_ALL; ?>" onclick="checkAll(this);">&nbsp;</a></th>
							<th width="10" class="fileColumns">&nbsp;</th>
							<th class="docName"><?php echo LBL_NAME; ?></th>
							<th  width="70" class="fileColumns"><?php echo LBL_SIZE; ?></th>
							<th class="fileColumns"><?php echo LBL_MODIFIED; ?></th>
						</tr>
					</thead>
					<tbody id="fileList">
						<?php					
						
							$count = 1;
							$css = "";
							//list all documents (files and folders) under this current folder, 
							// echo appendQueryString(appendQueryString(CONFIG_URL_FILEnIMAGE_MANAGER, "path=" . $file['path']), makeQueryString(array('path')));
							foreach($fileList as $file)
							{
								$css = ($css == "" || $css == "even"?"odd":"even");
								$strDisabled = ($file['is_writable']?"":" disabled");
								$strClass = ($file['is_writable']?"left":" leftDisabled");
								
								///First step for hidden some type of Dokeos files and folders 
								//Juan Carlos Ra�a
								
									//hidden files and folders deleted by Dokeos. Hidde folders css
									$deleted_by_dokeos='_DELETED_';
									$css_folder_dokeos='css';
									
									//hidden directory of the group if the user is not a member of the group					
									$group_folder='_groupdocs';
									
									
									//show group's directory only if I'm member
									$show_doc_group=true;								
									if(ereg($group_folder, $file['path']))
									{
										$show_doc_group=false;
										if($is_user_in_group)
										{
											$show_doc_group=true;					
										}
									}
									
									//show icon sharedfolder
									$shared_folder='sharedfolder';								
								
								///Second step: hiding as the case
								//Juan Carlos Ra�a
								
								if((!ereg($deleted_by_dokeos, $file['name']) || !ereg($deleted_by_dokeos, $file['path'])) && !ereg($css_folder_dokeos, $file['path']) && $show_doc_group==true)
								{	
															
								
									if($file['type'] == 'file')
									{		
								
									?>
									<tr class="<?php echo $css; ?>" id="row<?php echo $count; ?>"  >
										<td align="center" id="tdz<?php echo $count; ?>"><span id="flag<?php echo $count; ?>" class="<?php echo $file['flag']; ?>">&nbsp;</span><input type="checkbox"  name="check[]" id="cb<?php echo $count; ?>" value="<?php echo $file['path']; ?>" <?php echo $strDisabled; ?> />
											</td>
										<td align="center" class="fileColumns" id="tdst<?php echo $count; ?>">&nbsp;<a id="a<?php echo $count; ?>" href="<?php echo "../".$file['path']; ?>" target="_blank"><span class="<?php echo $file['cssClass']; ?>">&nbsp;</span></a></td><!-- Juan Carlos Ra�a Fix for Dokeos: On the path I put a directory up echo "../".$ file [ 'path'], what makes good show when pressed next on window preview, don't only one image -->
										<td class="<?php echo $strClass; ?> docName"  id="tdnd<?php echo $count; ?>"><?php echo $file['name']; ?></td>
										<td class="docInfo" id="tdrd<?php echo $count; ?>"><?php echo transformFileSize($file['size']); ?></td>
										<td class="docInfo" id="tdth<?php echo $count; ?>"><?php echo @date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
									</tr>
									<?php
									}else
									{											
									
										?>
										<tr class="<?php echo $css; ?>" id="row<?php echo $count; ?>" >
                                        
											<td align="center" id="tdz<?php echo $count; ?>"><span id="flag<?php echo $count; ?>" class="<?php echo $file['flag']; ?>">&nbsp;</span><input type="checkbox" name="check[]" id="cb<?php echo $count; ?>" value="<?php echo $file['path']; ?>" <?php echo $strDisabled; ?>/>
											</td>
                                                                             
                                            <?php
                                            if(ereg($shared_folder, $file['name']))
                                            {
											//add icon in ajaxfilemanager if sharedfolder is in Dokeos
											?>                                            
                                            	<td  lign="center" class="fileColumns" id="tdst<?php echo $count; ?>">&nbsp;<a id="a<?php echo $count; ?>" href="<?php echo $file['path']; ?>" <?php echo $file['cssClass'] == 'filePicture'?'rel="ajaxPhotos"':''; ?>  ><span class="<?php echo ($file['file']&&$file['subdir']?$file['cssClass']:"folderShared"); ?>">&nbsp;</span></a></td>                                            
                                            <?php
                                            }
                                            else
                                            {
											?>
                                            	<td  lign="center" class="fileColumns" id="tdst<?php echo $count; ?>">&nbsp;<a id="a<?php echo $count; ?>" href="<?php echo $file['path']; ?>" <?php echo $file['cssClass'] == 'filePicture'?'rel="ajaxPhotos"':''; ?>  ><span class="<?php echo ($file['file']||$file['subdir']?$file['cssClass']:"folderEmpty"); ?>">&nbsp;</span></a></td>
                                            <?php
                                            }																					
                                            ?>
                                            
                                            
											<td class="<?php echo $strClass; ?> docName" id="tdnd<?php echo $count; ?>"><?php echo $file['name']; ?></td>
											<td class="docInfo" id="tdrd<?php echo $count; ?>">&nbsp;</td>
											<td class="docInfo" id="tdth<?php echo $count; ?>"><?php echo @date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
										</tr>
										<?php
									}
									
								}//end if hidden files and folders deleted by Dokeos
								
								$count++;
							}
						?>	
					</tbody>
				</table>
</div>