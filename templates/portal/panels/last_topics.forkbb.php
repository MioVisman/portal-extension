        <section class="f-panel f-panel-last-topics">
          <h2 class="f-panel-header">{!! __($panel->name) !!}</h2>
          <div class="f-panel-body">
@foreach ($posts as $post)
    @if (! empty($post))
            <article class="f-panel-topic">
              <h3 class="f-panel-topic-header">
                <span><a href="{{ $post->parent->link }}">{{ $post->parent->name }}</a></span>
              </h3>
              <div class="f-panel-topic-body f-post-body">
                <div class="f-post-main">
                  {!! $post->html() !!}
        @if (true === $post->needReadMore)
                  <p class="f-panel-topic-rm"><a href="{{ $post->parent->link }}#p{!! (int) $post->id !!}">{!! __('Read more')!!}</a></p>
        @endif
                </div>
              </div>
              <footer class="f-panel-topic-footer">
                <address class="f-panel-topic-address">
        @if ($userRules->viewUsers && $post->user->link)
                  <span class="f-panel-topic-author" title="{{ __('Author') }}"><a href="{{ $post->user->link }}" rel="author">{{ $post->user->username }}</a></span>
        @else
                  <span class="f-panel-topic-author" title="{{ __('Author') }}">{{ $post->user->username }}</span>
        @endif
                </address>
                <span class="f-panel-topict-posted" title="{{ __('Published') }}"><time datetime="{{ \gmdate('c', $post->posted) }}">{{ dt($post->posted, null, 0, true) }}</time></span>
                <span class="f-panel-topict-view-count" title="{{ __('Views') }}">{{ num($post->parent->num_views) }}</span>
                <span class="f-panel-topict-cmm-count" title="{{ __('Comments') }}"><a href="{{ $post->parent->link }}#p{!! (int) $post->id !!}">{{ num($post->parent->num_replies) }}</a></span>
              </footer>
            </article>
    @endif
@endforeach
          </div>
        </section>
