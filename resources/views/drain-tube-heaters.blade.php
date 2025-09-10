@pushOnce('footer-script')
<script src="{{asset("vendor/backend/js/backend.js")}}"></script>
@endPushOnce

<div id="app">
    <custom-item current-component='drain-tube-heaters'/>
</div>
@include('cms::inc.full-page-loader')
