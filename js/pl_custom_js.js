var pageNumber = 1;
var cat = 0;
var x = 1;
var sizeArray = ['large', 'medium', 'smallbrick one','smallbrick two', 'large', 'smallbrick one','smallbrick two','medium', 'large', 'smallbrick one','smallbrick two', 'medium', 'large', 'smallbrick one','smallbrick two', 'medium', 'large','smallbrick one','smallbrick two', 'large', 'smallbrick one','smallbrick two','medium', 'medium'];

function getClass(index){
    return sizeArray[index];
}
var resizeTimer;
jQuery( window ).resize(function() {
    if(window.bricklayer){

    clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {    
            for(i = 0; i < bricklayer.elements.length; i++){
                if (bricklayer.elements[i].className == "smallbrick two"){
                    jQuery(bricklayer.elements[i]).appendTo(bricklayer.elements[i-1]);
                    }
                }
            }, 250);
}});

var xxx = 0;
//If it's small brick, add in to brick wrapper
function addtoParent(div){
    var last = bricklayer.elements[bricklayer.elements.length-2];
    return last; 
}

function newBox(value) {
if(getClass(xxx) == "smallbrick one"){
    var brickbox = document.createElement('div');
    brickbox.className = "brick smallx2";
}

var box = document.createElement('div');
box.className = "brick " + getClass(xxx);


  xxx = xxx + 1;
  if(xxx >= 23){
    xxx=0;
  }

  jQuery(box).append(value);
    if(getClass(xxx-1) == "smallbrick one"){
        jQuery(box).removeClass("brick");
        jQuery(brickbox).append(box);
        return brickbox;
    }else{
        return box;
    }
}
//Load posts with Ajax
function load_posts(){
    pageNumber++;
   var str = '&cat=' + cat + '&pageNumber=' + pageNumber + '&ppp=' + ppp + '&action=more_post_ajax';
    jQuery.ajax({
        type: "POST",
        dataType: "html",
        url : myAjax.ajaxurl,
        //action: "load_more_post_ajax",
        data: str,
        success: function(data){
            var jQuerydata = jQuery(data);
            //Get length of how many being pulled in
            if(jQuerydata.length){
                for(i = 0; i < jQuerydata.length; i++){
                    x = (x+1);
                    //Load in to right column, start at 2 finish at 6.
                    if(x >= 6){ x = 2}
                        //If it had bricklayer-column class (IE don't)
                        if(jQuery("div").hasClass('bricklayer-column')){
                             var box = newBox(jQuerydata[i]);
                             bricklayer.append(box);
                        }else{
                             var box = newBox(jQuerydata[i]);
                             bricklayer.append(box);
                        }
                        if(box.className == "brick smallbrick two"){
                            jQuery(box).removeClass("brick");
                            jQuery(addtoParent(box)).append(box);
                        }

                }
                //Change state of add more posts button
                if(jQuerydata.length > 23 ){
                    jQuery("#more_posts").removeClass("disabled");
                }else{
                    jQuery("#more_posts").addClass("disabled");
                }
            } else{
                jQuery("#more_posts").addClass("disabled");
            }         
        },
        complete : function(){
            loadBrickanimation();
        },
        error : function(jqXHR, textStatus, errorThrown) {
            jQueryloader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
        }

    });
      var numItems = jQuery('.brick').length;
        return false;
}
	//Disable add more button 
	jQuery("#more_posts").on("click",function(){ // When btn is pressed.
    jQuery("#more_posts").attr("disabled",true); // Disable the button, temp.
    load_posts();

});

function loadBrickanimation(){

jQuery( ".brick.large, .brick.medium" ).hover(
  function() {
    jQuery( this ).find( ".inner h3" ).stop().slideToggle("fast");
  }, function() {
    jQuery( this ).find( ".inner h3" ).stop().slideToggle("fast");
  }
);
}



