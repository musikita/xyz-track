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
{{array_last($breadcrumb->getItems())}}
                        </small>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        <a href="{{ url()->previous() }}" class="btn btn-clean kt-margin-r-10">
                            <i class="la la-arrow-left"></i>
                            <span class="kt-hidden-mobile">
                                Cancel
                            </span>
                        </a>
                        @include('suitcorecms::partials.form_buttons', ['resource' => $childResource])
                    </div>
                </div>
            </div>
            <!--begin: Form -->
            {!! $childResource->form()->show() !!}
            <!--end: Form -->
        </div>
    </div>
</div>