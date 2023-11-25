<div class="card">
    <div class="card-body">
        {{-- <h4 class="card-title">Seller Detail</h4> --}}
        <div class="d-flex flex-column align-items-center text-center">
            <img src="{{ asset($seller_det->sellerprofile) }}" alt="Seller"
                class="img-thumb p-1 bg-secondary" width="250">
            <div class="mt-3">
                <h4>{{ $seller_det->seller_full_name_buss }}</h4>
                <div class="d-flex justify-content-center">
                    <a href = "mailto: {{ $seller_det->selleremail }}" class="badge badge-success mx-2">{{ $seller_det->selleremail }}</a>
                    <a href="tel:+{{ $seller_det->mobile }}" class="badge badge-dark">{{ $seller_det->mobile }}</a>
                </div>

                {{-- <p class="text-secondary mb-1">{{ $seller_det->selleremail }}</p> --}}
                <h6 class="mt-5">About</h6>
                <hr class="mb-2">
                <p class="text-muted font-size-sm">
                    @if(is_null($seller_det->sellerabout))
                        -
                    @else
                        {{ $seller_det->sellerabout }}
                    @endif
                </p>
            </div>
        </div>
        {{-- <hr class="my-4"> --}}
        <h6 class="mt-5">Trade License</h6>
        <hr>
        <table class="table ">
            <tbody>
                <tr>
                    <td>View License</td>
                    <td> <button type="button" class="btn btn-outline-secondary btn-sm view_trade_license"
                        data-seller_name="{{ $seller_det->sellername }}"
                        data-trade_licenseurl='{{ asset($seller_det->seller_trade_license) }}'>View License</button></td>
                </tr>
                <tr>
                    <td>Expiry Date</td>
                    <td>
                        <span class="badge rounded-pill bg-dark text-white">{{ $seller_det->seller_trade_exp_dt }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
