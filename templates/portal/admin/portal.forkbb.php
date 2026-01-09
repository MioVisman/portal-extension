@extends ('layouts/admin')
      <section id="fork-portal-settings" class="f-admin">
        <h2>{!! __('Settings head') !!}</h2>
        <div class="f-fdiv">
@if ($form = $p->formSettings)
    @include ('layouts/form')
@endif
        </div>
      </section>
      <section @class(['f-admin', [$p->classForm, 'f-', '-form']])>
        <h2>{!! __($p->titleForm) !!}</h2>
        <div class="f-fdiv">
@if ($form = $p->form)
    @include ('layouts/form')
@endif
        </div>
      </section>
