

$(document).ready(function(){
  var mainNews = $('#news-container #hero-news');
  // set first item as active
  $('#news-container #list-container .news-card:first-of-type').addClass('active');
  mainNews.append($('#news-container #list-container .news-card:first-of-type').html());
  $('#news-container #list-container .news-card')
    .hover(function(event) {
      $('#news-container #list-container .news-card').removeClass('active');
      $(this).addClass('active');
      mainNews.empty();
      mainNews.append($(this).html());
    })
});