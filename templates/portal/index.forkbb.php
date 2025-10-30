@extends ('layouts/main')
    <!-- PRE start -->
    <!-- PRE h1Before -->
    <div class="f-mheader">
      <h1 id="fork-h1">{!! __('Portal') !!}</h1>
    </div>
    <!-- PRE h1After -->
    <!-- PRE mainBefore -->
    <div class="f-main f-portal">
    @if (! empty($p->locations[0]) || ! empty($p->locations[1]))
      <div id="fork-portal-center">
        @if (! empty($p->locations[0]))
            @foreach ($p->locations[0] as $panel)
{!! $panel->html !!}
            @endforeach
        @endif
        @if (! empty($p->locations[1]))
            @foreach ($p->locations[1] as $panel)
{!! $panel->html !!}
            @endforeach
        @endif
      </div>
    @endif
    @if (! empty($p->locations[2]))
      <div id="fork-portal-start">
        @foreach ($p->locations[2] as $panel)
{!! $panel->html !!}
        @endforeach
      </div>
    @endif
    @if (! empty($p->locations[3]))
      <div id="fork-portal-end">
        @foreach ($p->locations[3] as $panel)
{!! $panel->html !!}
        @endforeach
      </div>
    @endif
    </div>
    <!-- PRE mainAfter -->
    <!-- PRE end -->
