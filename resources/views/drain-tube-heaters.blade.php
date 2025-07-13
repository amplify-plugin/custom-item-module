@pushOnce('footer-script')
<script src="{{asset("js/app.js")}}"></script>
@endPushOnce

<div id="app">
    <custom-item current-component='drain-tube-heaters'/>
</div>
@include('cms::inc.full-page-loader')