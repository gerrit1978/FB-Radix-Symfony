$(document).ready(function() {

  $('li.job').click(function() {
    var url = $(this).data('job-url');
    self.location.href = url;
  });

/*
    FB.Canvas.setAutoGrow(); //from 5 July
    FB.Canvas.scrollTo(0,0);
    fbApiInit = true; //init flag
*/

});