$(document).ready(function(){
  $(window).on("scroll",function(){
    var wn = $(window).scrollTop();
    if(wn > 0){
      $(".navbar").css("background","#011728");
    }
    else{
      $(".navbar").css("background","transparent");
    }
  });
});