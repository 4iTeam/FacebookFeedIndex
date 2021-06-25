@if(isset($breadcrumb))
    {{Breadcrumbs::render($breadcrumb,get_defined_vars())}}
@else
    {{Breadcrumbs::render()}}
@endif