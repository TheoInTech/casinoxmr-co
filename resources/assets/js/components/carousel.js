$(document).ready(function() {
    $('#cards-carousel').carousel({
      interval: 5000
    })

    $('.carousel .item').each(function(){
        var itemToClone = $(this);
        var show = $('.carousel').data('show');

        for (var i=1;i<show;i++) {
          itemToClone = itemToClone.next();

          // wrap around if at end of item collection
          if (!itemToClone.length) {
            itemToClone = $(this).siblings(':first');
          }

          // grab item, clone, add marker class, add to collection
          itemToClone.children(':first-child').clone()
            .addClass("cloneditem-"+(i))
            .appendTo($(this));
        }
    });
});