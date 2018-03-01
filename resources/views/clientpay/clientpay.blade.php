@extends('admin::curd.index')
@section('heading')
<i class="fa fa-file-text-o"></i> {!! trans('user::user.user.name') !!} <small> {!! trans('cms.manage') !!} {!! trans('user::user.user.names') !!}</small>
@stop
@section('title')
{!! trans('user::user.user.names') !!}
@stop
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{!! trans_url('admin') !!}"><i class="fa fa-dashboard"></i> {!! trans('cms.home') !!} </a></li>
    <li class="active">{!! trans('user::user.user.names') !!}</li>
</ol>
@stop
@section('entry')
<div class="box box-warning" id='entry-user'>
</div>
@stop
@section('content')
<table id="main-list" class="table table-striped table-bordered">
    <thead>
        <th>Client</th>
        <th>Start date</th>
        <th>End date</th>
        <th>Elapsed</th>
        <th>Days left</th>
        <th>Type</th>
        <th>Price</th>
        <th>Count</th>
        <th>Discount</th>
        <th>Amount</th>
        <th>Paid</th>
        <th>Pay</th>
        <th>Reminder</th>
    </thead>
    <tfoot>
      <tr>
        <th colspan="9" style="text-align:right">Total:</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
</table>
@stop
@section('script')
<script type="text/javascript">

var oTable;
$(document).ready(function(){

/*
jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
    return this.flatten().reduce( function ( a, b ) {
        if ( typeof a === 'string' ) {
            a = a.replace(/[^\d.-]/g, '') * 1;
        }
        if ( typeof b === 'string' ) {
            b = b.replace(/[^\d.-]/g, '') * 1;
        }

        return a + b;
    }, 0 );
} );
*/


    //$('#entry-user').load('{{trans_url('admin/clients/calc/0')}}');
    oTable = $('#main-list').DataTable( {
        "ajax": '{{ trans_url('/admin/clients/calc') }}',
        "columns": [
            { "data": "client" },
            { "data": "created_at" },
            { "data": "status.end_date_str" },
            { "data": "status.elapsed" },
            { "data": "status.days_left" },
            { "data": "type" },
            { "data": "status.price" },
            { "data": "count" },
            { "data": "discount" },
            { "data": "status.amount" },
            { "data": "paid" },
            { "data": "status.pay" },
            { "data": "status.reminder_date_str" },
        ],
        "userLength": 50,
        "order": [[ 4, "asc" ]],
        "rowCallback": function( row, data, index ) {
            //console.log(data);
            if (data.status.days_left <= 0) {
                $('td:eq(4)', row).addClass('danger');
            } else if (data.status.days_left <= 5) {
                $('td:eq(4)', row).addClass('warning');
            }

            if (data.status.amount <= data.paid && data.status.days_left <= 0) {
                $('td:eq(4)', row).removeClass('danger').addClass('success').html('Paid');
            }

            if (data.paid <= 0) {
                $('td:eq(10)', row).addClass('danger');
            } else if (data.paid <= 5) {
                $('td:eq(10)', row).addClass('warning');
            }

            $('td:eq(8)', row).html(data.discount + ' %');


            $('td:eq(6)', row).html('€ <abbr title="Day price: € ' + data.status.day_price + '">' + data.status.price + '</abbr>');
            $('td:eq(9)', row).html('€ ' + data.status.amount);
            $('td:eq(10)', row).html('€ <abbr title="Paid days: ' + data.status.paid_for_days + '  ' + data.status.paid_till.date + '">' + data.paid + '</abbr>');
            $('td:eq(11)', row).html('€ ' + data.status.pay);

            if (data.status.pay >= 25) {
              $('td:eq(11)', row).addClass('warning');
            }
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            totalCost = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            totalPaid = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            totalPay = api
                .column( 11 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            /*
            // Total over this page
            pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            */

            // Update footer
            $( api.column( 9 ).footer() ).html(
                //'€'+pageTotal +' ( €'+ totalCost +' total)'
                '€ '+Number((totalCost).toFixed(2))
            );

            $( api.column( 10 ).footer() ).html(
                '€ '+Number((totalPaid).toFixed(2))
            );

            $( api.column( 11 ).footer() ).html(
                '€ '+Number((totalPay).toFixed(2))
            );
        }
    });
    $('#main-list tbody').on( 'click', 'tr', function () {
        $(this).toggleClass("selected").siblings(".selected").removeClass("selected");
        var d = $('#main-list').DataTable().row( this ).data();
        $('#entry-user').load('{{trans_url('admin/user/user')}}' + '/' + d.id);
    });

    $('.filter-role').on( 'click', function (e) {
        role = $( this ).data( "role" );

        oTable.ajax.url('{!! trans_url('/admin/user/user?role=') !!}' + role).load();
        e.preventDefault();
    });
});
</script>
@stop
@section('style')
@stop
