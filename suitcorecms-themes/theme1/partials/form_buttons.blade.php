<style>
    form.loading .kt-portlet__body {
        position: relative;
    }
    form.loading .kt-portlet__body::after {
        content: "";
        height: 100%;
        width: 100%;
        background: #f2f2f8ba;
        top: 0;
        position: absolute;
        left: 0;
    }
</style>
<div class="btn-group">
    <a href="javascript: saveAndBack()" class="btn btn-brand"><i id="icon-loader" class="la la-save"></i> <span class="kt-hidden-mobile">Save &amp; Back</span></a>

    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <ul class="kt-nav">
            <li class="kt-nav__item">
                <a href="javascript: save()" class="kt-nav__link">
                    <i class="kt-nav__link-icon la la-save"></i>
                    <span class="kt-nav__link-text">Save &amp; Keep</span>
                </a>
            </li>
            <li class="kt-nav__item">
                <a href="javascript: saveAndDetail()" class="kt-nav__link">
                    <i class="kt-nav__link-icon la la-save"></i>
                    <span class="kt-nav__link-text">Save &amp; Detail</span>
                </a>
            </li>
            @if ($resource->getMethod() == 'create')
            <li class="kt-nav__item">
                <a href="javascript: saveAndNew()" class="kt-nav__link">
                    <i class="kt-nav__link-icon la la-save"></i>
                    <span class="kt-nav__link-text">Save &amp; New</span>
                </a>
            </li>
            @endif
            @if ($resource->getMethod() == 'update' && $resource->routeExist('destroy'))
            <li class="kt-nav__item">
                <form action="{{$resource->routeDestroy()}}" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    {{csrf_field()}}
                </form>
                <a href="javascript: doDelete()" id="deleteFormButton" class="kt-nav__link">
                    <i class="kt-nav__link-icon la la-trash"></i>
                    <span class="kt-nav__link-text">Delete</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>

@push('scripts-footer')
<script type="text/javascript">
    var rForm = $('#{{ $resource->form()->getId() }}');
    var loader = $('#icon-loader');
    var loadingStart = function () {
        rForm.addClass('loading');
        loader.parents('a').addClass('disabled').siblings('button').addClass('disabled');
        loader.removeClass('la la-save').addClass('fa fa-spinner fa-spin');
    }
    var loadingEnd = function () {
        rForm.removeClass('loading');
        loader.parents('a').removeClass('disabled').siblings('button').removeClass('disabled');
        loader.removeClass('fa fa-spinner fa-spin').addClass('la la-save');
    }
    var formAlert = function (message, title, type, icon = 'la la-warning') {
        $.notify({message: message, title: title, icon: icon}, {type: type, placement: {from: 'top', align: 'center'}})
    }
    var formSubmit = function (success = null) {
        rForm.data('validator').settings.submitHandler = function (form, e) {
            e.preventDefault();
            loadingStart();
            var formdata = new FormData(rForm[0]);
            rForm.find('[richtext]').each(function (i, v) {
                var richId = $(v).attr('id');
                var richName = $(v).attr('name');
                formdata.set(richName, tinymce.get(richId).getContent());
            })
            $.ajax({
                url         : rForm.attr('action'),
                data        : formdata,
                cache       : false,
                contentType : false,
                processData : false,
                type        : 'POST',
                success     : function (response) {
                    formAlert(response.message ? response.message : '', 'Saving Success', 'success');
                    if (success) {
                        success(response);
                    }
                }
            }).fail(function (xhr) {
                formAlert(xhr.statusText, 'Saving Error', 'warning');
            }).always(function () {
                loadingEnd();
            });
        }
        rForm.submit();
    }
    var save = function () {
        formSubmit(function (response) {
            if (url = response.url) {
                if (url != window.location.href || rForm.find(':file').length) {
                    window.location.replace(url);
                }
            }
        });
    }
    var saveAndNew = function () {
        formSubmit(function (response) {
            rForm.find('input, textarea').not(':hidden').val(null);
            rForm.find('[select2]').select2('val', null);
        });
    }
    var saveAndDetail = function () {
        formSubmit(function (response) {
            if (url = response.url) {
                window.location.replace(url.replace('/edit', ''))
            }
        });
    }
    var saveAndBack = function () {
        formSubmit(function (response) {
            window.location.replace('{{ url()->previous() }}');
        });
    }
    var doDelete = function () {
        return confirm('Are you sure?') ? $('#deleteFormButton').siblings('form').submit() : false;
    }
</script>
@endpush()
