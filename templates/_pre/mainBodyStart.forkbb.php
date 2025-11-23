@if ($p->removeFWithNav)
  <div id="fork" @class(['f-pm-flash' => $p->fPMFlash, [$p->identifier, 'pg-']]) data-page-scroll="{!! (int) $p->user->page_scroll !!}">
@else
