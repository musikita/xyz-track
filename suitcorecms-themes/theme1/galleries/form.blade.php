@foreach($form->items() as $item)
{!! $item->show(false) !!}
<hr>
@endforeach
<button type="button" class="btn btn-primary btn-sm btn-add">Add</button>
<template>
{!! $form->show(false) !!}
<hr>
</template>

