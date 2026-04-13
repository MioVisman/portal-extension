        <section class="f-panel f-panel-empty @if ($topic) f-panel-to-topic @endif">
          <h2 class="f-panel-header">{!! __($panel->name) !!}</h2>
          <div class="f-panel-body">
            <div class="f-panel-content">{!! $panel->content !!}</div>
          </div>
@if ($topic)
          <footer class="f-panel-topic-footer">
            <address class="f-panel-topic-address">
    @if ($userRules->viewUsers && $author->link)
              <span class="f-panel-topic-author" title="{{ __('Author') }}"><a href="{{ $author->link }}" rel="author">{{ $author->username }}</a></span>
    @else
              <span class="f-panel-topic-author" title="{{ __('Author') }}">{{ $author->username }}</span>
    @endif
            </address>
            <span class="f-panel-topict-posted" title="{{ __('Published') }}"><time datetime="{{ \gmdate('c', $topic->posted) }}">{{ dt($topic->posted, null, 0, true) }}</time></span>
    @if ($topic->showViews)
            <span class="f-panel-topict-view-count" title="{{ __('Views') }}">{{ num($topic->num_views) }}</span>
    @endif
            <span class="f-panel-topict-cmm-count" title="{{ __('Comments') }}"><a href="{{ $topic->link }}#p{!! (int) $topic->first_post_id !!}">{{ num($topic->num_replies) }}</a></span>
          </footer>
@endif
        </section>
