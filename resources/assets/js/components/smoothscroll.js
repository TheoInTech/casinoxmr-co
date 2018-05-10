$(document).ready(function(){
  $('[role~="nav-link"]').on('click',function (e) {
      e.preventDefault();

      var target = this.hash,
      $target = $(target);

      $('html, body').animate({
        'scrollTop': $target.offset().top - 50
      }, 900, 'swing', function () {
        window.location.hash = target;
      });
  });

  $('[role~="nav-link-products"]').on('click',function (e) {
      e.preventDefault();

      var target = this.hash,
      $target = $(target);

      $('html, body').animate({
        'scrollTop': 490
      }, 900, 'swing', function () {
        window.location.hash = target;
      });
  });
});

