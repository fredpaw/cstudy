jQuery(document).ready(function($){
    var flag=1;
    $('#rightArrow').click(function(){
        if(flag==1){
            $("#floatDivBoxs").animate({right: '-195px'},300);
            $(this).animate({right: '-5px'},300);
            $(this).css('background-position','-50px 0');
            flag=0;
        }else{
            $("#floatDivBoxs").animate({right: '0'},300);
            $(this).animate({right: '190px'},300);
            $(this).css('background-position','0px 0');
            flag=1;
        }
    });
});