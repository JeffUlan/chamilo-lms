//function below added by logan (cailongqun [at] yahoo [dot] com [dot] cn) from www.phpletter.com

function selectFile(url)
{
	var selectedFileRowNum = $('#selectedFileRowNum').val();
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {
	  // insert information now
	 // var url = $('#fileUrl'+selectedFileRowNum).val();  	//comment and replaced for  put url into selecFile(url) by Juan Carlos Ra�a
		window.opener.SetUrl( url ) ;
		window.close() ;
		
  }else
  {
  	alert(noFileSelected);
  }
  

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
}



/*

// Alternative configuration. Juan Carlos Ra�a

function selectFile()
{
	//juan carlos ra�a quiz� si metemos aqu� un while meter�a todos los marcados y no solo el ultimo?, as� se recoger�an selecciones m�ltiples
  var selectedFileRowNum = getNum($('input[@type=checkbox][@checked]').attr('id'));
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {
     // insert information now
     var url = files[selectedFileRowNum]['url'];
      window.opener.SetUrl(url) ;
      window.close() ;
      
  }else
  {
     alert(noFileSelected);
  }
}

function cancelSelectFile()
{
  // close popup window
  window.close() ;
}

*/