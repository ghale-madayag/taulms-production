(function() {
    'use strict';
    // ENABLE sidebar menu tabs
    $('.js-sidebar-mini-tabs [data-toggle="tab"]').on('click', function(e) {
        e.preventDefault()
        $(this).tab('show')
    })
    $('.js-sidebar-mini-tabs').on('show.bs.tab', function(e) {
        $('.js-sidebar-mini-tabs > .active').removeClass('active')
        $(e.target).parent().addClass('active')
    })
})()
