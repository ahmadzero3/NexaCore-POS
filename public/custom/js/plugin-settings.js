$(function(){
    "use strict";

    /**
     * Dependency Libraries
     *
     * @iziToast.min.js
     */
    iziToast.settings({
        timeout: 3000, // default timeout
        resetOnHover: true,
        icon: '', // icon class
        transitionIn: 'flipInX',
        transitionOut: 'flipOutX',
        position: 'topRight', // bottomRight, bottomLeft, topRight, topLeft, topCenter, bottomCenter, center
      });

    /**
     * Dependency Libraries
     * Select2 css & js libraries
     *
     * @select2.min.css
     * @select2-bootstrap-5-theme.min.css
     * @select2.min.js
     */
    $( '.single-select-clear-field' ).select2( {
        theme: "bootstrap-5",
        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        placeholder: $( this ).data( 'placeholder' ),
        allowClear: true
    } );

    /**
     * Dependency Libraries
     * Select2 css & js libraries
     *
     * @select2.min.css
     * @select2-bootstrap-5-theme.min.css
     * @select2.min.js
     */
    $( '.single-select-field' ).select2( {
         theme: "bootstrap-5",
        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        placeholder: $( this ).data( 'placeholder' ),
    } );

    /**
     *
     * */
    $( '.multiple-select-clear-field' ).select2( {
        theme: "bootstrap-5",
        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        placeholder: $( this ).data( 'placeholder' ),
        closeOnSelect: false,
        allowClear: true,
    } );

    /**
     * Dependency Libraries
     * Start: Date & Time Picker Settings
     *
     * @flatpickr.min.css
     * @flatpickr.min.js
     * */
    $(".datepicker").flatpickr({
        dateFormat: dateFormatOfApp,//Defined in script.js
        defaultDate: new Date(),
    });

    $(".datepicker-edit").flatpickr({
        dateFormat: dateFormatOfApp,//Defined in script.js
    });

    $('.datepicker-month-first-date').flatpickr({
        defaultDate : new Date(new Date().getFullYear(), new Date().getMonth(), 1),
        dateFormat: dateFormatOfApp,//Defined in script.js
    });

    $(".time-picker").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "Y-m-d H:i",
        });

    $(".date-time").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
    });

    $(".date-format").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    $(".date-range").flatpickr({
        mode: "range",
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    $(".date-inline").flatpickr({
        inline: true,
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    /**
     * Initialize Tooltip
     * */
    setTooltip();
});

function toggleSidebar() {
    $(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function() {
                $(".wrapper").addClass("sidebar-hovered")
            }, function() {
                $(".wrapper").removeClass("sidebar-hovered")
            }))
}

function setTooltip(){
    $('[data-bs-toggle="popover"]').popover();
    $('[data-bs-toggle="tooltip"]').tooltip();
}
