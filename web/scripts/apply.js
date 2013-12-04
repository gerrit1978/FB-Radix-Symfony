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
  $('ul.work').append($newLinkLi);
  
  $('ul.education').append($newLinkEducationLi);

  $('ul.work').find('li').each(function() {
    addFormDeleteLink($(this));
  });
  
  $('ul.education').find('li').each(function() {
    addFormDeleteLink($(this));
  });

  
  $addWorkLink.on('click', function(e) {
    e.preventDefault();
    addWorkForm($('ul.work'), $newLinkLi);
  });

  $addEducationLink.on('click', function(e) {
    e.preventDefault();
    addEducationForm($('ul.education'), $newLinkEducationLi);
  });
  
});

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