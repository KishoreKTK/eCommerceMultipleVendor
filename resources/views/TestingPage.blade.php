<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Starling</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 border border-2 rounded shadow shadow-2 bg-white p-3 m-2 text-center">
                <h6>AWS Image Upload Test</h6>
                <hr size="3">
                <input type="file" name="image" id="image">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        <br>
        <h4 class="card-title">Demo Purchase Page</h4>
        <hr size="3">
        <div class="row row-eq-height">
            @foreach ($productlist as $product)
            <div class="col-2 p-2">
                <div class="card">
                    <img class="card-img-top img-thumbnail" height="250" src="{{ asset($product->image) }}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <button class="btn btn-primary d-flex justify-content-center buy_product" data-price='{{ $product->product_price }}'>Buy</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://www.foloosi.com/js/foloosipay.v2.js"></script>
<script type="text/javascript">

$('body').on("click",'.buy_product',function(){
    var base_url = window.location.origin;
    product_price = $(this).attr('data-price');
    console.log(product_price);
    $.ajax({
            type: "GET",
            url: base_url + '/testing/payproduct?product_price=' + product_price,
            success: function(msg) {
                if (msg['status'] == true) {
                    var data    =   msg['response'];
                    var reference_token = msg['response'].data.reference_token;
                    var options = {
                        "reference_token" : reference_token,
                        "merchant_key" : "test_$2y$10$X9WG5PnfdjLJ3QQ-KLvMIOgt2yzHyXkVJe.vYlxkg1-ggIeCeE.om"
                    }
                    var fp1 = new Foloosipay(options);
                    fp1.open();
                    foloosiHandler(response, function (e) {
                        if(e.data.status == 'success'){
                            console.log(e.data);
                        }
                        if(e.data.status == 'error'){
                            console.log(e.data);
                        }
                        if(e.data.status == 'closed'){
                            console.log(e.data);
                        }
                    });
                } else {
                    alert("something went wrong");
                }
            }
        });
});

</script>


</html>
