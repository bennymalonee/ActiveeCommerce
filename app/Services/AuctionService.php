<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuctionService
{
    /**
     * Create an auction product.
     *
     * @param  Request  $request
     * @return Product
     */
    public function store(Request $request)
    {
        $data = $this->prepareData($request->all(), null);
        $product = Product::create($data);
        if ($request->filled('category_ids')) {
            $product->categories()->attach($request->category_ids);
        }
        if ($request->filled('name') || $request->filled('description')) {
            $product->product_translations()->create([
                'product_id'  => $product->id,
                'lang'        => $request->get('lang', env('DEFAULT_LANGUAGE')),
                'name'        => $request->get('name', $request->get('product_name', '')),
                'unit'        => $request->get('unit', $request->get('product_unit', '')),
                'description' => $request->get('description', ''),
            ]);
        }
        return $product;
    }

    /**
     * Update an auction product.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->auction_product != 1) {
            abort(404, translate('Product is not an auction product'));
        }
        $data = $this->prepareData($request->all(), $product);
        $product->update($data);
        if ($request->filled('category_ids')) {
            $product->categories()->sync($request->category_ids);
        }
        $lang = $request->get('lang', env('DEFAULT_LANGUAGE'));
        $translation = $product->product_translations()->where('lang', $lang)->first();
        $name = $request->get('name', $request->get('product_name', $product->name));
        $unit = $request->get('unit', $request->get('product_unit', ''));
        $description = $request->get('description', '');
        if ($translation) {
            $translation->update(compact('name', 'unit', 'description'));
        } else {
            $product->product_translations()->create([
                'product_id'  => $product->id,
                'lang'        => $lang,
                'name'        => $name,
                'unit'        => $unit,
                'description' => $description,
            ]);
        }
        return $product;
    }

    /**
     * Delete an auction product and related data.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->auction_product != 1) {
            abort(404, translate('Product is not an auction product'));
        }
        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->bids()->delete();
        $product->taxes()->delete();
        $product->wishlists()->delete();
        $product->carts()->delete();
        $product->flash_deal_products()->delete();
        Product::destroy($id);
    }

    /**
     * Prepare product data for create/update.
     *
     * @param  array  $input
     * @param  Product|null  $product
     * @return array
     */
    protected function prepareData(array $input, $product)
    {
        $collection = collect($input);

        $approved = 1;
        if (auth()->user() && auth()->user()->user_type == 'seller') {
            $user_id = auth()->user()->id;
            $added_by = 'seller';
            if (get_setting('product_approve_by_admin') == 1) {
                $approved = 0;
            }
        } else {
            $admin = User::where('user_type', 'admin')->first();
            $user_id = $admin ? $admin->id : 0;
            $added_by = 'admin';
        }

        $name = $collection->get('name', $collection->get('product_name', 'Auction Product'));
        $slug = Str::slug($name);
        if ($product) {
            $slug = $collection->get('slug', $product->slug);
        } else {
            $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
            $slug .= $same_slug_count ? '-' . ($same_slug_count + 1) : '';
        }

        $auction_start_date = null;
        $auction_end_date = null;
        if ($collection->has('auction_date_range') && $collection->get('auction_date_range')) {
            $date_var = explode(' to ', $collection['auction_date_range']);
            if (count($date_var) >= 2) {
                $auction_start_date = strtotime(trim($date_var[0]));
                $auction_end_date = strtotime(trim($date_var[1]));
            }
        } elseif ($collection->has('auction_start_date') && $collection->has('auction_end_date')) {
            $auction_start_date = strtotime($collection['auction_start_date']);
            $auction_end_date = strtotime($collection['auction_end_date']);
        }

        $tags = $collection->get('tags', '');
        if (is_array($tags)) {
            $tags = implode(',', $tags);
        } elseif ($collection->has('tags') && is_string($collection->get('tags')) && preg_match('/^\[/', $collection->get('tags'))) {
            $decoded = json_decode($collection->get('tags'));
            if (is_array($decoded)) {
                $arr = [];
                foreach ($decoded as $t) {
                    $arr[] = is_object($t) ? ($t->value ?? '') : $t;
                }
                $tags = implode(',', $arr);
            }
        }

        $shipping_cost = 0;
        if ($collection->get('shipping_type') == 'flat_rate' && $collection->has('flat_shipping_cost')) {
            $shipping_cost = (float) $collection->get('flat_shipping_cost');
        } elseif ($collection->get('shipping_type') == 'flat_rate' && $collection->has('shipping_cost')) {
            $shipping_cost = (float) $collection->get('shipping_cost');
        }

        $photos = $collection->get('photos', '');
        if (is_array($photos)) {
            $photos = implode(',', $photos);
        }
        $thumbnail_img = $collection->get('thumbnail_img', '');
        if (is_array($thumbnail_img)) {
            $thumbnail_img = $thumbnail_img[0] ?? '';
        }

        $data = [
            'name'                => $name,
            'added_by'            => $added_by,
            'user_id'             => $user_id,
            'category_id'         => is_array($collection->get('category_ids')) ? ($collection->get('category_ids')[0] ?? 0) : ($collection->get('category_id', 0)),
            'brand_id'             => $collection->get('brand_id'),
            'unit'                => $collection->get('unit', $collection->get('product_unit', '')),
            'photos'              => $photos,
            'thumbnail_img'       => $thumbnail_img,
            'tags'                => $tags,
            'description'         => $collection->get('description', ''),
            'unit_price'          => (float) $collection->get('starting_bid', 0),
            'purchase_price'      => (float) $collection->get('starting_bid', 0),
            'variant_product'     => 0,
            'attributes'          => '[]',
            'choice_options'      => '[]',
            'colors'              => '[]',
            'published'           => $collection->get('button') === 'unpublish' ? 0 : 1,
            'draft'               => 0,
            'approved'            => $approved,
            'stock_visibility_state' => 'quantity',
            'current_stock'       => 1,
            'digital'             => 0,
            'auction_product'     => 1,
            'starting_bid'        => (float) $collection->get('starting_bid', 0),
            'auction_start_date'  => $auction_start_date,
            'auction_end_date'    => $auction_end_date,
            'slug'                => $slug,
            'discount'            => 0,
            'discount_type'       => 'amount',
            'weight'              => (float) $collection->get('weight', 0),
            'min_qty'             => 1,
            'shipping_type'       => $collection->get('shipping_type', 'flat_rate'),
            'shipping_cost'       => $shipping_cost,
            'cash_on_delivery'    => $collection->get('cash_on_delivery', 0) ? 1 : 0,
            'est_shipping_days'   => $collection->get('est_shipping_days'),
            'meta_title'          => $collection->get('meta_title', $name),
            'meta_description'    => $collection->get('meta_description', strip_tags($collection->get('description', ''))),
            'meta_img'            => $collection->get('meta_img', $thumbnail_img),
            'video_provider'      => $collection->get('video_provider'),
            'video_link'          => $collection->get('video_link'),
            'wholesale_product'   => 0,
        ];

        if (!isset($input['gst_rate']) && addon_is_activated('gst_system')) {
            $data['tax'] = 0;
            $data['tax_type'] = 'amount';
        }

        return $data;
    }
}
