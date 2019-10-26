var $W = $(window),
    $D = $(document),
    $B = $('body'),
    $HB = $('html, body'),
    isMobileView;

function mobChecker(maxWinWidth) {
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent) ||
      window.matchMedia('(max-width: ' + maxWinWidth + 'px)').matches ||
      $D.width() < maxWinWidth) {
    return true;
  }
  else {
    return false;
  }
}

isMobileView = mobChecker(1024);

/*--- MAPS -------------------------------------------------------------------*/
var google,
    mapStyles = [
      {
        'featureType': 'landscape',
        'elementType': 'labels',
        'stylers': [{'visibility': 'off'}],
      },
      {
        'featureType': 'transit',
        'elementType': 'labels',
        'stylers': [{'visibility': 'off'}],
      },
      {
        'featureType': 'poi',
        'elementType': 'labels',
        'stylers': [{'visibility': 'off'}],
      },
      {
        'featureType': 'water',
        'elementType': 'labels',
        'stylers': [{'visibility': 'off'}],
      },
      {
        'featureType': 'road',
        'elementType': 'labels.icon',
        'stylers': [{'visibility': 'off'}],
      },
      {
        'stylers': [
          {'hue': '#00aaff'},
          {'saturation': -100},
          {'gamma': 2.15},
          {'lightness': 12}],
      },
      {
        'featureType': 'road',
        'elementType': 'labels.text.fill',
        'stylers': [{'visibility': 'on'}, {'lightness': 24}],
      },
      {
        'featureType': 'road',
        'elementType': 'geometry',
        'stylers': [{'lightness': 57}],
      },
    ];

/*--- MAPS: Map on Main page -------------------------------------------------*/
function secMapStart() {
  console.log('Map start');

  var myMapPlace = document.getElementById('section-map'),
      pointData = $(myMapPlace).data('point') || '',
      mapCenter = new google.maps.LatLng(pointData.lat, pointData.lng);

  var mapOptions = {
    disableDefaultUI: true,
    scrollwheel: false,
    zoom: 17,
    center: mapCenter,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    styles: mapStyles,
  };

  var map = new google.maps.Map(myMapPlace, mapOptions);

  // Create and to map marker
  var pointLabel = new google.maps.MarkerImage('/assets/img/map_point.png',
      new google.maps.Size(50, 68),
      new google.maps.Point(0, 0),
      new google.maps.Point(25, 68)
  );
  var pointMarker = new google.maps.Marker({
    position: new google.maps.LatLng(pointData.lat, pointData.lng),
    map: map,
    icon: pointLabel,
    title: pointData.name,
  });

  // Custom zoom create
  var controlWrapper = document.createElement('div'),
      zoomInButton = document.createElement('div'),
      zoomOutButton = document.createElement('div');

  controlWrapper.appendChild(zoomInButton);
  controlWrapper.appendChild(zoomOutButton);

  controlWrapper.index = 1;
  zoomInButton.className = 'map-zoom__more';
  zoomOutButton.className = 'map-zoom__less';

  // Setup the click event listener - zoomIn
  google.maps.event.addDomListener(zoomInButton, 'click', function() {
    map.setZoom(map.getZoom() + 1);
  });
  // Setup the click event listener - zoomOut
  google.maps.event.addDomListener(zoomOutButton, 'click', function() {
    map.setZoom(map.getZoom() - 1);
  });
  // Add on map
  map.controls[google.maps.ControlPosition.LEFT_CENTER].push(controlWrapper);
}

/* --- FUNCTION: Start maps if it is ------------------------------------*/
function mapStart() {
  if ($('#section-map').length) secMapStart();
}

/*----------- FUNCTIONS AFTER READY ------------------------------------------*/
$D.ready(function() {


  /*--- Scrollbar width -------------------------------------------------*/
  var scrollWidth = 0;

  function getScroll() {
    var div = document.createElement('div');

    div.style.overflowY = 'scroll';
    div.style.width = '50px';
    div.style.height = '50px';
    div.style.visibility = 'hidden';

    document.body.appendChild(div);
    scrollWidth = div.offsetWidth - div.clientWidth;
    document.body.removeChild(div);
  }

  getScroll();

  /*--- FUNCTION: Scroll page to top ------------------------------------*/
  $.fn.scrollToTop = function() {
    var scrollLink = $(this);

    scrollLink.hide();
    if ($W.scrollTop() >= '150') scrollLink.fadeIn('slow');
    $W.scroll(function() {
      if ($W.scrollTop() <= '150') scrollLink.fadeOut('slow');
      else scrollLink.fadeIn('slow');
    });
    $(this).click(function() {
      $HB.velocity('scroll',
          {offset: 0, duration: 2000, easing: 'easeOutCubic'});
    });
  };
  // Scroll page to top init
  $('.js-scroll-top').scrollToTop();

  /*--- FUNCTION: Scroll to section -------------------------------------*/
  $('.js-scroll-to').click(function(e) {
    var $link = $(this),
        $target = $($link.attr('href') || $link.data('scroll')),
        targetTop;

    if ($target.length) {
      e.preventDefault();
      targetTop = $target.offset().top;
      $HB.velocity('scroll',
          {offset: targetTop, duration: 1000, easing: 'easeOutCubic'});
    }
    return false;
  });

  /*--- FUNCTION: Click ripple effect -----------------------------------*/
  $.fn.rippleEffect = function(ratio) {
    var $that = $(this);

    if (!ratio) ratio = 0.8;

    $that.click(function(e) {
      var $ripple = $(this),
          effect, d, x, y;

      if ($ripple.find('.effect').length == 0) $ripple.append(
          '<span class=\'effect\'></span>');

      effect = $ripple.children('.effect');
      effect.removeClass('replay');

      if (!effect.height() && !effect.width()) {
        d = Math.max($ripple.outerWidth(), $ripple.outerHeight());
        effect.css({height: d * ratio, width: d * ratio});
      }
      x = e.pageX - $ripple.offset().left - effect.width() / 2;
      y = e.pageY - $ripple.offset().top - effect.height() / 2;

      effect.css({top: y + 'px', left: x + 'px'}).addClass('replay');
    });
  };
  // Ripple effect init
  $('.btn').rippleEffect(1.0);

  /*--- FUNCTION: Youtube video play ------------------------------------*/
  $D.on('click', '.js-youtube-init', function() {
    var $that = $(this),
        youtubeData = $that.data('youtube'),
        youtubeElem;

    function youtubeRun() {
      youtubeElem = '<iframe class="video-unit__youtube" width="740" height="480" src="https://www.youtube.com/embed/' +
          youtubeData +
          '?rel=0&autoplay=1&showinfo=0&rel=0&iv_load_policy=3"  allowfullscreen ></iframe>';
      $that.before(youtubeElem);
      $that.addClass('is-hidden');
    }

    if (youtubeData) {
      youtubeRun();

      if ($('.sec-expert__worker').length) {
        $('.sec-expert__worker').addClass('is-hidden');
      }

      return this;
    } else return false;
  });

  /*--- FUNCTION: Local video play --------------------------------------*/
  $D.on('click', '.js-video-init', function() {
    var $btn = $(this),
        $video = $btn.siblings('.video-unit__local')[0];

    if ($video) {
      $btn.addClass('is-hidden');
      $video.load();
      $video.play();
    } else return false;
  });

  /*--- FUNCTION: Hide Header on scroll down ---------------------------------*/
  var didScroll,
      lastScrollTop = 0,
      delta = 5,
      $navbar = $('#header-line'),
      navbarHeight = $navbar.outerHeight();

  $W.scroll(function() {
    didScroll = true;
  });

  setInterval(function() {
    if (didScroll) {
      hasScrolled();
      didScroll = false;
    }
  }, 250);

  function hasScrolled() {
    var st = $(this).scrollTop();

    if (Math.abs(lastScrollTop - st) <= delta) return;
    if (st > 10) {
      $navbar.addClass('is-scrolled');
    } else {
      $navbar.removeClass('is-scrolled');
    }
    if (st > lastScrollTop && st > navbarHeight * 2) {
      $navbar.removeClass('nav-down').addClass('nav-up');
    } else {
      if (st + $W.height() < $D.height()) {
        $navbar.removeClass('nav-up').addClass('nav-down');
      }
    }
    lastScrollTop = st;
  }

  /* --- More reviews loading -------------------------------------------*/
  var reviewsLoading = false,
      noMoreReviews = false,
      reviewsPage = 1;

  // Load more reviews
  $('#js-auto-load-reviews').viewportChecker({
    offset: 100,
    repeat: true,
    callbackFunction: function() {
      if (reviewsLoading || noMoreReviews) return;

      var $loadingPlace = $('#for-load-reviews'),
          $loadingBtn = $('#js-auto-load-reviews'),
          template = $loadingPlace.data('url'),
          placeholder = $loadingPlace.data('placeholder');

      var url = template.replace(placeholder, ++reviewsPage);

      $loadingBtn.addClass('is-loading');
      reviewsLoading = true;
      $.ajax({
        type: 'GET',
        url: url,
        dataType: 'html',
        cache: false,
        statusCode: {
          204: function() {
            noMoreReviews = true;
          },
        },
        error: function() {
          console.log('Error loading more');
          $loadingBtn.removeClass('is-loading');
          reviewsLoading = false;
        },
        success: function(ajaxHtml) {
          console.log('Success loading more');

          var $tempContainer = $('#temp-container'),
              loadingPlace = $loadingPlace[0],
              new_items = []; // array to contain new DOM elements

          $tempContainer.html(ajaxHtml);
          $tempContainer.children('.just-loaded').each(function() {
            new_items.push($(this)[0]); // use [0] to get at the DOM element
          });
          $tempContainer.html('');
          // run salvattore update
          salvattore.appendElements(loadingPlace, new_items);

          setTimeout(function() {
            $loadingPlace.find('.just-loaded').removeClass('just-loaded');
            $loadingBtn.removeClass('is-loading');
            reviewsLoading = false;
          }, 100);
        },
      });
    },
  });

  /* --- More blog-cards loading -----------------------------------------*/
  var blogLoading = false;

  function blogAjaxLoad(loadingBtn, loadingPlace, addr) {
    var $loadingBtn = $(loadingBtn),
        $loadingPlace = $(loadingPlace);

    if ($loadingPlace.length && reviewsLoading === false) {
      $loadingBtn.addClass('is-loading');
      blogLoading = true;

      $.ajax({
        type: 'POST',
        url: addr,
        dataType: 'html',
        cache: false,
        error: function() {
          console.log('Error loading more');
          $loadingBtn.removeClass('is-loading');
          blogLoading = false;
        },
        success: function(ajaxHtml) {
          console.log('Success loading more');
          $loadingPlace.append(ajaxHtml);

          setTimeout(function() {
            $loadingPlace.find('.just-loaded').removeClass('just-loaded');
            $loadingBtn.removeClass('is-loading');
            blogLoading = false;
          }, 100);
        },
      });
    }
  }

  // Load more blog-cards
  $('#js-auto-load-blog').viewportChecker({
    offset: 100,
    repeat: true,
    callbackFunction: function() {
      if (blogLoading) return;
      blogAjaxLoad('#js-auto-load-blog', '#for-load-blog-cards',
          'ajax/more_blog-cards.php');
    },
  });

  /* --- FUNCTION: Counter ------------------------------------------------*/
  $.fn.counterInit = function() {
    var $that = $(this),
        $btnMore = $that.children('.counter__btn_more'),
        $btnLess = $that.children('.counter__btn_less'),
        $input = $that.children('.counter__val'),
        maxVal = +$that.data('max') || 99999,
        stepSize = +$that.data('step') || 1;

    $btnMore.click(function() {
      var $counterMax = maxVal,
          $inputVal = parseInt($input.val()) || 0;
      if ($counterMax > 0 && $inputVal < $counterMax) {
        $input.val($inputVal + stepSize).change();
      }
    });

    $btnLess.click(function() {
      var count = (parseInt($input.val()) || 0) - stepSize;
      count = count < 0 ? 0 : count;
      $input.val(count).change();
    });

    $input.on('blur', function() {
      $(this).val(parseInt($(this).val()) || 0);
    });
  };
  // Counter init
  $('.js-counter').each(function(index, elem) {
    $(elem).counterInit();
  });

  /*--- Selector --------------------------------------------------------*/
  $D.on('click', '.selector__val', function() {
    $(this).closest('.selector').addClass('show-list');
  });
  $D.on('mouseleave', '.selector__inner', function() {
    $(this).closest('.selector').removeClass('show-list');
  });
  $D.on('mouseenter', '.selector__drop', function() {
    $(this).closest('.selector').addClass('show-list');
  });
  $D.on('mouseleave', '.selector__drop', function() {
    //$(this).closest(".selector").removeClass("show-list");
  });
  $D.on('click', '.selector__list li', function() {
    var $item = $(this),
        selDataVal = $item.data('val'),
        selDataTxt = $item.text();
    $item.addClass('is-active').siblings('li').removeClass('is-active');
    $item.closest('.selector__drop').
        siblings('div').
        text(selDataTxt).
        attr({'data-val': selDataVal});
    $item.closest('.selector__drop').
        siblings('input').
        val(selDataVal).
        trigger('change');
    $item.closest('.selector').removeClass('show-list');
  });

  //Закрытие списка селектора при клике мимо
  $D.mouseup(function(e) {
    var selectorList = $('.selector.show-list');
    if (e.target !== selectorList[0] && !selectorList.has(e.target).length) {
      selectorList.removeClass('show-list');
    }
  });

  //Выставление правильного предзаданного значения в селекте
  $W.load(function() {
    $('.selector').each(function() {
      var $that = $(this),
          inputVal = $that.find('input').val(),
          selectedText,
          $selectedItem;
      if (inputVal) {
        $selectedItem = $that.find('li[data-val="' + inputVal + '"]');
        selectedText = $selectedItem.text();
        $selectedItem.addClass('is-active').
            siblings('li').
            removeClass('is-active');
      }
      if (selectedText) $that.find('.selector__val').
          text(selectedText).
          attr('data-val', inputVal);
      console.log(selectedText);
    });
  });

  /*--- Input masks -----------------------------------------------------*/
  $.mask.definitions['H'] = '[0-2]';
  $.mask.definitions['h'] = '[1-9]';
  $.mask.definitions['M'] = '[0-5]';
  $.mask.definitions['m'] = '[0-9]';

  $('.js-phone-mask').mask('+7(999)999-99-99', {placeholder: '_'});
  $('.js-date-mask').mask('99.99.9999', {placeholder: '_'});
  $('.js-time-mask').mask('Hh:Mm', {placeholder: '_'});

  /*--- Forms validation ------------------------------------------------*/
  $('.js-validate').each(function() {
    $(this).validate({
      focusInvalid: false,
      rules: {
        name: {required: true},
        mail: {required: true, email: true},
        numb: {required: true},
        text: {required: true},
        login: {required: true},
        pass: {required: true},
        captcha: {required: true},
      },
      messages: {
        name: '',
        mail: '',
        numb: '',
        text: '',
        login: '',
        pass: '',
        captcha: '',
      },
      errorClass: 'input-error',
      highlight: function(element, errorClass, validClass) {
        $(element).parent().addClass(errorClass);
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).parent().removeClass(errorClass);
      },
      submitHandler: function(form) {
        if ($(form).hasClass('popup-form')) $(form).
            hide().
            siblings('.js-form-ok').
            show();
        //alert("Submitted!");
        //form.submit();
      },
    });
  });

  /* --- Youtube video firing ----------------------------------------------*/
  if ($('.js-popbox-youtube').length) youtubePlugIn();

  function youtubePlugIn() {
    var tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;

    function onYouTubeIframeAPIReady() {
      console.log('onYouTubeIframeAPI Ready');
    }
  }

  /*--- Pop-up window ------------------------------------------------------*/
  setTimeout(function() {
    $B.removeClass('no-start-animations');
  }, 500);

  function popBoxStart(popId) {
    var $thisPopbox = $(popId),
        $popYoutube = $thisPopbox.find('.js-popbox-youtube'),
        $closeBtn = $thisPopbox.find('.js-popbox-close').addBack(),
        youtubePlayer, youtubeSource;

    $(popId).addClass('is-inited is-active');
    $B.css('padding-right', scrollWidth).addClass('is-trimmed');
    $('#header-line').css('padding-right', scrollWidth);

    if ($popYoutube.length) {

      youtubeSource = $popYoutube.data('yt-source');

      youtubePlayer = new YT.Player($popYoutube[0], {
        videoId: youtubeSource, // YouTube Video ID
        width: 640,               // Player width (in px)
        height: 360,              // Player height (in px)
        playerVars: {
          autoplay: 1,        // Auto-play the video on load
          controls: 1,        // Show pause/play buttons in player
          showinfo: 0,        // Hide the video title
          modestbranding: 0,  // Hide the Youtube Logo
          playlist: youtubeSource,
          loop: 0,            // Run the video in a loop
          rel: 0,
          fs: 1,              // Show the full screen button
          cc_load_policy: 0,  // Hide closed captions
          iv_load_policy: 3,  // Hide the Video Annotations
          autohide: 1,         // Hide video controls when playing
        },
        events: {
          'onReady': onYtPlayerReady,
        },
      });

      function onYtPlayerReady(event) {
        //event.target.playVideo();
      }
    }

    $closeBtn.click(function() {
      if (youtubePlayer) {
        youtubePlayer.destroy();
        youtubePlayer = '';
      }
    });
  }

  function popUpClose() {
    var $popbox = $('.popup');

    $popbox.removeClass('is-active');

    setTimeout(function() {
      $popbox.removeClass('is-inited');
      $B.css('padding-right', '').removeClass('is-trimmed');
      $('#header-line').css('padding-right', '');
    }, 300);
  }

  $D.on('click', '.js-popup-close, .popup', function(e) {
    if (e.target === this) {
      popUpClose();
    }
  });

  $('.js-show-popup').click(function() {
    popBoxStart('#' + $(this).data('pop'));
  });

  /*--- FUNCTIONS: Tabs -------------------------------------------------*/
  $.fn.tabsInit = function(tabsBody) {
    var $tabsHead = $(this),
        $buttons = $tabsHead.find('.js-tabs-btn'),
        $tabsBody,
        $tabs;

    if (tabsBody) {
      $tabsBody = $(tabsBody);
    } else {
      $tabsBody = $tabsHead.siblings('.js-tabs-body');
    }
    $tabs = $tabsBody.find('.js-tabs-item');

    $buttons.click(function() {
      var tabId = '#' + $(this).data('tab');
      $buttons.removeClass('is-active');
      $(this).addClass('is-active');
      $tabs.removeClass('is-active');
      $(tabId).addClass('is-active');
    });
    return this;
  };
  // Tabs init:
  $('#sec-faq-tabs').tabsInit();
  $('#sec-delivery-tabs').tabsInit();

  /*--- Parallax in Features section init -------------------------------*/
  var $features = $('#sec-features-back');
  if ($features.length) {
    var SECFEAT = {
      $ltMin: $('.sec-features__lt-img_min'),
      $ltBig: $('.sec-features__lt-img_big'),
      $ltBlur: $('.sec-features__lt-img_blur'),
      $rtMin: $('.sec-features__rt-img_min'),
      $rtBig: $('.sec-features__rt-img_big'),
      $rtBlur: $('.sec-features__rt-img_blur'),
    };

    if (isMobileView) {

      function featuresParr() {
        var winScroll = $W.scrollTop(),
            winHeight = $W.height(),
            secTop = $features.offset().top || 0,
            startLevel, stopLevel, thisOffset;

        startLevel = secTop - winHeight;
        stopLevel = secTop + winHeight;
        if (winScroll < startLevel || winScroll > stopLevel) return;
        thisOffset = winScroll - startLevel;

        TweenLite.to(SECFEAT.$ltMin, 2,
            {y: thisOffset / 8, force3D: true, delay: 0.1});
        TweenLite.to(SECFEAT.$ltBig, 2,
            {y: thisOffset / 5, force3D: true, delay: 0.1});
        TweenLite.to(SECFEAT.$ltBlur, 2,
            {y: thisOffset / 2, force3D: true, delay: 0.1});
        TweenLite.to(SECFEAT.$rtMin, 2,
            {y: thisOffset / 7, force3D: true, delay: 0.1});
        TweenLite.to(SECFEAT.$rtBig, 2,
            {y: thisOffset / 3, force3D: true, delay: 0.1});
        TweenLite.to(SECFEAT.$rtBlur, 2,
            {y: thisOffset / 2, force3D: true, delay: 0.1});
      }

      featuresParr();
      $W.scroll(featuresParr);
    }
  }

  /*--- Parallax in Features section init -------------------------------*/
  var $expert = $('#sec-expert-back');
  if ($expert.length) {
    var SECEXPERT = {
      $lt_01: $('.sec-expert__lt-img_01'),
      $lt_02: $('.sec-expert__lt-img_02'),
      $lt_03: $('.sec-expert__lt-img_03'),
      $lt_04: $('.sec-expert__lt-img_04'),
      $lt_05: $('.sec-expert__lt-img_05'),
      $lt_06: $('.sec-expert__lt-img_06'),
      $lt_07: $('.sec-expert__lt-img_07'),
      $rt_01: $('.sec-expert__rt-img_01'),
      $rt_02: $('.sec-expert__rt-img_02'),
      $rt_03: $('.sec-expert__rt-img_03'),
      $rt_04: $('.sec-expert__rt-img_04'),
      $rt_05: $('.sec-expert__rt-img_05'),
      $rt_06: $('.sec-expert__rt-img_06'),
    };

    if (isMobileView) {

      function expertParr() {
        var winScroll = $W.scrollTop(),
            winHeight = $W.height(),
            secTop = $expert.offset().top || 0,
            startLevel, stopLevel, thisOffset;

        startLevel = secTop - winHeight;
        stopLevel = secTop + winHeight;
        if (winScroll < startLevel || winScroll > stopLevel) return;
        thisOffset = winScroll - startLevel;

        TweenLite.to(SECEXPERT.$lt_01, 2,
            {y: thisOffset / 14, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_02, 2,
            {y: thisOffset / 12, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_03, 2,
            {y: thisOffset / 10, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_04, 2,
            {y: thisOffset / 8, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_05, 2,
            {y: thisOffset / 6, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_06, 2,
            {y: thisOffset / 4, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$lt_07, 2,
            {y: thisOffset / 2, force3D: true, delay: 0.1});

        TweenLite.to(SECEXPERT.$rt_01, 2,
            {y: thisOffset / 12, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$rt_02, 2,
            {y: thisOffset / 10, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$rt_03, 2,
            {y: thisOffset / 8, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$rt_04, 2,
            {y: thisOffset / 6, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$rt_05, 2,
            {y: thisOffset / 4, force3D: true, delay: 0.1});
        TweenLite.to(SECEXPERT.$rt_06, 2,
            {y: thisOffset / 2, force3D: true, delay: 0.1});
      }

      expertParr();
      $W.scroll(expertParr);
    }
  }

  /*--- Product viewer --------------------------------------------------*/
  $('.js-to-fotorama').fotorama({
    width: '100%',
    height: 340,
    allowfullscreen: true,
    nav: 'thumbs',
    fit: 'contain',
    ratio: 1,
    hash: true,
    loop: true,
    thumbfit: 'contain',
    thumbwidth: 60,
    thumbheight: 60,
    thumbmargin: 20,
    thumbborderwidth: 1,
  });

  /*--- Test notification example ---------------------------------------*/
  // https://ned.im/noty/#/options

  $('.js-show-noty').click(function() {
    var $btn = $(this),
        notyText = $btn.data('noty');

    new Noty({
      text: notyText,
      type: 'info', // alert, success, error, warning, info
      layout: 'topRight',
      timeout: 3000, // Delay for closing in milliseconds. Set 'false' for sticky notifications.
      closeWith: ['click', 'button'],
    }).show();

    new Noty({
      text: notyText,
      type: 'warning', // alert, success, error, warning, info
      layout: 'topRight',
      timeout: 4000, // Delay for closing in milliseconds. Set 'false' for sticky notifications.
      closeWith: ['click', 'button'],
    }).show();

    new Noty({
      text: notyText,
      type: 'success', // alert, success, error, warning, info
      layout: 'topRight',
      timeout: 5000, // Delay for closing in milliseconds. Set 'false' for sticky notifications.
      closeWith: ['click', 'button'],
    }).show();
  });

  /*--- Sec-work slider -------------------------------------------------*/
  var $secWorkSlider = $('#sec-work-slider'),
      $workFilterBtn = $('.js-work-filter');
  if ($secWorkSlider.length) {

    $secWorkSlider.slick({
      arrows: false,
      dots: true,
      infinite: true,
      speed: 600,
      autoplay: true,
      autoplaySpeed: 5000,
      slidesToShow: 1,
      slidesToScroll: 1,
    });

    function secWorkfilter(keyClassName, key) {
      var $workCounter = $('#sec-work-count'),
          valueStart = +key === 1 ? 101 : +key,
          od;

      $secWorkSlider.slick('slickUnfilter').
          slick('slickFilter', '.' + keyClassName);

      od = new Odometer({
        el: $workCounter[0],
        duration: 300,
        value: valueStart,
        format: '(dd)',
      });
      od.update(+key + 100);
    }

    secWorkfilter('js-type-01', '01');

    $workFilterBtn.click(function() {
      var key = $(this).data('key'),
          keyClassName = 'js-type-' + key;

      secWorkfilter(keyClassName, key);
      $workFilterBtn.removeClass('is-active');
      $(this).addClass('is-active');
    });
  }

  /*--- Sec-start slider -----------------------------------------------*/
  var $secStartSlider = $('#sec-start-slider');
  if ($secStartSlider.length) {

    $secStartSlider.slick({
      arrows: true,
      dots: false,
      infinite: true,
      speed: 0,
      fade: true,
      autoplay: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
      nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
      responsive: [
        {breakpoint: 768, settings: {arrows: false}},
      ],
    });
  }

  $('.js-set-start-slide').click(function() {
    var $btn = $(this),
        $btns = $('.js-set-start-slide'),
        slideNum = +$btn.data('slide');

    $secStartSlider.slick('slickGoTo', slideNum, false);
  });

  $('.js-sec-start-slider-freeze').click(function() {
    $('#sec-start-select').addClass('is-frozen');
  });

  $('.js-sec-start-slider-unfreeze').click(function() {
    $('#sec-start-select').removeClass('is-frozen');
  });

  /*--- Sec-master slider -----------------------------------------------*/
  var $secMasterSlider = $('#sec-master-slider');
  if ($secMasterSlider.length) {

    $secMasterSlider.slick({
      arrows: true,
      dots: true,
      infinite: true,
      speed: 800,
      autoplay: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
      nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
      responsive: [
        {breakpoint: 760, settings: {arrows: false}},
      ],
    });
  }

  /*--- Sec-reviews slider -----------------------------------------------*/
  var $secReviewsrSlider = $('#sec-reviews-slider');
  if ($secReviewsrSlider.length) {

    $secReviewsrSlider.slick({
      arrows: true,
      dots: true,
      infinite: true,
      speed: 800,
      autoplay: false,
      slidesToShow: 3,
      slidesToScroll: 1,
      prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
      nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
      responsive: [
        {breakpoint: 1100, settings: {slidesToShow: 2}},
        {breakpoint: 760, settings: {slidesToShow: 2, arrows: false}},
        {breakpoint: 540, settings: {slidesToShow: 1, arrows: false}},
      ],
    });
  }

  /*--- Sec-partners slider -----------------------------------------------*/
  var $secPartnersSlider = $('#sec-partners-slider, .sec-partners-slider');
  if ($secPartnersSlider.length) {

    $secPartnersSlider.slick({
      arrows: true,
      dots: true,
      infinite: true,
      speed: 800,
      autoplay: true,
      slidesToScroll: 1,
      variableWidth: true,
      prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
      nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
      responsive: [
        {breakpoint: 1024, settings: {arrows: false}},
      ],
    });
  }

  /*--- Sec-expert slider -----------------------------------------------*/
  var $secExpertSlider = $('#sec-expert-slider'),
      $expertBtn = $('.js-expert-btn');
  if ($secExpertSlider.length) {

    $secExpertSlider.slick({
      arrows: true,
      dots: true,
      infinite: true,
      speed: 800,
      autoplay: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
      nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
    });

    $expertBtn.click(function() {
      var num = +$(this).data('num') - 1;
      $secExpertSlider.slick('slickGoTo', num, false);
    });
  }

  /*--- User content slider --------------------------------------------*/
  $('.js-ucs-gallery').slick({
    arrows: true,
    dots: false,
    infinite: true,
    speed: 800,
    autoplay: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
    nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
  });

  /*--- Sec announces slider --------------------------------------------*/
  $('.js-announces-slider').slick({
    arrows: true,
    dots: true,
    infinite: false,
    speed: 800,
    autoplay: false,
    slidesToShow: 3,
    slidesToScroll: 1,
    prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
    nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
    responsive: [
      {breakpoint: 1024, settings: {slidesToShow: 2}},
      {breakpoint: 600, settings: {slidesToShow: 1}},
    ],
  });

  /*--- Sec announces slider --------------------------------------------*/
  $('#sec-offer-slider').slick({
    arrows: false,
    dots: true,
    infinite: true,
    speed: 800,
    autoplay: false,
    slidesToShow: 1,
    variableWidth: true,
    prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
    nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
  });

  /*--- Car-find slider -------------------------------------------------*/
  $('.js-car-find-slider').slick({
    arrows: true,
    dots: true,
    infinite: false,
    speed: 800,
    autoplay: false,
    slidesToShow: 5,
    slidesToScroll: 5,
    prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
    nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
    responsive: [
      {breakpoint: 960, settings: {slidesToShow: 4, slidesToScroll: 4}},
      {breakpoint: 780, settings: {slidesToShow: 3, slidesToScroll: 3}},
      {breakpoint: 600, settings: {slidesToShow: 2, slidesToScroll: 2}},
      {breakpoint: 480, settings: {slidesToShow: 1, slidesToScroll: 1}},
    ],
  });

  /*--- Sec-cars slider -------------------------------------------------*/
  $('.js-sec-cars-slider').slick({
    arrows: true,
    dots: true,
    infinite: false,
    speed: 800,
    autoplay: false,
    slidesToShow: 6,
    slidesToScroll: 6,
    prevArrow: '<button type=\'button\' class=\'slick-arrow slick-prev\'></button>',
    nextArrow: '<button type=\'button\' class=\'slick-arrow slick-next\'></button>',
    responsive: [
      {breakpoint: 1080, settings: {slidesToShow: 5, slidesToScroll: 5}},
      {breakpoint: 960, settings: {slidesToShow: 4, slidesToScroll: 4}},
      {breakpoint: 780, settings: {slidesToShow: 3, slidesToScroll: 3}},
      {breakpoint: 600, settings: {slidesToShow: 2, slidesToScroll: 2}},
      {breakpoint: 480, settings: {slidesToShow: 1, slidesToScroll: 1}},
    ],
  });

  /*--- Sec-services slider ---------------------------------------------*/
  $('.js-services-btn').click(function() {
    var $btn = $(this),
        $thisKind = $btn.closest('.js-services-kind'),
        $thisNum = $thisKind.data('kind'),
        $kinds = $('.js-services-kind'),
        $servicesImg = $('.js-services-img'),
        $servicesMenu = $('#services-menu'),
        $servicesCount = $('#services-count'),
        thisIndex, $newList;

    if ($thisKind.hasClass('is-active')) return false;

    thisIdex = $thisKind.index();
    $kinds.removeClass('is-active');
    $thisKind.addClass('is-active');

    if (thisIdex === 0) {
      $servicesMenu.prepend($kinds.eq(3));
    }
    if (thisIdex === 2) {
      $servicesMenu.append($kinds.eq(0));
    }
    if (thisIdex === 3) {
      $servicesMenu.append($kinds.eq(0));
      $servicesMenu.append($kinds.eq(1));
    }

    $servicesCount.text($thisNum);
    $servicesImg.removeClass('is-active');
    $servicesImg.eq(+$thisNum - 1).addClass('is-active');
  });

  /*--- Sec-advantages toggle -------------------------------------------*/
  $('.js-advantages-toggle').click(function() {
    var $btn = $(this),
        $info = $('#advantages-body');

    if ($btn.hasClass('is-open')) {
      $btn.removeClass('is-open').
          html(
              'Посмотреть преимущества <i class="btn__icon-rt icon_arrow-rt"></i>');
      $info.removeClass('is-open');
    } else {
      $btn.addClass('is-open').
          html(
              'Скрыть преимущества <i class="btn__icon-rt icon_arrow-rt"></i>');
      $info.addClass('is-open');
    }
  });

  /*--- Register form club check ----------------------------------------*/
  $('#reg-form-club-check').on('change', function() {
    var $btn = $(this),
        $info = $('.reg-form__club-input');

    if ($btn.prop('checked')) {
      $info.addClass('is-active');
    } else {
      $info.removeClass('is-active');
    }
  });

  /*--- Car find btn click ----------------------------------------------*/
  $('.js-car-find-btn').on('click', function() {
    var $btn = $(this),
        $btns = $('.js-car-find-btn');

    if ($btn.hasClass('is-active')) {
      $btns.removeClass('is-active');
    } else {
      $btns.removeClass('is-active');
      $btn.addClass('is-active');
    }
  });

  /*--- Car find btn click ----------------------------------------------*/
  $('.js-submenu-switch').on('click', function() {
    var $btn = $(this),
        $submenu = $btn.parent().siblings('.cm-submenu');

    console.log($submenu);

    if ($btn.hasClass('is-active')) {
      $submenu.removeClass('is-active');
      $btn.removeClass('is-active');
    } else {
      $submenu.addClass('is-active');
      $btn.addClass('is-active');
    }
  });

  /*--- Run line step change --------------------------------------------*/
  $('.js-run-step').click(function() {
    var $btn = $(this),
        $steps = $btn.siblings(),
        $stepsBefore = $btn.prevAll();

    $steps.removeClass('is-before is-active');
    $btn.addClass('is-active');
    $stepsBefore.addClass('is-before');
  });

  /*--- Inline-calendar costing on Maintenance-works page ---------------*/
  // www.bootstrap-datepicker.readthedocs.io/en/stable
  var $cgCalendar = $('.js-cg-datepicker'),
      disabledDates = [];

  $cgCalendar.datepicker({
    format: 'dd.mm.yyyy',
    startDate: '0d',
    language: 'ru',
    autoclose: false,
    multidate: false,
    clearBtn: false,
    todayHighlight: true,
    //title: "Календарь",
    orientation: 'left bottom',
  });

  //Событие изменения даты
  $cgCalendar.on('changeDate', function() {
    $('#cg-order-date').text($cgCalendar.datepicker('getFormattedDate'));
  });

  // Если один инпут с ограниченным сегодняшней датой календарем:
  var $datepickerLimited = $('.js-datepicker-limited');
  $datepickerLimited.datepicker({
    format: 'dd.mm.yyyy',
    startDate: '0d',
    language: 'ru',
    autoclose: true,
    multidate: false,
    clearBtn: true,
    //todayHighlight: true,
    title: 'Календарь',
    orientation: 'left bottom',
  });
  $datepickerLimited.datepicker().on('changeDate', function() {
    console.log($(this).datepicker('getFormattedDate'));
  });

  /*
  Событие изменения даты
  $calendar.on("changeDate", function(e){ // Событие выбора даты
      var selectedDate = e.date; //the relevant Date object, in local timezone
  });

  Добавление исключенных дат по формату
  disabledDates = ["22.01.2018", "23.01.2018"];
  $calendar.datepicker('setDatesDisabled', disabledDates);
  */

  /*--- Slider costing steps on Maintenance-works page -----------------------*/
  var $costingSteps = $('#costing-steps'),
      $csSvg = $('#cs-svg'),
      $csStage = $('#cs-stage');

  $costingSteps.slick({
    arrows: false,
    dots: false,
    draggable: false,
    adaptiveHeight: true,
    swipe: false,
    infinite: false,
    speed: 1200,
    autoplay: false,
    slidesToShow: 1,
    slidesToScroll: 1,
  });

  /*--- Costing steps -------------------------------------------------------*/
  $('.js-cg-next-step_02').click(function() {
    $costingSteps.slick('slickGoTo', 1, false);
    $csStage.removeClass('step_01-back').addClass('step_02');
  });

  $('.js-cg-next-step_03').click(function() {
    $costingSteps.slick('slickGoTo', 2, false);
    $csStage.removeClass('step_02 step_02-back').addClass('step_03');
  });

  $('.js-cg-next-step_04').click(function() {
    $costingSteps.slick('slickGoTo', 3, false);
    $csStage.removeClass('step_03').addClass('step_04');
  });

  $('.js-cg-back-step_01').click(function() {
    $costingSteps.slick('slickGoTo', 0, false);
    $csStage.removeClass('step_02').addClass('step_01-back');
  });

  $('.js-cg-back-step_02').click(function() {
    $costingSteps.slick('slickGoTo', 1, false);
    $csStage.removeClass('step_03').addClass('step_02-back');
  });

  /*----------- Мелькание количества ---------------------------------------*/
  $('.js-odometer').viewportChecker({
    offset: 100,
    callbackFunction: function(elem) {
      var $elem = $(elem),
          startVal = +$elem.text(),
          newValue = +$elem.data('value'),
          od;

      $elem.removeClass('is-hidden');
      od = new Odometer({
        el: $elem[0],
        value: startVal,
        format: '( ddd)',
        duration: 3000,
      });
      od.update(newValue);
    },
  });

  /*----------- Аккордеон на стр каталога ----------------------------------*/
  $('.js-cat-acc-head').click(function() {
    $(this).parent().toggleClass('is-active');
  });

  /*------------ Mobile menu toggle button ---------------------------------*/
  $('.js-mobmenu-toggle').click(function() {
    var $btn = $(this),
        $drop = $('#mob-drop'),
        $header = $('#site-header');

    if ($btn.hasClass('is-open')) {
      $btn.removeClass('is-open');
      $header.removeClass('is-open');
      $B.removeClass('is-cut');
    } else {
      $btn.addClass('is-open');
      $header.addClass('is-open');
      $B.addClass('is-cut');
    }
  });

  /*------------ Modal window -----------------------------------------------*/
  var $showModalBtn = $('.js-show-modal');

  $showModalBtn.click(function() {
    var $btn = $(this),
        $modal = $('#modal');

    $modal.toggleClass('is-active');
    $B.addClass('is-cut');
  });

  $('.js-modal-close').click(function() {
    var $btn = $(this),
        $modal = $('#modal');

    $modal.removeClass('is-active');
    $B.removeClass('is-cut');
  });

  $('.js-select-car').click(function() {
    var $btn = $(this),
        $modalTabs = $('.js-modal-tab'),
        $nextTab = $('#modal-tab_02'),
        $step1 = $('#modal-step-1'),
        $step2 = $('#modal-step-2'),
        $modalHead = $('#modal-head');

    $modalTabs.removeClass('is-active');
    $nextTab.addClass('is-active');
    $step1.removeClass('is-active').addClass('is-completed');
    $step2.addClass('is-active');
    $modalHead.addClass('on-step-two');
  });

  $('.js-car-card').click(function() {
    var $btn = $(this);

    $btn.parent().toggleClass('is-selected');
  });

  if ($showModalBtn.length) {
    $W.scroll(function() {
      $showModalBtn.parent().removeClass('is-active');
    });
  }

  /*------------ Reminder-pop-up -----------------------------------------*/
  var $reminder = $('#reminder');
  if ($reminder.length) {

    if (sessionStorage.getItem('reminder') !== 'closed') {
      setTimeout(function() {
        $reminder.addClass('is-open');
      }, 3000);

      $('.js-reminder-close').click(function() {
        $reminder.removeClass('is-open');
        sessionStorage.setItem('reminder', 'closed');
      });
    }
  }

  /*
  $("#js-catalog-pict-slider").on('beforeChange', function(event, slick, currentSlide, nextSlide){
    console.log(nextSlide);

    $(".js-price-unit").removeClass("is-active").eq( nextSlide ).addClass("is-active");

    $(".js-price-unit.is-active .js-price").each(function (index, element) {
      var od,
        valueFinish = $( element ).data("price-finish"),
        valueStart = $( element ).data("price-start");
      $( element ).text("");

      console.log( valueFinish );
      console.log( valueFinish );

      od = new Odometer({
        el: element,
        value: valueStart,
        format: "( ddd)"
      });
      od.update( valueFinish );
    });
  });
*/

});

/*----------- ФУНКЦИИ ПОСЛЕ ЗАГРУЗКИ -----------------------------------------*/
$W.on('load', function() {


  /*--- Custom scrollbar X ----------------------------------------------*/
  var $scroll_X = $('.js-scroll-x'),
      $scroll_Y = $('.js-scroll-y');

  function inintScroll_X($elem) {
    $elem.mCustomScrollbar({
      axis: 'x',
      scrollButtons: {enable: false},
      scrollbarPosition: 'outside',
      alwaysShowScrollbar: 2,
      updateOnContentResize: true,
      autoDraggerLength: false,
      mouseWheel: {enable: false},
    });
  }

  if ($scroll_X.length) {

    inintScroll_X($scroll_X);

    $W.resize(function() {
      $scroll_X.mCustomScrollbar('destroy');
      inintScroll_X($scroll_X);
    });
  }

  /*--- Custom scrollbar Y ----------------------------------------------*/
  function inintScroll_Y($elem) {
    $elem.mCustomScrollbar({
      axis: 'y',
      scrollButtons: {enable: false},
      scrollbarPosition: 'outside',
      alwaysShowScrollbar: 0,
      updateOnContentResize: true,
      autoDraggerLength: false,
    });
  }

  if ($scroll_Y.length) {
    inintScroll_Y($scroll_Y);
  }

  /*--- One section scroll ----------------------------------------------*/
  $.scrollify({
    section: '.js-sec-scroll',
    sectionName: 'section-name',
    easing: 'easeOutExpo',
    scrollSpeed: 1100,
    offset: 0,
    scrollbars: true,
    standardScrollElements: '',
    setHeights: false,
    overflowScroll: true,
    updateHash: true,
    touchScroll: true,
  });

  if (isMobileView) {
    console.log(isMobileView);
    console.log('disable');
    $.scrollify.disable();
  }

  $W.resize(function() {
    var isMobileView = mobChecker(1024);

    if (isMobileView) {
      console.log('disable');
      $.scrollify.disable();
    } else {
      console.log('enable');
      $.scrollify.enable();
    }
  });

  $('.js-stripe').viewportChecker({
    offset: 150,
    callbackFunction: function(elem) {
      var $elem = $(elem),
          delayTime = $elem.data('delay');

      if (delayTime) {
        setTimeout(function() {
          $elem.addClass('is-active');
        }, +delayTime);
      } else {
        $elem.addClass('is-active');
      }
    },
  });

  /*--- Google maps api -------------------------------------------------*/
  if ($('.js-map-here').length > 0) {
    $.getScript(
        'https://maps.google.com/maps/api/js?key=AIzaSyAr3L94pBd0Dy_AR0mirpFBB7NtbgmOV9E&sensor=false',
        function() {
          console.log('Map loaded');
          mapStart();
        });
  }
});

