        <section class="f-panel f-panel-recent-posts">
          <h2 class="f-panel-header">{!! __('Recent posts') !!}</h2>
          <div class="f-panel-body">
            <ul class="f-panel-content">
@foreach ($topics as $topic)
    @if (! empty($topic))
              <li class="f-panel-recent-posts-li @if (false !== $topic->hasNew) f-fnew @endif">
                <a class="f-panel-recent-posts-a" href="{{ (false !== $topic->hasNew ? $topic->linkNew : $topic->linkLast) }}" title="{{ __(false !== $topic->hasNew ? 'New posts info' : 'Last post info') }}">{{ $topic->name }}</a>
              </li>
    @endif
@endforeach
            </ul>
          </div>
        </section>
