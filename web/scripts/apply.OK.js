$(document).ready(function() {

  /* WORK ITEMS BEGIN */

  // hide the default form items
  $('ul.work li').css('display', 'none');

  // assemble the static overview part, including edit and remove links
  var output = "";
  
  $('ul.work li').each(function(i) {
    var employer = $(this).find('.item-employer').find('input').val();
    var position = $(this).find('.item-position').find('input').val();
    var startdate = $(this).find('.item-startdate').find('input').val();
    var enddate = $(this).find('.item-enddate').find('input').val();

    var datesFormatted = formatDates(startdate, enddate);

    output += "<div class='work-overview' id='work-overview-" + i + "'>";
    output += "<div class='employer'>" + employer + "</div>";
    
    if (position != "null" && position != null && position != "") {
      output += "<div class='position'>" + position + "</div>";
    }  else {
      output += "<div class='position'></div>";
    }
    
    output += datesFormatted;
    
    output += "<a href='#' class='edit_work_link' id='edit-work-" + i + "'>bewerken</a> &bull; <a href='#' class='delete_work_link' id='delete-work-" + i + "'>verwijderen</a>";
    output += "</div>";
  });  
  
  // put this content at the end of the list
  $('div.work-items').append(output);

  // event listener for delete work items
  $('a.delete_work_link').click(function(e) {
    var id = $(this).attr('id');
    var delta = id.replace('delete-work-', '');
    deleteWorkItem(delta);
    e.preventDefault();
  });

  $('ul.work').append($newWorkLinkLi);

  // event listener for add links  
  $addWorkLink.on('click', function(e) {
    e.preventDefault();
    addWorkForm($('ul.work'), $newWorkLinkLi);
  });

  /* WORK ITEMS END */

  /* EDUCATION ITEMS BEGIN */

  // hide the default form items
  $('ul.education li').css('display', 'none');

  // assemble the static overview part, including edit and remove links
  var output = "";
  
  $('ul.education li').each(function(i) {
    var school = $(this).find('.item-school').find('input').val();
    var year = $(this).find('.item-year').find('input').val();
    var type = $(this).find('.item-type').find('input').val();

    output += "<div class='education-overview' id='education-overview-" + i + "'>";
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
    
    if (school != null && school != "" && school != "null") {
      output += "<a href='#' class='edit_education_link' id='edit-education-" + i + "'>bewerken</a> &bull; <a href='#' class='delete_education_link' id='delete-education-" + i + "'>verwijderen</a>";
    }
    output += "</div>";
  });  

  // put this content at the end of the list
  $('div.education-items').append(output);

  // event listener for delete education items
  $('a.delete_education_link').click(function(e) {
    var id = $(this).attr('id');
    var delta = id.replace('delete-education-', '');
    deleteEducationItem(delta);
    e.preventDefault();
  });

  $('ul.education').append($newEducationLinkLi);

  // event listener for add links  
  $addEducationLink.on('click', function(e) {
    e.preventDefault();
    addEducationForm($('ul.education'), $newEducationLinkLi);
  });
  
  
  /* EDUCATION ITEMS END */

  // enable the fancybox
  enableFancybox($);



});

// setup an Add a work link
var $addWorkLink = $('<span class="button" href="#" class="add_work_link">Werk toevoegen</span>');
var $newWorkLinkLi = $('<div></div>').append($addWorkLink);

// setup an Add an education link
var $addEducationLink = $('<span class="button" href="#" class="add_education_link">Opleiding toevoegen</span>');
var $newEducationLinkLi = $('<div></div>').append($addEducationLink);


function enableFancybox($) {
  $("a.edit_work_link").each(function() {
    var id = $(this).attr('id');
    var index = id.replace('edit-work-', '');

    var selector = "ul.work li#form-item-work-" + index;
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
    var index = id.replace('edit-education-', '');

    var selector = "ul.education li#form-item-education-" + index;
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

function closeFancybox() {
  $.fancybox.close();
  return false;
}

function addWorkForm(workCollectionHolder, $newLinkLi) {
  var prototype = workCollectionHolder.attr('data-prototype');

  // define the delta: we first find the largest id number
  var delta = 0;
  var largestId = 0;
  $('ul.work li').each(function() {
    var id = $(this).attr('id');
    if (id != undefined) {
      var idCorrected = parseInt(id.replace("form-item-work-", ""));
      if (idCorrected > largestId) {
        largestId = idCorrected;
      }
    }
  });
  var delta = largestId + 1;
  var newForm = prototype.replace(/__name__/g, delta);

  var content = newForm;
  content += "<input type='hidden' value='" + delta + "' name='delta' class='delta' />";
  content += "<a href='#' onClick='closeFancybox($);'>Overnemen</a>";

  $newLinkLi.fancybox({
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

/*   var $newWorkFormLi = $('<li id="form-item-work-' + delta + '"></li>').append(newForm);   */
  var $newWorkFormLi = $('<li id="form-item-work-' + delta + '"></li>').append(newForm).css('display', 'none');  
  $newWorkLinkLi.before($newWorkFormLi);
}

function addEducationForm(educationCollectionHolder, $newLinkLi) {
  var prototype = educationCollectionHolder.attr('data-prototype');

  // define the delta: we first find the largest id number
  var delta = 0;
  var largestId = 0;
  $('ul.education li').each(function() {
    var id = $(this).attr('id');
    if (id != undefined) {
      var idCorrected = parseInt(id.replace("form-item-education-", ""));
      if (idCorrected > largestId) {
        largestId = idCorrected;
      }
    }
  });
  var delta = largestId + 1;
  var newForm = prototype.replace(/__name__/g, delta);

  var content = newForm;
  content += "<input type='hidden' value='" + delta + "' name='delta' class='delta' />";
  content += "<a href='#' onClick='closeFancybox($);'>Overnemen</a>";

  $newLinkLi.fancybox({
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

/*   var $newEducationFormLi = $('<li id="form-item-education-' + delta + '"></li>').append(newForm);   */
  var $newEducationFormLi = $('<li id="form-item-education-' + delta + '"></li>').append(newForm).css('display', 'none');  
  $newEducationLinkLi.before($newEducationFormLi);
}


function copyWorkValues() {

  // get the values from the Fancybox form
  var employer = $('.fancybox-overlay input.employer').val();
  var location = $('.fancybox-overlay input.location').val();
  var position = $('.fancybox-overlay input.position').val();
  var description = $('.fancybox-overlay input.description').val();
  var startdate = $('.fancybox-overlay input.startdate').val();
  var enddate = $('.fancybox-overlay input.enddate').val();        
  
  var datesFormatted = formatDates(startdate, enddate);
  
  // validation
  if (employer == null || employer == "null" || employer == "") {
    $('.fancybox-overlay input.employer').addClass('error');
    return false;
  }
  
  // first, put the values in the correct Symfony form item
  var delta = $('.fancybox-overlay input.delta').val();

  var sBaseSelector = "#form-item-work-" + delta;
  var sSelectorEmployer = sBaseSelector + " input.employer";
  var sSelectorLocation = sBaseSelector + " input.location";
  var sSelectorPosition = sBaseSelector + " input.position";
  var sSelectorDescription = sBaseSelector + " input.description";
  var sSelectorStartdate = sBaseSelector + " input.startdate";
  var sSelectorEnddate = sBaseSelector + " input.enddate";
  
  $(sSelectorEmployer).attr('value', employer);
  $(sSelectorLocation).attr('value', location);
  $(sSelectorPosition).attr('value', position);
  $(sSelectorDescription).attr('value', description);
  $(sSelectorStartdate).attr('value', startdate);
  $(sSelectorEnddate).attr('value', enddate); 
  
  // second, check if there is already an overview div
  var oBaseSelector = ".work-overview#work-overview-" + delta;
  if ($(oBaseSelector).length == 0 || $(oBaseSelector).length == "null" || $(oBaseSelector).length == null) {
    // we add a new div for the overview - TODO
    
    var output = "";
    output += "<div class='work-overview' id='work-overview-" + delta + "'>";
    output += "<div class='employer'>" + employer + "</div>";
    
    if (position != "null" && position != null && position != "") {
      output += "<div class='position'>" + position + "</div>";
    }  else {
      output += "<div class='position'></div>";
    }
    
    output += datesFormatted;
    output += "</div>";

    $('div.work-items').append(output);

  } else {
    // we change the values in this div
    var oSelectorEmployer = oBaseSelector + " .employer";
    var oSelectorPosition = oBaseSelector + " .position";
    var oSelectorDates = oBaseSelector + " .dates";
    
    $(oSelectorEmployer).html(employer);
    $(oSelectorPosition).html(position);
    $(oSelectorDates).html(datesFormatted);
  }

  // refresh the fancybox content
  enableFancybox($);

}

function copyEducationValues() {


  // get the values from the Fancybox form
  var school = $('.fancybox-overlay input.school').val();
  var year = $('.fancybox-overlay input.year').val();
  var type = $('.fancybox-overlay input.type').val();

  // validation
  if (school == null || school == "null" || school == "") {
    $('.fancybox-overlay input.school').addClass('error');
    return false;
  }
  
  // first, put the values in the correct Symfony form item
  var delta = $('.fancybox-overlay input.delta').val();

  var sBaseSelector = "#form-item-education-" + delta;
  var sSelectorSchool = sBaseSelector + " input.school";
  var sSelectorYear = sBaseSelector + " input.year";
  var sSelectorType = sBaseSelector + " input.type";
  
  $(sSelectorSchool).attr('value', school);
  $(sSelectorYear).attr('value', year);
  $(sSelectorType).attr('value', type);
  
  // second, check if there is already an overview div
  var oBaseSelector = ".education-overview#education-overview-" + delta;
  if ($(oBaseSelector).length == 0 || $(oBaseSelector).length == "null" || $(oBaseSelector).length == null) {
    // we add a new div for the overview - TODO
    
    var output = "";
    output += "<div class='education-overview' id='education-overview-" + delta + "'>";
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

    $('div.education-items').append(output);

  } else {
    // we change the values in this div
    var oSelectorSchool = oBaseSelector + " .school";
    var oSelectorYear = oBaseSelector + " .year";
    var oSelectorType = oBaseSelector + " .type";
    
    $(oSelectorSchool).html(school);
    $(oSelectorYear).html(year);
    $(oSelectorType).html(type);
  }

  // refresh the fancybox content
  enableFancybox($);

}


function formatDates(startdate, enddate) {

  var ret = "<div class='dates'>";
  
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

  ret += dates + "</div>";  
  
  return ret;
}


function deleteWorkItem(delta) {
//  alert('Hier zullen form en overview item ' + delta + ' verwijderd worden');

  // first, remove the symfony form item
  var sSelector = "ul.work li#form-item-work-" + delta;
  $(sSelector).remove();
  
  // second, remove the overview div
  var oSelector = ".work-overview#work-overview-" + delta;
  $(oSelector).remove();  
}

function deleteEducationItem(delta) {
/*   alert('Hier zullen form en overview item ' + delta + ' verwijderd worden'); */

  // first, remove the symfony form item
  var sSelector = "ul.education li#form-item-education-" + delta;
  $(sSelector).remove();
  
  // second, remove the overview div
  var oSelector = ".education-overview#education-overview-" + delta;
  $(oSelector).remove();  
}