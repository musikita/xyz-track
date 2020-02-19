<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">

<div class="mb-4">
        @if ($childResource->routeExist('create'))
        <a href="{{$childResource->routeCreate()}}" class="btn btn-sm btn-primary btn-icon-h">
            Add New &nbsp;
            <i class="flaticon2-plus"></i>
        </a>
        @endif
        <div class="dropdown d-inline-block selected-menu">
            <button class="btn btn-sm btn-outline-primary primary btn-icon-h dropdown-toggle bulk-selected-dropdown" disabled="disabled" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                @if ($childResource->routeExist('bulk-delete'))
                    <form class="form-bulk-delete" action="{{$childResource->route('bulk-delete')}}" method="POST" onsubmit="return $.BulkDelete(this)">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="deleted_ids">
                        @csrf
                    </form>
                    <a class="dropdown-item btn-bulk-delete-datatables btn-danger" href="#bulk-delete" onclick="return (confirm(&quot;Are you sure?&quot;) ? $(this).siblings(&quot;form&quot;).submit() : false);"><i class="la la-trash text-white"></i> Delete</a>
                @endif
            </div>
        </div>

    <div class="pull-right">
        @if ($childResource->routeExist('export-csv'))
        <a href="{{$childResource->route('export-csv')}}" class="btn btn-warning  btn-sm btn-icon-h btn-export-csv">
            Export CSV &nbsp;
            <i class="fa fa-file-csv"></i>
        </a>
        @endif
        @if ($childResource->routeExist('import'))
        <a href="{{$childResource->route('import')}}" class="btn btn-primary  btn-sm btn-icon-h">
            Import &nbsp;
            <i class="fa fa-file-excel"></i>
        </a>
        @endif
    </div>
</div>

<!--begin: Datatable -->
{!! $childResource->datatables()->table() !!}
<!--end: Datatable -->
    </div>
</div>
@push('scripts-footer')
{!! $childResource->datatables()->scripts() !!}
@endpush

@if (! defined('CRUD_DATATABLE_ONE_SCRIPT'))
@php
    define('CRUD_DATATABLE_ONE_SCRIPT', true);
@endphp
@push('scripts-footer')
<script>
    (function ($) {
        $.BulkDelete = function (form) {
            let deleted_ids = [];
            $(form).closest('.kt-portlet__body').find('.dataTables_wrapper .datatables-checkbox:checked').each(function (i, el) {
                deleted_ids.push(el.dataset.id);
            });
            $(form).find('[name="deleted_ids"]').val(deleted_ids);
            return true;
        }
        $.BulkSelect = function (obj) {
            const totalChecked = $(obj).closest('.dataTables_wrapper').find('.datatables-checkbox:checked').length;
            const button = $(obj).closest('.kt-portlet__body').find('.bulk-selected-dropdown');
            if (totalChecked) {
                button.removeAttr('disabled');
                button.text(totalChecked + ' selected');
            } else {
                button.prop('disabled', true);
                button.text('');
            }
        }
        $.BulkSelectAll = function (obj) {
            if (obj.checked == true) {
                $(obj).closest('table').find('.datatables-checkbox').prop('checked', true).trigger('change');
            } else {
                $(obj).closest('table').find('.datatables-checkbox').prop('checked', false).trigger('change');
            }
        }

        $('table.dataTable').on('draw', function () {
            console.log('draw');
            // .dataTable().api();
                // var $api = this.api();
                if (! $(this).data().count()) {
                    $(this).closest(`.kt-portlet__body`).find(`.btn-export-csv`).addClass(`disabled`);
                }

        })
    })(jQuery);
</script>
@endpush
@endif
