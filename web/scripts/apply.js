  // get the div that holds the tags
  var workCollectionHolder = $('ul.work');
  
  var educationCollectionHolder = $('ul.education');
  
  // setup an Add a work link
  var $addWorkLink = $('<a href="#" class="add_work_link">Add a work</a>');
  var $newLinkLi = $('<li></li>').append($addWorkLink);

  // setup an Add an education link
  var $addEducationLink = $('<a href="#" class="add_education_link">Add an education</a>');
  var $newLinkEducationLi = $('<li></li>').append($addEducationLink);



$(document).ready(function() {

  

  /*** WORK ITEMS ***/
  
  // hide the default form items
  $('ul.work li').css('display', 'none');
  
  var output = "";
  
  $('ul.work li').each(function(i) {
  
    var employer = $(this).find('.item-employer').find('input').val();
    var position = $(this).find('.item-position').find('input').val();
    var startdate = $(this).find('.item-startdate').find('input').val();
    var enddate = $(this).find('.item-enddate').find('input').val();

    output += "<div class='work-overview' id='overview" + i + "'>";
    output += "<div class='employer'>" + employer + "</div>";
    
    if (position != "null" && position != null && position != "") {
      output += "<div class='position'>" + position + "</div>";
    }  else {
      output += "<div class='position'></div>";
    }
    
    var date = "<div class='dates'>";
    
    if (startdate != "null" && startdate != null && startdate != "" && startdate != "0000-00") {
      if (enddate != "null" && enddate != null && enddate != "" && enddate != "0000-00") {
        var dates = startdate + " tot " + enddate;
      } else {
        var dates = startdate;
      }
    } else {
      if (enddate != "null" && enddate != null && enddate != "" && enddate != "0000-00") {
        var dates = "tot " + enddate;
      } else {
        var dates = "";
      }
    }
    
    date += dates + "</div>";
    
    output += date;
    
    output += "<a href='#' class='edit_work_link' id='edit" + i + "'>bewerken</a>";
    output += "</div>";

  });

  // show the static overview instead of the default form items
  $('ul.work').append(output);

  /*** END WORK ITEMS ***/

  /*** EDUCATION ITEMS ***/
  
  // hide the default form items
  $('ul.education li').css('display', 'none');
  
  var output = "";
  
  $('ul.education li').each(function(i) {
  
    var school = $(this).find('.item-school').find('input').val();
    var year = $(this).find('.item-year').find('input').val();
    var type = $(this).find('.item-type').find('input').val();

    output += "<div class='education-overview' id='edu-overview" + i + "'>";
    output += "<div class='school'>" + school + "</div>";
    
    if (year != "null" && year != null && year != "") {
      output += "<div class='year'>" + year + "</div>";
    }  else {
      output += "<div class='year'></div>";
    }

    if (type != "null" && type != null && type != "") {
      output += "<div class='type'>" + type + "</div>";
    }  else {
      output += "<div class='type'></div>";
    }

    output += "<a href='#' class='edit_education_link' id='edit-edu" + i + "'>bewerken</a>";
    output += "</div>";

  });

  // show the static overview instead of the default form items
  $('ul.education').append(output);

  /*** END EDUCATION ITEMS ***/

 

  // add "Delete" links 
  $('ul.work').find('li').each(function() {
    addFormDeleteLink($(this));
  });
  
  $('ul.education').find('li').each(function() {
    addFormDeleteLink($(this));
  });

  // add "New" links
  $('ul.work').append($newLinkLi);
  
  $('ul.education').append($newLinkEducationLi);

  // event listener for add links  
  $addWorkLink.on('click', function(e) {
    e.preventDefault();
    addWorkForm($('ul.work'), $newLinkLi);
  });

  $addEducationLink.on('click', function(e) {
    e.preventDefault();
    addEducationForm($('ul.education'), $newLinkEducationLi);
  });
  
  // enable the fancybox
  enableFancybox($);
  
});

function enableFancybox($) {
	$("a.edit_work_link").each(function() {
	  var id = $(this).attr('id');
	  var index = id.replace('edit', '');
	  var indexCorrected = parseInt(index) + 1;
	  var selector = "ul.work li:nth-child(" + indexCorrected + ")";
	  var content = $(selector).html();
	  content += "<input type='hidden' value='" + index + "' name='delta' class='delta' />";
	  content += "<a href='#' onClick='closeFancybox($);'>Overnemen</a>";
	  $(this).fancybox({
	        transitionIn: 'elastic',
	        transitionOut: 'elastic',
	        speedIn: 600,
	        speedOut: 200,
	        width: 500,
          height: "auto",
          autoSize: false,
          fitToView: false,
	        content: content,
          beforeClose: copyWorkValues 
	  });
	});

  $("a.edit_education_link").each(function() {
    var id = $(this).attr('id');
    var index = id.replace('edit-edu', '');
	  var indexCorrected = parseInt(index) + 1;
	  var selector = "ul.education li:nth-child(" + indexCorrected + ")";
	  var content = $(selector).html();
	  var content = $(selector).html();
	  content += "<input type='hidden' value='" + index + "' name='delta' class='delta' />";
	  content += "<a href='#' onClick='closeFancybox($);'>Overnemen</a>";
	  $(this).fancybox({
	        transitionIn: 'elastic',
	        transitionOut: 'elastic',
	        speedIn: 600,
	        speedOut: 200,
	        width: 500,
          height: "auto",
          autoSize: false,
          fitToView: false,
	        content: content,
          beforeClose: copyEducationValues 
	  });

  });


}

function closeFancybox($) {
  $.fancybox.close();
  return false;
}

function copyWorkValues() {
  var employer = $('.fancybox-outer .item-employer input').val();
  var location = $('.fancybox-outer .item-location input').val();
  var position = $('.fancybox-outer .item-position input').val();
  var description = $('.fancybox-outer .item-description input').val();
  var startDate = $('.fancybox-outer .item-startdate input').val();
  var endDate = $('.fancybox-outer .item-enddate input').val();

  var dates = "";

  if (startDate != null && startDate != "null" && startDate != "") {
    if (endDate != null && endDate != "null" && endDate != "") {
      dates = "<span class='startdate'>" + startDate + "</span> tot <span class='enddate'>" + endDate + "</span>";
    } else {
      dates = "<span class='startdate'>" + startDate + "</span>";
    }
  } else {
    if (endDate != null && endDate != "null" && endDate != "") {
      dates = "tot <span class='enddate'>" + endDate + "</span>";
    }
  }
  

  var delta = $('.fancybox-outer .delta').val();  
  // copy the values to the overview (static) part
  var selector = "#overview" + delta;
  var selectorEmployer = selector + " div.employer";
  var selectorPosition = selector + " div.position";
  var selectorDates = selector + " div.dates";
  
  $(selectorEmployer).html(employer);
  $(selectorPosition).html(position);  
  $(selectorDates).html(dates);  
  
  // copy the values to the internal Symfony form
  var selector = "#application_work_";
  var sSelectorEmployer = selector + delta + "_employer";
  var sSelectorLocation = selector + delta + "_location";
  var sSelectorPosition = selector + delta + "_position";
  var sSelectorDescription = selector + delta + "_description";
  var sSelectorStartDate = selector + delta + "_startdate";
  var sSelectorEndDate = selector + delta + "_enddate";
  
  $(sSelectorEmployer).attr('value', employer);
  $(sSelectorLocation).attr('value', location);
  $(sSelectorPosition).attr('value', position);
  $(sSelectorDescription).attr('value', description);
  $(sSelectorStartDate).attr('value', startDate);
  $(sSelectorEndDate).attr('value', endDate);

  // refresh the fancybox content
  enableFancybox($);
}

function copyEducationValues() {
  var school = $('.fancybox-outer .item-school input').val();
  var year = $('.fancybox-outer .item-year input').val();
  var type = $('.fancybox-outer .item-type input').val();

  var delta = $('.fancybox-outer .delta').val();  
  // copy the values to the overview (static) part

  var selector = "#edu-overview" + delta;
  var selectorSchool = selector + " div.school";
  var selectorYear = selector + " div.year";
  var selectorType = selector + " div.type";
  
  $(selectorSchool).html(school);
  $(selectorYear).html(year);  
  $(selectorType).html(type);  
  
  // copy the values to the internal Symfony form
  var selector = "#application_education_";
  var sSelectorSchool = selector + delta + "_school";
  var sSelectorYear = selector + delta + "_year";
  var sSelectorType = selector + delta + "_type";
  
  $(sSelectorSchool).attr('value', school);
  $(sSelectorYear).attr('value', year);
  $(sSelectorType).attr('value', type);

  // refresh the fancybox content
  enableFancybox($);
}



function addWorkForm(workCollectionHolder, $newLinkLi) {
  var prototype = workCollectionHolder.attr('data-prototype');
  var newForm = prototype.replace(/__name__/g, workCollectionHolder.children().length);
  
  var $newFormLi = $('<li></li>').append(newForm);
  $newLinkLi.before($newFormLi);
}

function addEducationForm(educationCollectionHolder, $newLinkEducationLi) {
  var prototype = educationCollectionHolder.attr('data-prototype');
  var newEducationForm = prototype.replace(/__name__/g, educationCollectionHolder.children().length);
  
  var $newEducationFormLi = $('<li></li>').append(newEducationForm);
  $newLinkEducationLi.before($newEducationFormLi);
}


function addFormDeleteLink($tagFormLi) {
  var $removeFormA = $('<a href="#">delete</a>');
  $tagFormLi.append($removeFormA);
  
  $removeFormA.on('click', function(e) {
    e.preventDefault();
    
    $tagFormLi.remove();
  });
  
}