@pushOnce('footer-script')
<script src="{{mix("js/backend.js", "vendor/backend")}}"></script>
@endPushOnce

<div id="app">
    <custom-item current-component='gaskets'/>
</div>
@include('cms::inc.full-page-loader')
