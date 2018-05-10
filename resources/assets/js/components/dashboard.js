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

  // loadTransactions('transactions', 10);
  // loadTransactions('transactions-history');
  loadTotalPotSize();
  loadTotalChips();
  loadHistory();

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
  $("#nav-main").on("click", viewMain);
  $("#nav-transactions").on("click", viewTransactions);

  $("#banner-close").on("click", function() {
    $("#keep-open").animate({ marginTop: '-80px' }, 1000);
  });

  $("#btn-speed").on("click", function() {
    $(".speed-throttle").fadeToggle(1000);
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
  // $.getScript("/coinimp?f=8kcR.js", function() {
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
            // loadTransactions('transactions', 10);
            // loadTransactions('transactions-history');
            loadTotalPotSize();
            loadTotalChips();
            loadHistory();
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

  function loadTransactions(from, take = '') {
    container = $("#"+from);
    $.ajax({
      type: "GET",
      url: '/dashboard/transactions?take='+take,
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
            var li = document.createElement("li");
            li.setAttribute('class', 'item');
            var col1 = document.createElement("div");
            col1.setAttribute('class', 'col-xs-2');
            var date = document.createElement("span");
            date.setAttribute('class', 'body uppercase');
            var dateText = document.createTextNode(res.transactions[i].transacted_at);
            date.appendChild(dateText);
            col1.appendChild(date);

            var col2 = document.createElement("div");
            col2.setAttribute('class', 'col-xs-6');
            var descriptionText = document.createTextNode(' ' + res.transactions[i].description);
            col2.appendChild(descriptionText);

            var col3 = document.createElement("div");
            var chipText = document.createTextNode(res.transactions[i].chips + ' chips');
            col3.appendChild(chipText);
            if (res.transactions[i].is_gain == 1) {
              col3.setAttribute('class', 'col-xs-4 text-success');
            }
            else {
              col3.setAttribute('class', 'col-xs-4 text-danger');
            }

            li.appendChild(col1);
            li.appendChild(col2);
            li.appendChild(col3);

            var transactions = document.getElementById(from);
            transactions.appendChild(li);
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

    clock.html(('0' + days).slice(-2) + ' days, ' +
                ('0' + hours).slice(-2) + ' hours, ' +
                ('0' + minutes).slice(-2) + ' minutes, ' +
                ('0' + seconds).slice(-2) + ' seconds ');
  
    if (t <= 0) {
      clearInterval(timeInterval);
    }
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

        var graph = new Chart(ctx, {
          type: 'line',
          data: {
            labels: last7Days(),
            datasets: [{
              label: '# of Chips',
              data: res.history, //[0.00,1.00,6.00,3.00,4.00,5.00,2.00],
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
                  stepSize: 1,
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

    $(".menu-title").text('Transactions');
    $("#nav-main").closest('li').removeClass('active');
    $("#nav-transactions").closest('li').addClass('active');
  }

});