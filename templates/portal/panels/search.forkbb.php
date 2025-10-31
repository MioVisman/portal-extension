        <section class="f-panel f-panel-search">
          <h2 class="f-panel-header">{!! __('Search') !!}</h2>
          <div class="f-panel-body">
            <div class="f-fdiv">
              <form class="f-form" method="post" action="{{ $action }}">
                <fieldset id="id-fs-what">
                  <dl id="id-dl-keywords" class="f-ft-text f-field-w0">
                    <dt></dt>
                    <dd>
                      <input id="id-keywords" name="keywords" class="f-ctrl" type="text" minlength="1" maxlength="100" value="" required="" placeholder="{{ __('Keyword search') }}">
                    </dd>
                  </dl>
                </fieldset>
                <p class="f-btns">
                  <button class="f-btn f-fbtn" type="submit" name="search" value="{{ __('Search btn') }}" title="{{ __('Search btn') }}"><span>{{ __('Search btn') }}</span></button>
                </p>
              </form>
            </div>
          </div>
        </section>
