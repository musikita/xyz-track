<style>
    .kt-portlet__head-label {
        max-width: 60%;
    }
    @media (max-width: 768px) {
        .kt-portlet__head-label {
            max-width: 40%;
        }
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" id="kt_page_portlet">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
{{$childResource->getCaption()}}
                        <small class="kt-hidden-mobile">
{{$childResource->getTitle() ?? 'Detail of '.Illuminate\Support\Str::singular($childResource->getName())}}
                        </small>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        @if ($childResource->routeExist('index') && !$childResource->getParent())
                        <a href="{{$childResource->routeIndex()}}" class="btn btn-clean kt-margin-r-10">
                            <i class="la la-arrow-left"></i>
                            <span class="kt-hidden-mobile">
                                Back To All
                            </span>
                        </a>
                        @endif
                        {!!
                            crud_button_group(
                                $childResource->buttons(),
                                $childResource->routeExist('edit')
                                    ? cms_button_new('Edit')
                                        ->url($childResource->routeEdit())
                                        ->icon('la la-pencil')
                                    : null
                            )
                        !!}
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
{!! $childResource->show() !!}
                <!--end: Datatable -->
            </div>
            <div class="kt-portlet__foot">
                
            </div>
        </div>
    </div>
</div>

{!! $childResource->showJavascript() !!}

@if (count($multiResources = $childResource->children()))
@include('suitcorecms::crud.multiresource')
@endif

@if (count($childResource->buttons()))
@push('scripts-footer')
<script>
@foreach($childResource->buttons() as $button)
    {!!$button->showJavascript()!!}
@endforeach
</script>
@endpush
@endif
