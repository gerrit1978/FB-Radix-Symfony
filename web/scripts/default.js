$("document").ready(function() {

  $.ajaxSetup({ cache: true });
  $.getScript('//connect.facebook.net/en_UK/all.js', function(){
    // we initialize the facebook object. By adding 'xfbml' we make the "Like" plugin on the frontpage work
    FB.init({
      appId: '600850943303218',
      xfbml: true
    });

    FB.Canvas.setAutoGrow(); //from 5 July
    FB.Canvas.scrollTo(0,0);
  });
  


  $('li.job').click(function() {
    var url = $(this).data('job-url');
    self.location.href = url;
  });
  
  $('span.button').click(function() {
    var url = $(this).data('url');
    self.location.href = url;
  });

/*
    FB.Canvas.setAutoGrow(); //from 5 July
    FB.Canvas.scrollTo(0,0);
    fbApiInit = true; //init flag
*/

});