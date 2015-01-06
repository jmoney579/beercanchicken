// JavaScript Document
/* -- Class Change Script for Navigation-- */
  $(document).ready(function() {
    $('.inactive_page').hover(function() {
      $(this).addClass('active_page');
    }, function() {
      $(this).removeClass('active_page');
    });
  });
