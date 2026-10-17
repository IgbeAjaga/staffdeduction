/*=========================================================================================
    File Name: pickers.js
    Description: Pick a date/time Picker, Date Range Picker JS
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';

  /*******  Flatpickr  *****/
  var basicPickr = $('.flatpickr-basic'),
    timePickr = $('.flatpickr-time'),
    dateTimePickr = $('.flatpickr-date-time'),
    multiPickr = $('.flatpickr-multiple'),
    rangePickr = $('.flatpickr-range'),
    rangeFromTodayPickr = $('.flatpickr-today-range'),
    humanFriendlyPickr = $('.flatpickr-human-friendly'),
    disabledRangePickr = $('.flatpickr-disabled-range'),
    inlineRangePickr = $('.flatpickr-inline');

  // Default
  if (basicPickr.length) {
    basicPickr.flatpickr({
      minDate: 'today'
    });
  }

  // Time
  if (timePickr.length) {
    timePickr.flatpickr({
      enableTime: true,
      noCalendar: true
    });
  }

  // Date & TIme
  if (dateTimePickr.length) {
    dateTimePickr.flatpickr({
      enableTime: true
    });
  }

  // Multiple Dates
  if (multiPickr.length) {
    multiPickr.flatpickr({
      weekNumbers: true,
      mode: 'multiple',
      minDate: 'today'
    });
  }

  // Range
  if (rangeFromTodayPickr.length) {
    rangeFromTodayPickr.flatpickr({
       mode: "range",
      minDate: "today",
      dateFormat: "Y-m-d"
    });
  }

  if (rangePickr.length) {
    rangePickr.flatpickr({
      mode: 'range'
    });
  }

  // Human Friendly
  if (humanFriendlyPickr.length) {
    humanFriendlyPickr.flatpickr({
      altInput: true,
      altFormat: 'F j, Y',
      dateFormat: 'Y-m-d'
    });
  }

  // Disabled Range
  if (disabledRangePickr.length) {
    disabledRangePickr.flatpickr({
      dateFormat: 'Y-m-d',
      disable: [
        {
          from: new Date().fp_incr(2),
          to: new Date().fp_incr(7)
        }
      ]
    });
  }

  // Inline
  if (inlineRangePickr.length) {
    inlineRangePickr.flatpickr({
      inline: true
    });
  }
  /*******  Pick-a-date Picker  *****/
  // Basic date
  if($('.pickadate').length){
    $('.pickadate').pickadate();
  }

  // Format Date Picker
  if($('.format-picker').length){
    $('.format-picker').pickadate({
      format: 'mmmm, d, yyyy'
    });
  }

  // Date limits
  if($('.pickadate-limits').length){
    $('.pickadate-limits').pickadate({
      min: [2019, 3, 20],
      max: [2019, 5, 28]
    });
  }

  // Disabled Dates & Weeks
  if($('.pickadate-disable').length){
    $('.pickadate-disable').pickadate({
      disable: [1, [2019, 3, 6], [2019, 3, 20]]
    });
  }

  // Picker Translations
  if($('.pickadate-translations').length){
    $('.pickadate-translations').pickadate({
      formatSubmit: 'dd/mm/yyyy',
      monthsFull: [
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre'
      ],
      monthsShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
      weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
      today: "aujourd'hui",
      clear: 'clair',
      close: 'Fermer'
    });
  }

  // Month Select Picker
  if($('.pickadate-months').length){
    $('.pickadate-months').pickadate({
      selectYears: false,
      selectMonths: true
    });
  }

  // Month and Year Select Picker
  if($('.pickadate-months-year').length){
    $('.pickadate-months-year').pickadate({
      selectYears: true,
      selectMonths: true
    });
  }

  // Short String Date Picker
  if($('.pickadate-short-string').length){
    $('.pickadate-short-string').pickadate({
      weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
      showMonthsShort: true
    });
  }

  // Change first weekday
  if($('.pickadate-firstday').length){
    $('.pickadate-firstday').pickadate({
      firstDay: 1
    });
  }

  /*******    Pick-a-time Picker  *****/
  // Basic time
  if($('.pickatime').length){
    $('.pickatime').pickatime();
  }

  // Format options
  if($('.pickatime-format').length){
    $('.pickatime-format').pickatime({
      // Escape any “rule” characters with an exclamation mark (!).
      format: 'T!ime selected: h:i a',
      formatLabel: 'HH:i a',
      formatSubmit: 'HH:i',
      hiddenPrefix: 'prefix__',
      hiddenSuffix: '__suffix'
    });
  }

  // Format options
  if($('.pickatime-formatlabel').length){
    $('.pickatime-formatlabel').pickatime({
      formatLabel: function (time) {
        var hours = (time.pick - this.get('now').pick) / 60,
          label = hours < 0 ? ' !hours to now' : hours > 0 ? ' !hours from now' : 'now';
        return 'h:i a <sm!all>' + (hours ? Math.abs(hours) : '') + label + '</sm!all>';
      }
    });
  }

  // Min - Max Time to select
  if($('.pickatime-min-max').length){
    $('.pickatime-min-max').pickatime({
      // Using Javascript
      min: new Date(2015, 3, 20, 7),
      max: new Date(2015, 7, 14, 18, 30)

      // Using Array
      // min: [7,30],
      // max: [14,0]
    });
  }

  // Intervals
  if($('.pickatime-intervals').length){
    $('.pickatime-intervals').pickatime({
      interval: 150
    });
  }

  // Disable Time
  if($('.pickatime-disable').length){
    $('.pickatime-disable').pickatime({
      disable: [
        // Disable Using Integers
        3,
        5,
        7,
        13,
        17,
        21

        /* Using Array */
        // [0,30],
        // [2,0],
        // [8,30],
        // [9,0]
      ]
    });
  }

  // Close on a user action
  if($('.pickatime-close-action').length){
    $('.pickatime-close-action').pickatime({
      closeOnSelect: false,
      closeOnClear: false
    });
  }
})(window, document, jQuery);
