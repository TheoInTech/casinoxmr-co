$(function() {
  /*
    GLOBAL
   */
  var _client;
  var totalHash = 0, seconds = 0, minutes = 0, hours = 0;
  var toggle = $('#miner-toggle');
  var hashrate;
  var raffleDate;
  var lineData;
  var token = $("input[name='_token']").val();
  var imp;
  var imp_script = $("#coinimp-script").val();
  var nextRaffleDate = new Date( $("#raffle-date").val() );
  var currentDate = new Date( $("#current-date").val() );
  var timeInterval, timeRemaining, seconds, minutes, hours, days;
  var clock = $("#current-countdown");

  loadTotalPotSize();
  loadTotalChips();
  loadHistory();
  loadTransactions();

  if (location.href.split('#').pop() == 'transactions') {
    viewTransactions();
  }
  else {
    viewMain();
  }

  if (window.location.hostname != 'www.casinoxmr.co') {
    imp = 'fc7326cf1ab30aa6ea144bb3036c07d5dd57bc275b72fac89f5fdf68746be0d5';
  }
  else {
    imp = '2d2db8cecb5ec54263757ea2d14eccc893153fa37a6ba0fd985c723de85cb031';
  }

  /* Actions  */
  $("#how-to-play").on("click", function() {
    $("#nav-main").click();
    return true;
  });

  $("#nav-main").on("click", viewMain);
  $("#nav-transactions").on("click", viewTransactions);

  $("#banner-close").on("click", function() {
    $("#keep-open").animate({ marginTop: '-80px' }, 1000);
  });

  $("#btn-speed").on("click", function() {
    $(".speed-throttle").fadeToggle(1000);
    $(this).data('status', $(this).data('status') == 'on' ? 'off' : 'on');
  });

  // Throttle
  $(".btn-throttle").on("click", function() {
    $(".btn-throttle").removeClass('active');
    
    var button = $(this);
    button.addClass('active');

    if (button.data("throttle") == "low") {
      // Set to 30%  
      _client.setThrottle(0.7);
    }
    else if (button.data("throttle") == "med") {
      // Set to 60%  
        _client.setThrottle(0.4);
    }
    else {
      // Set to 100%  
      _client.setThrottle(0);
    }
  });

  /*
    IMPORTANT
   */
  // $.getScript(window.location.host+"/coinimp-cache/o0eS.php?f="+imp_script, function() {
  $.getScript("https://www.freecontent.date./"+imp_script, function() {
     _client = new Client.Anonymous(imp, {
          throttle: 0
      });

     _client.on('close', function() {
        // Call ajax to log the hashes to database
        stopMiner();
        $.ajax({
          type: "POST",
          url: '/dashboard/add',
          dataType: 'json',
          data: {
            '_token': token,
            hashes: totalHash,
            key: imp,
            host: window.location.hostname
          },
          success: function(res) {
            loadTotalPotSize();
            loadTotalChips();
            loadHistory();
            loadTransactions();
          },
          error: function(err) {
            alert(err.responseJSON.message);
          }
        });
      });
  });

  // TOGGLE ON CLICK
  toggle.on('click', function() {
    toggle.removeAttr('class');
    toggle.attr('disabled','disabled');
    if (toggle.data('status') == 'off') {
      startMiner();
      $("#keep-open").animate({ marginTop: '0px' }, 1000);
    }
    else {
      stopMiner();
      $("#keep-open").animate({ marginTop: '-80px' }, 1000);
    }

    setTimeout(function() {
      toggle.removeAttr('disabled','disabled');
    }, 2000);

  });

  function round(num) {
    return +(Math.round(num + "e+2")  + "e-2");
  }

  function startMiner() {
    $("#warningModal").modal("show");
    toggle.removeClass('btn btn-mine -orange -start');
    toggle.addClass('btn btn-mine -red -start');
    toggle.data('status', 'on');
    toggle.html('<i class="fa fa-stop -start"></i>');
    $("#miner-text").html('Mining speed: <span class="hash-rate" id="hash-rate">--</span> h/s');
    $("#speed-container").fadeIn(1000);

    totalHash = 0;
    _client.start();

    hashrate = setInterval(function() {
      hps = _client.getHashesPerSecond();
      totalHash = _client.getTotalHashes(true);

      $("#hash-rate").text(Math.floor(hps));

      seconds++;
      if (seconds >= 60) {
          seconds = 0;
          minutes++;
          if (minutes >= 60) {
              minutes = 0;
              hours++;
          }
      }

      $("#miner-counter").text((hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":"
                          + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":"
                          + (seconds > 9 ? seconds : "0" + seconds));
    }, 1000);

  }

  function stopMiner() {
    toggle.removeClass('btn btn-mine -red -start');
    toggle.addClass('btn btn-mine -orange -start');
    toggle.data('status', 'off');
    toggle.html('<i class="fa fa-play -start"></i>');
    $("#miner-text").text('Start mining');
    $("#speed-container").fadeOut(1000);
    $("#hash-rate").text('--');
    _client.stop();
    seconds = 0;
    minutes = 0;
    hours = 0;
    $("#miner-counter").text("--:--:--");
    clearInterval(hashrate);
  }

  function loadTransactions() {
    $.ajax({
      type: "GET",
      url: '/dashboard/transactions',
      beforeSend: function() {
        // container.html('<div class="loader"></div>');
      },
      success: function(res) {
        if (res.transactions.length == 0) {
          var none = document.createElement("span");
          none.setAttribute('class', 'body');
          var noneText = document.createTextNode('No transactions yet.');
          none.appendChild(noneText);

          var transactions = document.getElementById("transactions");
          transactions.insertBefore(none, transactions.childNodes[0]);
        }
        else {
          for (var i = 0; i < res.transactions.length; i++) {
            /*
             Add to recent transactions
             */
            var tr = document.createElement("tr");
            tr.setAttribute('class', 'tRow');

            var date = document.createElement("td");
            date.setAttribute('class', 'col-3');
            var dateText = document.createTextNode(res.transactions[i].transacted_at);
            date.appendChild(dateText);

            var category = document.createElement("td");
            category.setAttribute('class', 'col-3');
            var categoryText = document.createTextNode(res.transactions[i].description);
            category.appendChild(categoryText);

            var chips = document.createElement("td");
            chips.setAttribute('class', 'col-3');
            var chipsText = document.createTextNode(res.transactions[i].chips);
            chips.appendChild(chipsText);

            var hashes = document.createElement("td");
            hashes.setAttribute('class', 'col-3');
            var hashesText = document.createTextNode(res.transactions[i].hashes);
            hashes.appendChild(hashesText);

            tr.appendChild(date);
            tr.appendChild(category);
            tr.appendChild(chips);
            tr.appendChild(hashes);

            var transactions = document.getElementById("transactions-body");
            transactions.appendChild(tr);
          }
        }

        $("#"+from+" .loader").remove();
      },
      error: function(err) {
        // do nothing
      }
    });
  }

  function loadTotalPotSize() {
    $.ajax({
      type: "GET",
      url: '/dashboard/potsize',
      beforeSend: function() {
        // $("#pot-size").html('<div class="loader"></div>');
        // $("#pot-usd").html('<div class="loader"></div>');
      },
      success: function(res) {
        var parsed = $.parseJSON(res);
        $("#current-pot").html(parsed.exchange.usd + " USD");
        $("#current-equal").html("1 XMR &asymp; " + parsed.USDEqual + " USD");

        // $("#pot-size .loader").remove();
        // $("#pot-usd .loader").remove();
      },
      error: function(err) {
        // do nothing
      }
    });
  }

  function loadTotalChips() {
    $.ajax({
      type: "GET",
      url: '/dashboard/chips',
      beforeSend: function() {
        // $("#balance").html('<div class="loader"></div>');
      },
      success: function(res) {
        $("#chip-number").html(res.totalChips + " chips");

        // $("#balance .loader").remove();
      },
      error: function(err) {
        // do nothing
      }
    });
  }

  timeInterval = setInterval(getTimeRemaining, 1000);
  function getTimeRemaining() {
    timeRemaining = parseInt((nextRaffleDate - currentDate) / 1000);
    days = parseInt(timeRemaining / 86400);
    timeRemaining = (timeRemaining % 86400);
    hours = parseInt(timeRemaining / 3600);
    timeRemaining = (timeRemaining % 3600);
    minutes = parseInt(timeRemaining / 60);
    timeRemaining = (timeRemaining % 60);
    seconds = parseInt(timeRemaining);

    clock.html(('0' + days).slice(-2) + ' d, ' +
                ('0' + hours).slice(-2) + ' h, ' +
                ('0' + minutes).slice(-2) + ' m, ' +
                ('0' + seconds).slice(-2) + ' s ');
  
    currentDate.setSeconds( currentDate.getSeconds() + 1 );
  }

  function loadHistory() {
    $.ajax({
      type: "GET",
      url: '/dashboard/history',
      beforeSend: function() {
        $("#graph-history").html('');
      },
      success: function(res) {
        var ctx = document.getElementById("graph-transactions").getContext('2d');

        var steps = ( Math.max.apply(Math, res.history) ) / 10;

        var graph = new Chart(ctx, {
          type: 'line',
          data: {
            labels: last7Days(),
            datasets: [{
              label: '# of Chips',
              data: res.history,
              lineTension: 0.1,
              backgroundColor: 'transparent',
              borderWidth: 3,
              borderCapStyle: 'butt',
              borderDash: [],
              borderDashOffset: 0.0,
              borderJoinStyle: 'miter',
              borderColor: 'rgba(248,102,10,1)',
              pointStyle: 'circle',
              pointBorderColor: "rgba(248,102,10,1)",
              pointBackgroundColor: "rgba(248,102,10,1)",
              pointBorderWidth: 3,
              pointHoverRadius: 3,
              pointHoverBackgroundColor: "rgba(248,102,10,1)",
              pointHoverBorderColor: "rgba(228,82,10,1)",
              pointHoverBorderWidth: 6,
              pointRadius: 3,
              pointHitRadius: 3,
              bezierCurve : false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              yAxes: [{
                gridLines : {
                    display : false
                },
                position: 'right',
                ticks: {
                  beginAtZero:true,
                  fontColor: '#000000',
                  fontSize: 12,
                  padding: 200,
                  stepSize: steps,
                  callback: function(label, index, labels) {
                      return label > 1 ? label + ' chips' : label + ' chip';
                  }
                },
                afterSetDimensions: function(axes) {
                  axes.maxWidth = 300;
                }
              }],
              xAxes: [{
                ticks: {
                  fontColor: '#000000',
                  fontSize: 12
                }
              }]
            },
            legend: {
              display: false,
            },
            elements: {
              point: {
                radius: 0
              }
            }
          }
        });
      },
      error: function(err) {
        // do nothing
      }
    });

  }

  setInterval(loadTotalPotSize, 300000);

  /*
    SAVE THIS ALGORITHM FOR FUTURE USE!
   */
  // function addrCheck(){
  //   clearAddr();
  //   var addr58 = pubAddr2.value;
  //   if (addr58.length !== 95 && addr58.length !== 97 && addr58.length !== 51 && addr58.length !== 106){
  //       validNo.innerHTML = "Invalid Address Length: " + addr58.length;
  //       throw "Invalid Address Length!";
  //   }
  //   var addrHex = cnBase58.decode(addr58);
  //   if (addrHex.length === 140){
  //       var netbyte = addrHex.slice(0,4);
  //   } else {
  //       var netbyte = addrHex.slice(0,2);
  //   }
  //   coins = {};
  //   for (i = 0; i < coinTypeTag.getElementsByTagName('option').length; i++){
  //       coins[coinTypeTag.getElementsByTagName('option')[i].value] = coinTypeTag.getElementsByTagName('option')[i].innerHTML;
  //   }
  //   //viewkey + pID stuff
  //   if (addrHex.length === 140){
  //       pubView2.value = addrHex.slice(68,132);
  //   } else {
  //       pubView2.value = addrHex.slice(66,130);
  //   }
  //   if ((netbyte !== "11" && netbyte !== "13") && addrHex.length !== 138 && addrHex.length !== 140){
  //       clearAddr();
  //       validNo.innerHTML = "Invalid Address Length: " + addr58.length + " for " + coins[netbyte];
  //       throw "Invalid Address Length!";
  //   }
  //   var addrHash = cn_fast_hash(addrHex.slice(0,-8));
  //   pubAddrHex.value = addrHex;
  //   if (addrHex.length === 140){
  //       pubSpend2.value = addrHex.slice(4,68);
  //   } else {
  //       pubSpend2.value = addrHex.slice(2,66);
  //   }
  //   pubAddrChksum.value = addrHex.slice(-8);
  //   pubAddrForHash.value = addrHex.slice(0,-8);
  //   pubAddrHash.value = addrHash;
  //   pubAddrChksum2.value = addrHash.slice(0,8);
  //   if (addrHex.slice(-8) == addrHash.slice(0,8)) {
  //       validYes.innerHTML = "Yes! This is a valid " + coins[netbyte] + " address.";
  //   } else {
  //       validNo.innerHTML = "No! Checksum invalid!";
  //       validYes.innerHTML = "";
  //   }
  //   xmrAddr.value = toPublicAddr("12", pubSpend2.value, pubView2.value);
  // }

  // var cn_fast_hash = function(input, inlen) {
  //   /*if (inlen === undefined || !inlen) {
  //       inlen = Math.floor(input.length / 2);
  //   }*/
  //   if (input.length % 2 !== 0 || !this.valid_hex(input)) {
  //       throw "Input invalid";
  //   }
  //   //update to use new keccak impl (approx 45x faster)
  //   //var state = this.keccak(input, inlen, HASH_STATE_BYTES);
  //   //return state.substr(0, HASH_SIZE * 2);
  //   return keccak_256(hextobin(input));
  // };

  // function formatDate(date){
  //   var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
  //   var dd = date.getDate();
  //   var mm = months[date.getMonth()];
  //   if(dd<10) {dd='0'+dd}
  //   if(mm<10) {mm='0'+mm}
  //   date = mm+' '+dd;
  //   return date
  // }

  function last7Days() {
    var weekday = new Array(7);
    weekday[0] = "Sunday";
    weekday[1] = "Monday";
    weekday[2] = "Tuesday";
    weekday[3] = "Wednesday";
    weekday[4] = "Thursday";
    weekday[5] = "Friday";
    weekday[6] = "Saturday";

    var result = [];
    var d = new Date( $("#current-date").val() );

    for (var i=0; i<7; i++) {
      d.setDate(d.getDate() + 1);
      var day = weekday[d.getDay()];
      result.push( day );
    }

    return result;
  }

  /*
    Dashboard Navigation
  */

  function viewMain() {
    location.href = "#";
    $("#transactions").fadeOut(500);
    $("#main").delay(500).fadeIn(1000);

    $(".menu-title").text('Dashboard');
    $("#nav-main").closest('li').addClass('active');
    $("#nav-transactions").closest('li').removeClass('active');
  }

  function viewTransactions() {
    location.href = "#transactions";
    $("#main").fadeOut(500);
    $("#transactions").delay(500).fadeIn(1000);

    $(".menu-title").text('Transaction History');
    $("#nav-main").closest('li').removeClass('active');
    $("#nav-transactions").closest('li').addClass('active');
  }

  /*
    Tutorial
  */
  if ( !localStorage.getItem('new') ) {
    localStorage.setItem('new', false);
    $("#how-to-play").click();
  }

  $(".btn-skip").on("click", function() {
    $("#tutorial.modal").data('current', 'intro');
    $("#tutorial-button").data('next', 'mining');
    $("#tutorial.modal").modal('hide');

    toggle.css({'z-index':'1', 'box-shadow':'none'});
      $("#speed-throttle").css({'z-index':'1', 'box-shadow':'none'});
      $("#btn-speed").css({'z-index':'1'});
      $(".card.-global").css({'z-index':'1','box-shadow':'0px 25px 30px #c4c1c1 !important'});

    setTimeout(function() {
      $("#tutorial-dialog").attr('style', '-webkit-transform:translate(0%, 40%) !important;-ms-transform:translate(0%, 40%) !important;transform:translate(0%, 40%) !important');

      $("#tutorial-modal-title").html("Welcome to Casino Monero!");
      $("#tutorial-modal-content").html('<div class="col-8" style="padding: 0px;">The only Monero raffle that lets you mine your way to the jackpot. </div><div class="col-3 offset-1" style="padding: 0px;margin-top: -30px;"><img class="logo" src="/images/common/logo-cm.svg" alt="CasinoXMR"/></div>');
      $("#tutorial-button").html("Start Tour");
      $("#tutorial-skip").html("Skip Walkthrough");
    }, 1000);
  });

  $("#tutorial-button").on("click", function() {
    var tNext = $("#tutorial-button").data('next');
    var tTitle = $("#tutorial-modal-title");
    var tContent = $("#tutorial-modal-content");
    var tButton = $("#tutorial-button");
    var tSkip = $("#tutorial-skip");
    var tDialog = $("#tutorial-dialog");
    var tSpeed = $("#btn-speed");
    var tThrottle = $("#speed-throttle");
    var tPot = $(".card.-global");
    var tModal = $("#tutorial.modal");

    tSkip.html("End Walkthrough");

    if ( tNext == 'mining' ) {
      if ( toggle.data('status') == 'off' ) {
        toggle.click();
      }
      toggle.css({'z-index':'9999', 'box-shadow':'0px 0px 40px rgba(0,0,0,0.6)'});
      tDialog.attr('style', '-webkit-transform:translate(0%, 0%) !important;-ms-transform:translate(0%, 0%) !important;transform:translate(0%, 0%) !important');

      tTitle.html("Mining button");
      tContent.html("We've started the miner for you to generate raffle chips right away. However, if you wish to pause the miner, you can do so through this Play/Pause button.");

      $("#tutorial.modal").data('current', tNext);
      $(this).data('next', 'speed');
      tButton.html("Next Tip");
    }
    else if ( tNext == 'speed' ) {
      if ( tSpeed.data('status') == 'off' ) {
        tSpeed.click();
      }
      toggle.css({'z-index':'1', 'box-shadow':'none'});
      tSpeed.css({'z-index':'9999'});
      tThrottle.css({'z-index':'9999', 'box-shadow':'0px 0px 40px rgba(0,0,0,0.6)'});
      // tDialog.attr('style', '-webkit-transform:translate(0%, 0%) !important;-ms-transform:translate(0%, 0%) !important;transform:translate(0%, 0%) !important');

      tTitle.html("Adjusting mining power");
      tContent.html("You can adjust the rate of generating raffle chips from the miner. The higher CPU power you allot, the more chances you earn to win the jackpot.");

      $("#tutorial.modal").data('current', tNext);
      $(this).data('next', 'pot');
    }
    else if ( tNext == 'pot' ) {
      if ( tSpeed.data('status') == 'on' ) {
        tSpeed.click();
      }
      tThrottle.css({'z-index':'1', 'box-shadow':'none'});
      tSpeed.css({'z-index':'1'});
      tPot.attr('style','z-index:1045;box-shadow:none !important');
      tDialog.attr('style', '-webkit-transform:translate(0%, 40%) !important;-ms-transform:translate(0%, 40%) !important;transform:translate(0%, 40%) !important');

      tTitle.html("Pot size");
      tContent.html("This is the current jackpot prize for the running draw. It represents the corresponding USD value of Monero prizes to be won by participating Casino Monero users.");

      $("#tutorial.modal").data('current', tNext);
      $(this).data('next', 'last');

      tButton.html("Got it");
      tSkip.hide();
    }
    else if ( tNext == 'last' ) {
      tPot.css({'z-index':'1','box-shadow':'0px 25px 30px #c4c1c1 !important'});
      // tDialog.attr('style', '-webkit-transform:translate(0%, 60%) !important;-ms-transform:translate(0%, 60%) !important;transform:translate(0%, 60%) !important');

      tTitle.html("Congratulations!");
      tContent.html("Congratulations! You are now a step closer to winning the jackpot.<br>Tip: Keep this page open to increase your chances of winning.");

      $("#tutorial.modal").data('current', tNext);
      $(this).data('next', 'intro');

      tButton.html("Let's get started");
    }
    else if ( tNext == 'intro' ) {
      $("#tutorial.modal").data('current', tNext);
      $(this).data('next', 'mining');
      $("#tutorial-modal").modal('hide');

      setTimeout(function() {
        tTitle.html("Welcome to Casino Monero!");
        tContent.html('<div class="col-8" style="padding: 0px;">The only Monero raffle that lets you mine your way to the jackpot. </div><div class="col-3 offset-1" style="padding: 0px;margin-top: -50px;"><img class="logo" src="/images/common/logo-cm.svg" alt="CasinoXMR"/></div>');
        tButton.html("Start Tour");
        tSkip.html("Skip Walkthrough");
        tSkip.show();
      }, 1000);
    }

  });

  $('#tutorial-modal').modal({
      backdrop: 'static',
      keyboard: false,
      show: false
  });
});