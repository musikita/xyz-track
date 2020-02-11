@extends('suitcorecms::template')

@section('content')

@if (isset($multiResources))
@include('suitcorecms::crud.multiresource')
@else
@include($resource->getChildView(), ['childResource' => $resource])
@endif

@endsection
