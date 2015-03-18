/* For licensing terms, see /license.txt */
/*
 Learning Path minipanel - Chamilo 1.8.8
 Adding mini panel to browse Learning Paths
 Requirements: JQuery 1.4.4, JQuery UI 1.8.7
 @author Alberto Torreblanca @albert1t0
 @author Julio Montoya Cleaning/fixing some code
 **/

// Copy little progress bar in <tr></tr>
function toggle_minipanel() {

    // Construct mini panel
    var panel = $('#lp_navigation_elem div:first').clone();

    $(panel).attr('id', 'control');
    $('#learning_path_main').append(panel);

    $('#learning_path_main #control tr').after('<tr></tr>');
	$('#learning_path_main #control tr:eq(1)').append($('#progress_bar').html());
	$('#learning_path_main #control tr:eq(1) #progress_img_limit_left').attr('height','5');
	$('#learning_path_main #control tr:eq(1) #progress_img_full').attr('height','5');
	$('#learning_path_main #control tr:eq(1) #progress_img_limit_middle').attr('height','5');
	$('#learning_path_main #control tr:eq(1) #progress_img_empty').attr('height','5');
	$('#learning_path_main #control tr:eq(1) #progress_bar_img_limit_right').attr('height','5');
	$('#learning_path_main #control tr:eq(1) #progress_text').remove();
	$('#learning_path_main #control tr:eq(1) div').css('width','');

    $('#learning_path_main #control .buttons').attr('text-align','center');
    $('#content_id').css({ height: $('#content_id').height() - ($('#control').height() + 10) });

    $('#learning_path_main #control .buttons img').click(function(){
        $('#learning_path_main #control tr:eq(1)').remove();
        toggle_minipanel();
    });
    // Hiding navigation left zone
    $('#learning_path_left_zone').hide(50);
    $('#learning_path_right_zone').css('margin-left','10px');
    $('#hide_bar table').css('backgroundImage','url(../img/hide2.png)').css('backgroundColor','#EEEEEE');
}
function hiddenPanel(){
    $("#learning_path_left_zone").addClass('demo');
}
var left_width_mini = 20;  // (relative) hide_bar position

$(document).ready(function() {

    var left_width = $('#learning_path_left_zone').width();

   //Adding div to hide panel
    $('#learning_path_right_zone').before('<div id="hide_bar" class="scorm-toggle" style="float: left; width: 25px; height: 1000px;"></div>');
    //$('#hide_bar table').css({backgroundImage: "url(../img/hide0.png)", backgroundRepeat: "no-repeat", backgroundPosition: "center center"});



    $("#hider_bar").click(function(){
        $("#learning_path_left_zone").css('display:none;');
    });

    //Adding effects to hide bar
    /* $('#hide_bar table').hover(function () {
    	if ($('#hide_bar').position().left >= left_width)
    		$(this).css('backgroundImage','url(../img/hide1.png)').css('backgroundColor','#888888');
    	else if($('#hide_bar').position().left <= left_width_mini)
    		$(this).css('backgroundImage','url(../img/hide3.png)').css('backgroundColor','#888888');
        },function (){
            if($('#hide_bar').position().left >= left_width)
              $(this).css('backgroundImage','url(../img/hide0.png)').css('backgroundColor','#EEEEEE');
            else if($('#hide_bar').position().left <= left_width_mini)
              $(this).css('backgroundImage','url(../img/hide2.png)').css('backgroundColor','#EEEEEE');
        }
    );
    */
    var original = $('#content_id').height();

    // Adding functionality

    /*$('#hide_bar table').toggle(function(){
        if ($('#hide_bar').position().left >= left_width) {
            toggle_minipanel();
        }
    },
    function() {
        // Show navigation left zone
        $('#learning_path_left_zone').show(50);
        $('#learning_path_right_zone').css('marginLeft', left_width + 25 + 'px');
        /* $('#hide_bar table').css('backgroundImage','url(../img/hide0.png)').css('backgroundColor','#EEEEEE');
        $('#learning_path_main  #control').remove();
        $('#content_id').css({ height: original});
    }); */


});
