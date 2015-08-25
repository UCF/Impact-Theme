// @codekit-prepend "../bower_components/bootstrap-sass-official/vendor/assets/javascripts/bootstrap.js";
// @codekit-prepend "generic-base.js";

// Define globals for JSHint validation:
/* global ga, console */


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

    socialButtonTracking($);

  });
} else {
  console.log('jQuery dependency failed to load');
}

