<?php

namespace App\Http\Controllers;

use App\Mail\AuctionBidMailManager;
use App\Models\AuctionProductBid;
use App\Models\Product;
use Illuminate\Http\Request;
use Mail;

class AuctionProductBidController extends Controller
{
    // ---------- Admin ----------

    public function product_bids_admin($id)
    {
        $product = Product::where('auction_product', 1)->findOrFail($id);
        $bids = AuctionProductBid::where('product_id', $id)->with('user')->orderBy('amount', 'desc')->paginate(15);
        return view('auction.backend.bids.index', compact('product', 'bids'));
    }

    public function bid_destroy_admin($id)
    {
        $bid = AuctionProductBid::findOrFail($id);
        $bid->delete();
        flash(translate('Bid has been deleted successfully'))->success();
        return redirect()->route('product_bids.admin', $bid->product_id);
    }

    // ---------- Seller ----------

    public function product_bids_seller($id)
    {
        $product = Product::where('auction_product', 1)->where('user_id', auth()->id())->findOrFail($id);
        $bids = AuctionProductBid::where('product_id', $id)->with('user')->orderBy('amount', 'desc')->paginate(15);
        return view('auction.seller.bids.index', compact('product', 'bids'));
    }

    public function bid_destroy_seller($id)
    {
        $bid = AuctionProductBid::findOrFail($id);
        $product = Product::find($bid->product_id);
        if (!$product || $product->user_id != auth()->id()) {
            abort(403);
        }
        $bid->delete();
        flash(translate('Bid has been deleted successfully'))->success();
        return redirect()->route('product_bids.seller', $bid->product_id);
    }

    // ---------- Resource (for bid modal form) ----------

    public function index()
    {
        return redirect()->route('auction_product.purchase_history');
    }

    public function create()
    {
        return redirect()->route('auction_products.all');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount'     => 'required|numeric|min:0',
        ]);

        $product = Product::where('auction_product', 1)->findOrFail($request->product_id);
        $highest_bid = AuctionProductBid::where('product_id', $product->id)->orderBy('amount', 'desc')->first();
        $min_amount = $highest_bid ? ($highest_bid->amount + 1) : $product->starting_bid;
        if ($request->amount < $min_amount) {
            flash(translate('Your bid must be at least') . ' ' . single_price($min_amount))->error();
            return back();
        }

        if ($product->user_id == auth()->id()) {
            flash(translate('Seller cannot place bid on own product'))->error();
            return back();
        }

        $bid = AuctionProductBid::where('product_id', $request->product_id)->where('user_id', auth()->id())->first();
        if (!$bid) {
            $bid = new AuctionProductBid;
            $bid->user_id = auth()->id();
        }
        $bid->product_id = $request->product_id;
        $bid->amount = $request->amount;
        $bid->save();

        $second_max = AuctionProductBid::where('product_id', $request->product_id)->orderBy('amount', 'desc')->skip(1)->first();
        if ($second_max && $second_max->user && $second_max->user->email) {
            $array = [
                'view'    => 'emails.auction_bid',
                'subject' => translate('Auction Bid'),
                'from'    => env('MAIL_FROM_ADDRESS'),
                'content' => 'Hi! A new user bidded more than you for the product, ' . $product->getTranslation('name') . '. Highest bid amount: ' . $bid->amount,
                'link'    => route('auction-product', $product->slug),
            ];
            try {
                Mail::to($second_max->user->email)->queue(new AuctionBidMailManager($array));
            } catch (\Exception $e) {
                // ignore
            }
        }

        flash(translate('Bid placed successfully'))->success();
        return back();
    }

    public function show($id)
    {
        return redirect()->route('auction_products.all');
    }

    public function edit($id)
    {
        return redirect()->route('auction_products.all');
    }

    public function update(Request $request, $id)
    {
        return $this->store($request);
    }

    public function destroy($id)
    {
        return redirect()->route('auction_products.all');
    }
}
