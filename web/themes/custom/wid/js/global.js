/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function (context, settings) {
      var position = $(window).scrollTop();
      $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
          $('body').addClass("scrolled");
        } else {
          $('body').removeClass("scrolled");
        }
        var scroll = $(window).scrollTop();
        if (scroll > position) {
          $('body').addClass("scrolldown");
          $('body').removeClass("scrollup");
        } else {
          $('body').addClass("scrollup");
          $('body').removeClass("scrolldown");
        }
        position = scroll;
      });

      // Track file download
      $('.file--public a').once('download-count-public').each(function () {
        $(this).attr('target', '_blank').click(function (e) {
          // Get parent element.
          let $parent = $(this).parent();
          // Get data.
          let entityType = $parent.data('entityType');
          let entityId = $parent.data('entityId');
          let fileId = $parent.data('fileId');
          if (entityType && entityId && fileId) {
            // Build track url.
            let trackUrl = settings.path.baseUrl + 'download-count/' + entityType + '/' + entityId + '/' + fileId + '/track';
            $.ajax(trackUrl);
            return true;
          }
        });
      });
    }
  };

  function styleSearch() {
    $('.js-form-item-search-api-fulltext')
      .append("<span class='fa fa-search form-control-feedback' style=' position: absolute; top: 12px; left: 20px; color: #556cff; '></span>");
  }

  var accordion = document.getElementsByClassName("faq-accordion");
  var accordionItem;
  for (accordionItem = 0; accordionItem < accordion.length; accordionItem++) {
    accordion[accordionItem].addEventListener("click", function () {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    });
  }

  $('#toolkitTestimonialSlide').find('.carousel-item').first().addClass('active');
  $('#toolkitTestimonialSlideIndicator').find('.item-indicator').first().addClass('active');

  styleSearch();

  // window.onscroll = function() {
  //   scrollFunction()
  // };
  // function scrollFunction() {
  //   if (document.body.scrollTop >= 20 || document.documentElement.scrollTop >= 20) {
  //     document.getElementById("navbar-main").style.padding = "5px 20px";
  //   } else {
  //     document.getElementById("navbar-main").style.padding = "25px 20px";
  //   }
  // }

  $(".owl-carousel").owlCarousel({
    margin: 20,
    mouseDrag: false,
    dots: false,
    nav: true,
    navText: [
      `
        <div class="carousel-card-button carousel-card-button-left">
          <i class="ion-android-arrow-back"></i>
        </div>
      `,
      `
        <div class="carousel-card-button carousel-card-button-right">
          <i class="ion-android-arrow-forward"></i>
        </div>
      `
    ],
    responsive: {
      0: {
        items: 1,
      },
      480: {
        items: 1,
      },
      768: {
        items: 2,
      },
      1024: {
        items: 3,
      }
    }
  });

  $('.approach-container .approach-steps').on('click', 'li', function () {
    const cardIndex = $(this).attr('class').split('-').pop();
    $(`.approach-container .toolkit-steps-wrapper .steps-card:not(#card-${cardIndex})`).css("display", "none");
    $(`.approach-container .toolkit-steps-wrapper #card-${cardIndex}`).css("display", "block");
  });

  // const downloadsData = [10, 20, 30, 40, 50, 60, 70, 30, 20, 12, 124, 30];
  const ctx = $('#downloads-graph');
  let downloadsData = {};

  function downloadsGraph(downloadsData) {
    const downloadsGraph = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
        datasets: [{
          label: 'Downloads',
          backgroundColor: '#553ba8',
          barPercentage: 0.5,
          data: downloadsData,
        }]
      },
      options: {
        scales: {
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: "Month",
              fontFamily: "'Open Sans', sans-serif",
              fontSize: 13,
              fontColor: "#272727"
            },
            gridLines: {
              drawOnChartArea: false,
            }
          }],
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: "Downloads",
              fontFamily: "'Open Sans', sans-serif",
              fontSize: 13,
              fontColor: "#272727"
            },
            gridLines: {
              drawOnChartArea: false,
            },
          }]
        },
        legend: {
          display: false,
        },
        tooltips: {
          callbacks: {
            label: tooltipItem => ` ${tooltipItem.yLabel} downloads`,
            title: () => null,
          }
        },
      },
    });
  }

  if (ctx.length) {
    $.ajax({
      url: Drupal.url('') + 'download-count',
      method: "GET",
      success: function (data) {
        let downloadsData = data;
        downloadsGraph(downloadsData)
      }
    });
  }

  $('.single-event-list').on('click', function () {
    $(this).toggleClass("active");
  });

  $('.view-search').addClass('container');

  $("#login, #login-link").click(function () {
    $(".login-container").css("display", "block");
    $(".signup-container").css("display", "none");
    $("#login").addClass("active");
    $("#register").removeClass("active");
  });
  $("#register, #register-link").click(function () {
    $(".login-container").css("display", "none");
    $(".signup-container").css("display", "block");
    $("#register").addClass("active");
    $("#login").removeClass("active");
  });
})(jQuery, Drupal);
