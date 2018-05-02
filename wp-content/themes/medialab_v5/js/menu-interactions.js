



$(document).ready(function(){
  var searchVisible = false;
  var form = $('#searchbar form');
  var srcBtn = $('#searchbar-toggle');
  srcBtn.remove();
  var btn = srcBtn.insertBefore(form);
  form.css({maxWidth: 0, width: 0});

  srcBtn.on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    
    if (searchVisible) {
      searchVisible = false;
      form.css({maxWidth: 0, width: 0})
    } else {
      searchVisible = true;
      form.css({width: '100%', maxWidth: '100%'})
    }
  })

});