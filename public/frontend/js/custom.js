

$(document).ready(function(){
    loadcart();
    loadwishlist();

    function loadcart(){
        $.ajax({
            method: "GET",
            url: "/load-cart-data",
            success: function (response){
                $('.cart-count').html('');
                $('.cart-count').html(response.count);
            }
        });
    }

    function loadwishlist(){
        $.ajax({
            method: "GET",
            url: "/load-wishlist-data",
            success: function (response){
                $('.wish-count').html('');
                $('.wish-count').html(response.count);
            }
        });
    }

    $('.addCartBtn').click(function(e){
        e.preventDefault();

        var product_id = $(this).closest('.product_data').find('.prod_id').val();
        var product_qty = $(this).closest('.product_data').find('.qty-input').val();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: "POST",
            url: "/add-to-cart",
            data: {
                'product_id':product_id,
                'product_qty':product_qty,
            },
            success: function(response){
                swal(response.status);
            }
        });
    });

    $('.addToWishlist').click(function(e){
        e.preventDefault();

        var product_id = $(this).closest('.product_data').find('.prod_id').val();
        
        $.ajax({
            method: "POST",
            url: "/add-to-wishlist",
            data: {
                'product_id':product_id,
            },
            success: function(response){
                swal(response.status);
            }
        });
    });

    $('.incre-btn').click(function(e){
        e.preventDefault();

        var inc_value = $(this).closest('.product_data').find('.qty-input').val();
        var value = parseInt(inc_value,10);
        value = isNaN(value)? 0 : value;
        if(value<10){
            value++;
            $(this).closest('.product_data').find('.qty-input').val(value);
        }
    })

    $('.decre-btn').click(function(e){
        e.preventDefault();

        var dec_value = $(this).closest('.product_data').find('.qty-input').val();
        var value = parseInt(dec_value,10);
        value = isNaN(value)? 0 : value;
        if(value>1){
            value--;
            $(this).closest('.product_data').find('.qty-input').val(value);
        }
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.delete-cart-item').click(function(e){
        e.preventDefault();

        var prod_id = $(this).closest('.product_data').find('.prod_id').val();

        $.ajax({
            method: "POST",
            url: "delete-cart-item",
            data: {
                'prod_id':prod_id,
            },
            success: function(response){
                window.location.reload();
                swal("",response.status,"success");
            }
        });
    })

    $('.remove-wishlist-item').click(function(e){
        e.preventDefault();

        var prod_id = $(this).closest('.product_data').find('.prod_id').val();

        $.ajax({
            method: "POST",
            url: "delete-wishlist-item",
            data: {
                'prod_id':prod_id,
            },
            success: function(response){
                window.location.reload();
                swal("",response.status,"success");
            }
        });
    })
    

    $('.changeQty').click(function(e){
        e.preventDefault();

        var prod_id = $(this).closest('.product_data').find('.prod_id').val();
        var qty = $(this).closest('.product_data').find('.qty-input').val();
        data = {
            'prod_id':prod_id,
            'prod_qty':qty,
        }

        $.ajax({
            method: "POST",
            url: "update-cart",
            data: data,
            success: function(response){
                 window.location.reload();
            }
        });
    })
});

