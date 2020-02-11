            <form
            id="{{ $form->getId() }}"
            method="POST"
            action="{{ $form->getActionUrl() }}"
            ßaccept-charset="UTF-8" class="kt-form" enctype="multipart/form-data">
                <div class="kt-portlet__body">
                    @if (strtolower($form->getMethod()) == 'update')
                    <input name="_method" type="hidden" value="PUT">
                    @endif
                    {!! csrf_field() !!}
                    {!! $form->show(false) !!}
                </div>
            </form>


@push('scripts-footer')
<script>
{!! $form->javascript() !!}
</script>
{!! $form->getResource()->validator()->selector('#'.$form->getId()) !!}
@endpush
