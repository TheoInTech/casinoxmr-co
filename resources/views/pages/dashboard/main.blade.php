<div id="main">

  <div class="row row-graph">
    <div class="col-12 no-padding">
      <canvas class="graph-transactions" id="graph-transactions">
        
      </canvas>
      <!-- Chips box -->
      <div class="col-12 panel-padding">
        <div class="card -chips">
          <div class="card-body -chips">
            <div class="content-chips">
              <div class="global-title">
                My Chips
              </div>
              <div class="row global-body">
                <div class="col-12 col-sm-1 miner-button">
                  <button class="btn btn-mine -orange -start" id="miner-toggle" data-status="off">
                    <i class="fa fa-play -start"></i>
                  </button>
                </div>
                <div class="col-12 col-sm-10 miner-description">
                  <div class="chip-number" id="chip-number">
                    <!-- Chip numbers -->
                  </div>
                  <div class="miner-text" id="miner-text">
                    Start mining
                  </div>
                  <div class="speed-container" id="speed-container">
                    <a class="btn-speed" id="btn-speed" data-status="off">Adjust speed</a>
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
    <div class="col-12 col-sm-6 panel-padding">
      <div class="card -global">
        <div class="card-body -global">
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
        </div>

        <div class="card-footer content-countdown">
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

    <div class="col-12 col-sm-6 panel-padding">
      <div class="card -payouts">
        <div class="card-header -payouts">
          Recent payouts
        </div>
        <div class="card-body -payouts">
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
