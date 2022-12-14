<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\facades\Auth;
use ECPay_PaymentMethod as ECPayMethod;
use ECPay_AllInOne as ECPay;

// use TsaiYiHua\ECPay\Checkout;

class CheckoutController extends Controller
{
    //
    protected $checkout;

    // public function __construct(Checkout $checkout)
    // {
    //     $this->checkout = $checkout;
    //     $this->checkout->setReturnUrl(url('ec-order/callback'));
    // }

    public function index(){
        
        $old_cartitems = Cart::where('user_id', Auth::id())->get();
        //checkout quantity = 0;
        foreach ($old_cartitems as $item) {
            # code...
            /*if(!Product::where('id',$item->prod_id)->where('quantity','>=',$item->prod_qty)->exists()){
                $removeitem = Cart::where('user_id', Auth::id())->where('prod_id',$item->prod_id)->first();
                $removeitem->delete();
            }*/
           
        }
        $cartitems = Cart::where('user_id', Auth::id())->get();

        return view('frontend.checkout',compact('cartitems'));
    }

    public function placeorder(Request $request)
    {
        # code...
        $order = new Order();
        $order->user_id = Auth::id();
        $order->firstname = $request->input('firstname');
        $order->lastname = $request->input('lastname');
        $order->email = $request->input('email');
        $order->phone = $request->input('phone');
        $order->address = $request->input('address');
        $order->city = $request->input('city');
        $order->country = $request->input('country');
        $order->pincode = $request->input('pincode');

        $order->payment_mode = $request->input('payment_mode');
        $order->payment_id = $request->input('payment_id');
        

        //total
        $total = 0;
        $cartitems_total = Cart::where('user_id',Auth::id())->get();
        foreach ($cartitems_total as $prod) {
            # code...
            $total += ($prod->products->selling_price * $prod->prod_qty);
        }
        $order->total_price = $total;

        $order->tracking_no = 'jinyan'.rand(1111,9999);
        $order->save();

        $cartitems = Cart::where('user_id',Auth::id())->get();
        foreach ($cartitems as $item) {
            # code...
            OrderItem::create([
                'order_id' => $order->id,
                'prod_id' => $item->prod_id,
                'quantity' => $item->prod_qty,
                'price' => $item->products->selling_price,
            ]);

            $prod = Product::where('id',$item->prod_id)->first();
            $prod->quantity = $prod->quantity - $item->prod_qty;
            $prod->update();
        }
        
        //add that's user content after address is null 
        if (Auth::user()->address == NULL) {
            # code...
            $user = User::where('id',Auth::id())->first();
            $user->name = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->city = $request->input('city');
            $user->country = $request->input('country');
            $user->pincode = $request->input('pincode');
            $user->update();
        }
        $cartitems = Cart::where('user_id', Auth::id())->get();
        Cart::destroy($cartitems);

        if($request->input('payment_mode') == 'Paid by Paypal'){
            return response()->json(['status'=>"????????????Paypal?????????"]);
        }
        return redirect('/')->with('status',"???????????????");
    }

    public function razorpaycheck(Request $request){

        $cartitems = Cart::where('user_id', Auth::id())->get();
        $total_price = 0;
        foreach ($cartitems as $item) {
            # code...
            $total_price += $item->products->selling_price * $item->prod_qty;
        }

        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $city = $request->input('city');
        $country = $request->input('country');
        $pincode = $request->input('pincode');

        return response()->json([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'pincode' => $pincode,
            'total_price' => $total_price
        ]);
    }

    public function checkout(Request $request){
        //????????????&??????
        $order = new Order();
        $order->user_id = Auth::id();
        $order->firstname = $request->input('firstname');
        $order->lastname = $request->input('lastname');
        $order->email = $request->input('email');
        $order->phone = $request->input('phone');
        $order->address = $request->input('address');
        $order->city = $request->input('city');
        $order->country = $request->input('country');
        $order->pincode = $request->input('pincode');
        //total
        $total = 0;
        $cartitems_total = Cart::where('user_id',Auth::id())->get();
        foreach ($cartitems_total as $prod) {
            # code...
            $total += ($prod->products->selling_price * $prod->prod_qty);
        }
        $order->total_price = $total;

        
        //total
        $total = 0;
        $cartitems_total = Cart::where('user_id',Auth::id())->get();
        foreach ($cartitems_total as $prod) {
            # code...
            $total += ($prod->products->selling_price * $prod->prod_qty);
        }
        $order->total_price = $total;

        $order->tracking_no = 'jinyan'.rand(1111,9999);
        $order->save();

        $cartitems = Cart::where('user_id',Auth::id())->get();
        foreach ($cartitems as $item) {
            # code...
            OrderItem::create([
                'order_id' => $order->id,
                'prod_id' => $item->prod_id,
                'quantity' => $item->prod_qty,
                'price' => $item->products->selling_price,
            ]);

            $prod = Product::where('id',$item->prod_id)->first();
            $prod->quantity = $prod->quantity - $item->prod_qty;
            $prod->update();
        }
        

       
        //???????????????????????????
        
        try {
            include('ECPay.Payment.Integration.php');
            $obj = new ECPay();
       
            //????????????
            $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";   //????????????
            $obj->HashKey     = '5294y06JbISpM5x9' ;                                           //?????????Hashkey??????????????????ECPay?????????HashKey
            $obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                           //?????????HashIV??????????????????ECPay?????????HashIV
            $obj->MerchantID  = '2000132';                                                     //?????????MerchantID??????????????????ECPay?????????MerchantID
            $obj->EncryptType = '1';                                                           //CheckMacValue??????????????????????????????1?????????SHA256??????
    
    
            //????????????(??????????????????????????????)
            $MerchantTradeNo = "Test".time() ;
            $obj->Send['ReturnURL']         = "https://d8b8-36-235-153-229.jp.ngrok.io/callback" ;    //?????????????????????????????????
            $obj->Send['MerchantTradeNo']   = $MerchantTradeNo ;                         //????????????
            $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //????????????
            $obj->Send['TotalAmount']       = $total;                                      //????????????
            $obj->Send['TradeDesc']         = "good to drink" ;                          //????????????
            $obj->Send['ChoosePayment']     = ECPayMethod::ALL ;                 //????????????:ATM
            $obj->Send['CustomField1']      = $order->id; //???????????????1
            $obj->Send['CustomField2']      = Auth::user()->id;//???????????????2
    
            //?????????????????????
            array_push($obj->Send['Items'], 
            array('Name' => "????????????????????????", 'Price' => $total, 'Currency' => "???", 'Quantity' => (int) "1", 'URL' => "dedwed"));
            
            //ATM ????????????(????????????????????????????????????)
            $obj->SendExtend['ExpireDate'] = 3 ;     //???????????? (??????3????????????60????????????1???)
            $obj->SendExtend['PaymentInfoURL'] = ""; //???????????????????????????????????????
    
            # ??????????????????
             /*
            $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
            $obj->SendExtend['RelateNumber'] = "Test".time();
            $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
            $obj->SendExtend['CustomerPhone'] = '0911222333';
            $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
            $obj->SendExtend['CustomerAddr'] = '???????????????????????????19-2???5???D???';
            $obj->SendExtend['InvoiceItems'] = array();
            // ?????????????????????????????????????????????
            foreach ($obj->Send['Items'] as $info)
            {
                array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                    $info['Quantity'],'Word' => '???','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
            }
            $obj->SendExtend['InvoiceRemark'] = '??????????????????';
            $obj->SendExtend['DelayDay'] = '0';
            $obj->SendExtend['InvType'] = ECPay_InvType::General;
            */
    
            
            //????????????(auto submit???ECPay)
            $order->update([
                'payment_id'        => $MerchantTradeNo,
                'payment_mode'      => 'ECPAY credit',
                'total_price' => $request('TradeAmt'),
                'status' => '1',
            ]);
            $html = $obj->CheckOut();
            echo $html;
    
        } catch (Exception $e) {
            echo $e->getMessage();
        } 
    }
    

    //?????????????????????????????????
    public function checkoutCallback(Request $request)
    {
       
        $cartitems = Cart::where('user_id', Auth::id())->get();
        Cart::destroy($cartitems);
            
        return response()->json(['status'=>'??????ECPAY???' .'????????????' . $order->payment_id . '????????????']);
            // Log::info('????????????' . $order->payment_id . '????????????');
        
        return redirect('/'); //????????????
    }
}
