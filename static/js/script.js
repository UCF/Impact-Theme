// Define globals for JSHint validation:
/* global ga, console */


/**
 * Google Analytics click event tracking.
 * Do not apply the .ga-event-link class to non-link ('<a></a>') tags!
 *
 * interaction: Default 'event'. Used to distinguish unique interactions, i.e. social interactions
 * category:    Typically the object that was interacted with (e.g. button); for social interactions, this is the 'socialNetwork' value
 * action:      The type of interaction (e.g. click) or 'like' for social ('socialAction' value)
 * label:       Useful for categorizing events (e.g. nav buttons); for social, this is the 'socialTarget' value
 **/
function gaEventTracking($) {
  $('.ga-event-link').on('click', function(e) {
    e.preventDefault();

    var $link       = $(this);
    var url         = $link.attr('href');
    var interaction = $link.attr('data-ga-interaction') ? $link.attr('data-ga-interaction') : 'event';
    var category    = $link.attr('data-ga-category') ? $link.attr('data-ga-category') : 'Outbound Links';
    var action      = $link.attr('data-ga-action') ? $link.attr('data-ga-action') : 'click';
    var label       = $link.attr('data-ga-label') ? $link.attr('data-ga-label') : $link.text();
    var target      = $link.attr('target');

    if (typeof ga !== 'undefined' && action !== null && label !== null) {
      ga('send', interaction, category, action, label);
      if (typeof target !== 'undefined' && target === '_blank') {
        window.open(url, '_blank');
      }
      else {
        window.setTimeout(function(){ document.location = url; }, 200);
      }
    }
    else {
      document.location = url;
    }
  });
}


function socialButtonTracking($) {
  $('.social a').click(function() {
    var link = $(this),
      target = link.attr('data-button-target'),
      network = '',
      socialAction = '';

    if (link.hasClass('share-facebook')) {
      network = 'Facebook';
      socialAction = 'Like';
    }
    else if (link.hasClass('share-twitter')) {
      network = 'Twitter';
      socialAction = 'Tweet';
    }
    else if (link.hasClass('share-googleplus')) {
      network = 'Google+';
      socialAction = 'Share';
    }
    else if (link.hasClass('share-linkedin')) {
      network = 'Linkedin';
      socialAction = 'Share';
    }
    else if (link.hasClass('share-email')) {
      network = 'Email';
      socialAction = 'Share';
    }

    if (typeof ga !== 'undefined' && network !== null && socialAction !== null) {
      ga('send', 'social', network, socialAction, window.location);
    }
  });
}


if (typeof jQuery !== 'undefined'){
  jQuery(document).ready(function($) {

    gaEventTracking($);
    socialButtonTracking($);

  });
} else {
  console.log('jQuery dependency failed to load');
}

