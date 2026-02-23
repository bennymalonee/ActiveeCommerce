<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuctionProductController extends Controller
{
    protected $auctionService;

    public function __construct(AuctionService $auctionService)
    {
        $this->auctionService = $auctionService;
    }

    // ---------- Admin ----------

    public function all_auction_product_list(Request $request)
    {
        $type = 'all';
        $products = Product::where('auction_product', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('auction.backend.products.index', compact('products', 'type'));
    }

    public function inhouse_auction_products(Request $request)
    {
        $type = 'inhouse';
        $products = Product::where('auction_product', 1)
            ->where('added_by', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('auction.backend.products.index', compact('products', 'type'));
    }

    public function seller_auction_products(Request $request)
    {
        $type = 'seller';
        $products = Product::where('auction_product', 1)
            ->where('added_by', 'seller')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('auction.backend.products.index', compact('products', 'type'));
    }

    public function product_create_admin()
    {
        $categories = Category::where('parent_id', 0)->where('digital', 0)->with('childrenCategories')->get();
        return view('auction.backend.products.create', compact('categories'));
    }

    public function product_store_admin(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_ids' => 'required|array',
            'starting_bid' => 'required|numeric|min:0',
            'auction_date_range' => 'required|string',
        ]);
        $this->auctionService->store($request);
        flash(translate('Auction product has been inserted successfully'))->success();
        return redirect()->route('auction.all_products');
    }

    public function product_edit_admin($id)
    {
        $product = Product::where('auction_product', 1)->findOrFail($id);
        $categories = Category::where('parent_id', 0)->where('digital', 0)->with('childrenCategories')->get();
        return view('auction.backend.products.edit', compact('product', 'categories'));
    }

    public function product_update_admin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_ids' => 'required|array',
            'starting_bid' => 'required|numeric|min:0',
            'auction_date_range' => 'required|string',
        ]);
        $this->auctionService->update($request, $id);
        flash(translate('Auction product has been updated successfully'))->success();
        return redirect()->route('auction.all_products');
    }

    public function product_destroy_admin($id)
    {
        $this->auctionService->destroy($id);
        flash(translate('Auction product has been deleted successfully'))->success();
        return redirect()->route('auction.all_products');
    }

    public function admin_auction_product_orders(Request $request)
    {
        $orders = Order::query()
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.auction_product', 1)
            ->select('orders.*')
            ->distinct()
            ->orderBy('orders.code', 'desc')
            ->paginate(15);
        return view('auction.backend.orders.index', compact('orders'));
    }

    // ---------- Seller ----------

    public function auction_product_list_seller(Request $request)
    {
        $products = Product::where('auction_product', 1)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('auction.seller.products.index', compact('products'));
    }

    public function product_create_seller()
    {
        $categories = Category::where('parent_id', 0)->where('digital', 0)->with('childrenCategories')->get();
        return view('auction.seller.products.create', compact('categories'));
    }

    public function product_store_seller(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_ids' => 'required|array',
            'starting_bid' => 'required|numeric|min:0',
            'auction_date_range' => 'required|string',
        ]);
        $this->auctionService->store($request);
        flash(translate('Auction product has been inserted successfully'))->success();
        return redirect()->route('auction_products.seller.index');
    }

    public function product_edit_seller($id)
    {
        $product = Product::where('auction_product', 1)->where('user_id', auth()->id())->findOrFail($id);
        $categories = Category::where('parent_id', 0)->where('digital', 0)->with('childrenCategories')->get();
        return view('auction.seller.products.edit', compact('product', 'categories'));
    }

    public function product_update_seller(Request $request, $id)
    {
        $product = Product::where('auction_product', 1)->where('user_id', auth()->id())->findOrFail($id);
        $request->validate([
            'name' => 'required|max:255',
            'category_ids' => 'required|array',
            'starting_bid' => 'required|numeric|min:0',
            'auction_date_range' => 'required|string',
        ]);
        $this->auctionService->update($request, $id);
        flash(translate('Auction product has been updated successfully'))->success();
        return redirect()->route('auction_products.seller.index');
    }

    public function product_destroy_seller($id)
    {
        $product = Product::where('auction_product', 1)->where('user_id', auth()->id())->findOrFail($id);
        $this->auctionService->destroy($id);
        flash(translate('Auction product has been deleted successfully'))->success();
        return redirect()->route('auction_products.seller.index');
    }

    public function seller_auction_product_orders(Request $request)
    {
        $orders = Order::query()
            ->where('seller_id', auth()->id())
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.auction_product', 1)
            ->select('orders.*')
            ->distinct()
            ->orderBy('orders.code', 'desc')
            ->paginate(15);
        return view('auction.seller.orders.index', compact('orders'));
    }

    // ---------- Frontend (auth) ----------

    public function purchase_history_user(Request $request)
    {
        $order_ids = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.user_id', auth()->id())
            ->where('products.auction_product', 1)
            ->select('orders.id')
            ->distinct()
            ->pluck('id');
        $orders = Order::whereIn('id', $order_ids)->orderBy('code', 'desc')->paginate(15);
        return view('auction.frontend.purchase_history', compact('orders'));
    }

    // ---------- Frontend (guest) ----------

    public function auction_product_details($slug)
    {
        $product = Product::where('slug', $slug)->where('auction_product', 1)->where('published', 1)->firstOrFail();
        if ($product->added_by == 'seller' && get_setting('vendor_system_activation') != 1) {
            abort(404);
        }
        return view('auction.frontend.product_details', compact('product'));
    }

    public function all_auction_products(Request $request)
    {
        $products = Product::where('auction_product', 1)
            ->where('published', 1)
            ->where('approved', 1);
        if (get_setting('seller_auction_product') == 0) {
            $products = $products->where('added_by', 'admin');
        }
        $products = $products->where('auction_start_date', '<=', time())
            ->where('auction_end_date', '>=', time())
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return view('auction.frontend.products_list', compact('products'));
    }
}
