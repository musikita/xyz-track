<div class="kt-portlet kt-portlet--mobile mt-3">
    <div class="kt-portlet__body pb-0">
        <ul id="{{ $theId = 'section-'.uniqid() }}" class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
        @foreach ($multiResources as $childResource)
            @if (view()->exists($childView = $childResource->getChildView()))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#_{{ snake_case($childResource->getName(), '_') }}_" role="tab" aria-selected="false"><h5>{{strtoupper($childResource->getTitle() ?? $childResource->getName())}}</h5></a>
            </li>
            @endif
        @endforeach
         </ul>
    </div>
    <div class="tab-content" style="margin-top: -2rem; margin-bottom: -2rem">
    @foreach ($multiResources as $childResource)
        @if (view()->exists($childView = $childResource->getChildView()))
        <div class="tab-pane" id="_{{ snake_case($childResource->getName(), '_') }}_" role="tabpanel">
            @include($childView)
        </div>
        @endif
    @endforeach
    </div>
</div>

@push('scripts-footer')
<script type="text/javascript">
(function ($, window) {
    let _tab = $('#{{$theId}}.nav-tabs');
    if (_tab.length){
        __obj = _tab.find('li:first-child a');
        if ((hash = location.hash)) {
            if (_tab.find('[href="'+hash+'"]').length) {
                __obj = _tab.find('[href="'+hash+'"]');
            }
        }
        __obj.tab('show')
    }
})(jQuery, window)
</script>
@endpush
