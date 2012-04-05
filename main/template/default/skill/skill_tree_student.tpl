<style>
/* just because */
body {    
    /* fallback */ 
    background-color: #eee; 
    background-image: url(images/radial_bg.png); 
    background-position: center center; 
    background-repeat: no-repeat; 
    
    /* Safari 4-5, Chrome 1-9 */ /* Can't specify a percentage size? Laaaaaame. */ 
    background: -webkit-gradient(radial, center center, 0, center center, 460, from(#eee), to(#666)); 
    /* Safari 5.1+, Chrome 10+ */ 
    background: -webkit-radial-gradient(circle, #eee, #666); 
    /* Firefox 3.6+ */ 
    background: -moz-radial-gradient(circle, #eee, #666); 
    /* IE 10 */ 
    background: -ms-radial-gradient(circle, #eee, #666);
}
</style>
<script type="text/javascript">

//js settings
var url             = '{{url}}';
var skills          = []; //current window divs
var parents         = []; //list of parents normally there should be only 2
var hidden_parent   = '';
var duration_value  = 500;


//Block settings see the SkillVisualizer Class
var offset_x                = {{skill_visualizer.offset_x}};
var offset_y                = {{skill_visualizer.offset_y}};
var space_between_blocks_x  = {{skill_visualizer.space_between_blocks_x}};
var space_between_blocks_y  = {{skill_visualizer.space_between_blocks_y}};
var center_x                = {{skill_visualizer.center_x}};
var block_size              = {{skill_visualizer.block_size}};

//Setting the parent by default 
var parents = ['block_1'];

jsPlumb.bind("ready", function() {
    
    //Open dialog
    $("#dialog-form").dialog({
        autoOpen: false,
        modal   : true, 
        width   : 550, 
        height  : 350,
    });
    
    //On box click -(we use live instead of bind because we're creating divs on the fly )
    $(".open_block").live('click', function() {        
        var id = $(this).attr('id');
        
        //if is root
        if (parents[0] == id) {
            parents = [id];
        }     
        
        if (parents[1] != id) {            
            if (parents.length == 2 ) {
                hidden_parent = parents[0];   
                //console.log('deleting: '+parents[0]);        
                //removing father
                for (var i = 0; i < skills.length; i++) {
                               
                    if ( skills[i].element == parents[0] ) {
                         //console.log('deleting :'+ skills[i].element + ' here ');                             
                         jsPlumb.deleteEndpoint(skills[i].endp);
                         $("#"+skills[i].element).remove();
                         //skills.splice(i,1)
                    }
                }                                
                parents.splice(0,1);                
                parents.push(id);
            }     
                
            if ($(this).hasClass('first_window')) {                
                 //show the hidden_parent
                //if (hidden_parent != '') {
                   parents = [hidden_parent, id];
                   //    console.log(parents);
                   open_parent(hidden_parent, id);
                //}
            }
            if (jQuery.inArray(id, parents) == -1) {                              
                parents.push(id);
            }
            open_block_student(id);
        }
        
        //Setting class       
        cleanclass($(this));
        $(this).addClass('second_window');        
        
        parent_div = $("#"+parents[0]);
        cleanclass(parent_div);
        parent_div.addClass('first_window');
        
        parent_div = $("#"+parents[1]);        
        cleanclass(parent_div);
        parent_div.addClass('second_window');
       
        console.log(parents);
       // console.log(skills);        
        console.log('hidden_parent : ' + hidden_parent);         
    });
    
    
    
    $(".edit_block").live('click',function() {        
        var my_id = $(this).attr('id');
        my_id = my_id.split('_')[2];
                
        $.ajax({
            url: url+'&a=get_skill_info&id='+my_id,             
            success: function(json) {
                var skill = jQuery.parseJSON(json);
                $("#name").html(skill.name);
                $("#id").attr('value',   skill.id);                        
                $("#description").html(skill.description);                
                //filling parent_id
                $("#parent_id option[value='"+skill.extra.parent_id+"']").attr('selected', 'selected');
                //filling the gradebook_id         
                $("#gradebook_id").html('');
                jQuery.each(skill.gradebooks, function(index, data) {                    
                    $("#gradebook_id").append('<span class="label_tag gradebook">'+data.name+'</div>');
                });
            },
        });
                
        $("#dialog-form").dialog("open");
        return false;
    });
    
    $("#dialog-form").dialog({
        close: function() {     
            $("#name").html('');
            $("#description").html('');                
        }
    });    
    
    // chrome fix.
    document.onselectstart = function () { return false; };             

    // render mode
    var resetRenderMode = function(desiredMode) {
        var newMode = jsPlumb.setRenderMode(desiredMode);        
        jsPlumbDemo.init();
    };    
    resetRenderMode(jsPlumb.CANVAS);       
});
        
            
;(function() {    
    window.jsPlumbDemo = {  	   
        init : function() {
            console.log('Import defaults');            
            jsPlumb.Defaults.Anchors = [ "BottomCenter", "TopCenter" ];            
            jsPlumb.Defaults.Container = "skill_tree";
            
            open_block('block_1', 1, 1);            
        }
    };
})();



$(document).ready( function() {     
    //When creating a connection see
    //http://jsplumb.org/apidocs/files/jsPlumb-1-3-2-all-js.html#bind 
    jsPlumb.bind("jsPlumbConnection", function(conn) {
        //alert("Connection created " + conn.sourceId + " to " + conn.targetId + " ");
            //jsPlumb.detach(conn); 
    });
});
</script>

<div id="skill_tree"></div>

<div id="dialog-form" style="display:none;">    
    <form id="add_item" class="form-horizontal"  name="form">
        <fieldset>
        <input type="hidden" name="id" id="id"/>
        <div class="control-group">            
            <label class="control-label" for="name">Name</label>            
            <div class="controls">                
                <span id="name"></span>             
            </div>
        </div>
         <div class="control-group">            
            <label class="control-label" for="name">Description</label>            
            <div class="controls">
                <span id="description"></span>                
            </div>
        </div>  
        <div class="control-group">            
            <label class="control-label" for="name">Gradebook</label>            
            <div class="controls">
                <div id="gradebook_id"></div>                
            </div>
        </div>      
        </fieldset>
    </form>    
</div>