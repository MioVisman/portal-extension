        <section class="f-panel f-panel-info">
          <h2 class="f-panel-header">{!! __('Info header') !!}</h2>
          <div class="f-panel-body f-panel-body-info">
            <dl class="f-panel-info-total-dl">
              <dt class="f-panel-info-total-dt">{!! __('Total data') !!}</dt>
              <dd class="f-panel-info-total-user" title="{{ \strip_tags(__(['No of users: %s', num($stats->userTotal)])) }}">{{ num($stats->userTotal) }}</dd>
              <dd class="f-panel-info-total-topic" title="{{ \strip_tags(__(['No of topics: %s', num($stats->topicTotal)])) }}">{{ num($stats->topicTotal) }}</dd>
              <dd class="f-panel-info-total-post" title="{{ \strip_tags(__(['No of posts: %s', num($stats->postTotal)])) }}">{{ num($stats->postTotal) }}</dd>
            </dl>
            <dl class="f-panel-info-max-dl">
              <dt class="f-panel-info-max-dt">{!! __('Max data') !!}</dt>
@php $curTitle = \strip_tags(__(['Most online', num($online->maxNum), dt($online->maxTime)])); @endphp
              <dd class="f-panel-info-max-user" title="{{ $curTitle }}">{{ num($online->maxNum) }}</dd>
              <dd class="f-panel-info-max-date" title="{{ $curTitle }}">{{ dt($online->maxTime, 1, 0) }}</dd>
            </dl>
            <dl class="f-panel-info-cur-dl">
              <dt class="f-panel-info-cur-dt">{!! __('Current data') !!}</dt>
@php $curTitle = \strip_tags(__(['Visitors online', num($online->numUsers), num($online->numGuests)])); @endphp
              <dd class="f-panel-info-cur-user" title="{{ $curTitle }}">{{ num($online->numUsers) }}</dd>
              <dd class="f-panel-info-cur-guest" title="{{ $curTitle }}">{{ num($online->numGuests) }}</dd>
            </dl>
@if ($online->info)
            <dl class="f-panel-info-online"><!-- inline -->
              <dt class="f-panel-info-online-dt">{!! __('Online users') !!}</dt>
    @foreach ($online->info as $cur)
        @if ($cur['link'])
              <dd><a href="{{ $cur['link'] }}">{{ $cur['name'] }}</a></dd>
        @else
              <dd>{{ $cur['name'] }}</dd>
        @endif
    @endforeach
            </dl><!-- endinline -->
@endif
          </div>
        </section>
