<style>
    .custom-item-card {
        padding: 1.25rem;
    }
    .custom-item-card .card-body{
        padding: 1.25rem 0 0 0 !important;
    }
    .custom-item-img-container {
        height: 300px;
        width: 100%;
        object-fit: contain;

    }
    .custom-item-img-container > img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    </style>
<div class="container padding-bottom-2x mb-2">
    <div class="row">
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="custom-item-card card">
                <div class="custom-item-img-container">
                    <img src="{{asset("/images/strip-curtains/complete.jpg")}}" alt="strip-curtains">
                </div>
                <div class="card-body">
                    <h5 class="fw-600 my-4">ORDER COMPLETE RHS CUSTOM STRIP CURTAINS</h5>
                    <a href="{{$completedUrl}}" class="text-white btn btn-primary btn-block">Order Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="custom-item-card card">
                <div class="custom-item-img-container">
                    <img src="{{asset("/images/strip-curtains/replacement.jpg")}}" alt="strip-curtains">
                </div>
                <div class="card-body">
                    <h5 class="fw-600 my-4">ORDER REPLACEMENT STRIPS FOR RHS CURTAINS</h5>
                    <a href="{{ $replacementUrl}}" class="text-white btn btn-primary btn-block">Order Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="custom-item-card card">
                <div class="custom-item-img-container">
                    <img src="{{asset("/images/strip-curtains/material.jpg")}}" alt="strip-curtains">
                </div>
                <div class="card-body">
                    <h5 class="fw-600 my-4">ORDER BULK STRIP MATERIAL FOR STRIP CURTAINS</h5>
                    <a href="{{$bulkUrl}}" class="text-white btn btn-primary btn-block">Order Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

