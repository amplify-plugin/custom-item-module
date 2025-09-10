@pushOnce('footer-script')
<script src="{{mix("js/backend.js", "vendor/backend")}}"></script>
@endPushOnce

<div id="app">
    <custom-item current-component='wire-shelf'/>
</div>
@include('cms::inc.full-page-loader')
