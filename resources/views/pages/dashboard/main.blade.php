<div id="main">

  <div class="row row-graph">
    <div class="col-xs-12 no-padding">
      <canvas class="graph-transactions" id="graph-transactions">
        
      </canvas>
      <!-- Chips box -->
      <div class="col-xs-12 col-sm-4 panel-padding">
        <div class="panel -chips">
          <div class="panel-body -chips">
            <div class="content-chips">
              <div class="global-title">
                My Chips
              </div>
              <div class="global-body">
                <div class="col-xs-2 miner-button">
                  <button class="btn btn-mine -orange -start" id="miner-toggle" data-status="off">
                    <i class="fa fa-play -start"></i>
                  </button>
                </div>
                <div class="col-xs-10 miner-description">
                  <div class="chip-number" id="chip-number">
                    <!-- Chip numbers -->
                  </div>
                  <div class="miner-text" id="miner-text">
                    Start mining
                  </div>
                  <div class="speed-container" id="speed-container">
                    <button class="btn-speed" id="btn-speed">Adjust speed</button>
                    <div class="speed-throttle" id="speed-throttle">
                      <button class="btn btn-throttle" data-throttle="low">Low</button>
                      <button class="btn btn-throttle" data-throttle="med">Med</button>
                      <button class="btn btn-throttle active" data-throttle="high">High</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    
  <div class="row row-panel">
    <div class="col-xs-12 col-sm-6 panel-padding">
      <div class="panel -global">
        <div class="panel-body -global">
          <div class="content-pot">
            <div class="global-title">
              Current pot
            </div>
            <div class="global-body">
              <div class="current-pot" id="current-pot">
              </div>
              <div class="current-equal" id="current-equal">
              </div>
            </div>
          </div>

          <div class="content-countdown">
            <div class="icon">
              <i class="fa fa-clock"></i>
            </div>
            <div class="countdown">
              <div class="global-title">
                Time remaining until next draw
              </div>
              <div class="global-body">
                <div class="current-countdown" id="current-countdown">
                  <!-- Next raffle countdown -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6 panel-padding">
      <div class="panel -payouts">
        <div class="panel-heading -payouts">
          Recent payouts
        </div>
        <div class="panel-body -payouts">
          @if ( count($winners) > 0 )
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">Time</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Hash Transaction</th>
                </tr>
              </thead>
              <tbody>
                @foreach($winners as $winner)
                  <tr>
                    <td>{{ $winner->date }}</td>
                    <td>${{ $winner->winning }}</td>
                    <td>{{ $winner->address }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <span class="body">No payouts yet.</span>
          @endif
        </div>  
      </div>
    </div>
  </div>

</div>
