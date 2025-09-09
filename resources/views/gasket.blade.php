@pushOnce('footer-script')
<script src="{{asset("assets/js/app.js")}}"></script>
@endPushOnce

<div id="app">
    <custom-item current-component='gaskets'/>
</div>
@include('cms::inc.full-page-loader')
