<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Gateway</title>
</head>
<body>
    <form action="{{route('paypal')}}" method="POST">
        <h1>PayPal Payment</h1>
        <h3>Product Laptop</h3>
        <h3>Price 10$</h3>
        <h3>Quantity 2</h3>
        <h3>=> Total Price is 20$</h3>
        @csrf
        <input type="hidden" name="price" value="10">
        <input type="hidden" name="product_name" id="product_name" value="Laptop">
        <input type="hidden" name="quantity" id="quantity" value="2">
        <input type="submit" value="Submit with PayPal">
    </form>
</body>
</html>