$(document).ready(function(){
  // flash error box
  $('#status').animate({ backgroundColor: 'rgb(187,0,0)' }, 30).animate({ backgroundColor: 'rgb(255,238,221)' }, 500);

  // flash success box
  $('#msg').animate({ backgroundColor: 'rgb(51,204,0)' }, 30).animate({ backgroundColor: 'rgb(221,255,170)' }, 500);

  // bind event handlers
  $('button').mousedown(function() {
    $(this).addClass('button-down');
  });
  $('button').mouseup(function() {
    $(this).removeClass('button-down');
  });
  $('button').mouseleave(function() {
    $(this).removeClass('button-down');
  });

  $('.hintable').focus(showHint);
  $('.hintable').blur(function() {
    $(this).parent().children('.field-hint').hide();
  });

  //focus first input form field on page
  $("input:visible:enabled:first").focus();
});

function popup(url, name) {
  new_win = window.open(url, name, 'top=100,left=100,height=500,width=800,scrollbars=1,location=1,toolbar=1');
  if (window.focus) {
    new_win.focus()
  }
  return false;
}

function showHint() {
  var padding = 3;
  var hint = $(this).parent().children('.field-hint');
  var x = $(this).offset().left + padding;
  var y = $(this).offset().top - hint.outerHeight() - padding;
  hint.css({
    position:'absolute',
    left:x + 'px',
    top:y + 'px'
  }).show();
}

function swf_submit_handler(e) {
    e.data.form.append(
        $('<input>')
            .attr('type', 'hidden')
            .attr('name', '_eventId')
            .val(e.data.eventId)).submit();
}
