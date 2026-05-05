        <aside id="fork-stats">
          <p class="f-sim-header">{!! __('Stats info') !!}</p>
          <dl id="fork-stboard">
            <dt class="f-stats-dt">{!! __('Board stats') !!}</dt>
            <dd class="f-stats-dd">{!! __(['No of users: %s', num($stats->userTotal)]) !!}</dd>
            <dd class="f-stats-dd">{!! __(['No of topics: %s', num($stats->topicTotal)]) !!}</dd>
            <dd class="f-stats-dd">{!! __(['No of posts: %s', num($stats->postTotal)]) !!}</dd>
          </dl>
          <dl id="fork-stusers">
            <dt class="f-stats-dt">{!! __('User info') !!}</dt>
@if ($stats->userLast['link'])
            <dd class="f-stats-dd">{!! __(['Newest user: <a href="%2$s">%1$s</a>', $stats->userLast['name'], $stats->userLast['link']]) !!}</dd>
@else
            <dd class="f-stats-dd">{!! __(['Newest user: %s', $stats->userLast['name']]) !!}</dd>
@endif
            <dd class="f-stats-dd">{!! __(['Visitors online', num($online->numUsers), num($online->numGuests)]) !!}</dd>
            <dd class="f-stats-dd">{!! __(['Most online', num($online->maxNum), dt($online->maxTime)]) !!}</dd>
          </dl>
@if ($online->info)
          <dl id="fork-onlinelist" class="f-inline"><!-- inline -->
            <dt id="id-onlst-dt">{!! __('Online users') !!}</dt>
    @foreach ($online->info as $cur)
        @if ($cur['link'])
            <dd><a href="{{ $cur['link'] }}">{{ $cur['name'] }}</a></dd>
        @else
            <dd>{{ $cur['name'] }}</dd>
        @endif
    @endforeach
          </dl><!-- endinline -->
@endif
        </aside>
