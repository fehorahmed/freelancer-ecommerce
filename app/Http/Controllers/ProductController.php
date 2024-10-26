<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->per_page) {
            $perPage = $request->per_page;
        } else {
            $perPage = 10;
        }
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        return ProductResource::collection($query->paginate($perPage));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            // 'weight' => 'required|numeric',
            // 'length' => 'required|numeric',
            // 'width' => 'required|numeric',
            // 'height' => 'required|numeric',
            'reguler_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'sku' => 'required|unique:products,sku',
            'brand' => 'nullable|numeric',
            'category' => 'required|numeric',
            'unit' => 'required|numeric',
            // 'product' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp,gif|max:2000',
            'status' => 'required|boolean',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        // dd($request->all());
        $transactionFail = false;
        try {
            DB::beginTransaction();

            $product = new Product();

            $findUrl = $product->where('url', '=', Str::slug($request->name, '-'))->count();
            if ($findUrl == 0) {
                $product->url = Str::slug($request->name, '-');
            } else {
                $product->url = Str::slug($request->name . ' ' . rand(00, 99), '-');
            }

            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->brand_id = $request->brand;
            $product->unit_id = $request->unit;
            $product->category_id = $request->category;
            $product->warranty_id = $request->warranty;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->status = $request->status;
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $path = '\images\products\profile';
                $dpath = '\images\products\profile\150';

                $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

                $resize_image = Image::make($file->getRealPath());
                $resize_image->resize(250, 250, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                $dbimg = $image_name;
            } else {
                $dbimg = '';
            }

            $product->image = $dbimg;
            $product->created_by = auth()->id();
            if ($product->save()) {
                $productId = $product->id;
                $i = 0;



                if ($request->hasFile("gallery")) {
                    $gfiles = $request->file('gallery');
                    foreach ($gfiles as $file) {
                        $path = '\images\products\gallery_image';
                        $dpath = '\images\products\gallery_image\150';

                        $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

                        $resize_image = Image::make($file->getRealPath());
                        $resize_image->resize(250, 250, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                        $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                        $dbgimg = $image_name;

                        $proGal = new ProductImage();
                        $proGal->product_id = $productId;
                        $proGal->image = $dbgimg;
                        $proGal->created_by = auth()->id();
                        if (!$proGal->save()) {
                            $transactionFail = true;
                        }
                    }
                }
                $proPrice = new ProductPrice();

                $proPrice->product_id = $productId;
                // $proPrice->product_attribute_id = $productAttId;
                $proPrice->reguler_price = $request->reguler_price;
                $proPrice->sell_price = $request->sell_price;
                $proPrice->created_by = auth()->id();

                if (!$proPrice->save()) {
                    $transactionFail = true;
                }

                // if ($request->hasFile('brochure')) {

                //     $file = $request->file('brochure');
                //     $path = '\images\products\brochure';
                //     $file_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
                //     $path1 = Storage::disk('public')->put($path . '\\' . $file_name, File::get($file));

                //     $proBro = new ProductsBrochure();
                //     $proBro->product_id = $productId;
                //     $proBro->description = 'N/A';
                //     $proBro->brochure = $file_name;
                //     if (!$proBro->save()) {
                //         $isTransactionFails = true;
                //     }
                // }

                // if (isset($request->categories)) {
                //     foreach ($request->categories as $category) {
                //         $proCat = new ProductsCategories();
                //         $proCat->product_id = $productId;
                //         $proCat->category_id = $category;
                //         $proCat->created_by = $userId;
                //         if (!$proCat->save()) {
                //             $isTransactionFails = true;
                //         }
                //     }
                // }

                // if (isset($request->video)) {
                //     $proVid = new ProductVideos();
                //     $proVid->product_id = $productId;
                //     $proVid->video_url = $request->video;
                //     if (!$proVid->save()) {
                //         $isTransactionFails = true;
                //     }
                // }

                // if ($request->cdeddate != '' || $request->cdeddate != null) {
                //     $proCDown = new ProductsCountdown();
                //     $proCDown->product_id = $productId;
                //     $proCDown->start_date = $request->cdstdate;
                //     $proCDown->start_time = $request->cdsttime;
                //     $proCDown->end_date = $request->cdeddate;
                //     $proCDown->end_time = $request->cdedtime;
                //     $proCDown->created_by = $userId;

                //     if (!$proCDown->save()) {
                //         $isTransactionFails = true;
                //     }
                // }

                // $proMes = new ProductsMeasurements();
                // $proMes->product_id = $productId;
                // $proMes->weight = $request->weight;
                // $proMes->length = $request->length;
                // $proMes->width = $request->width;
                // $proMes->height = $request->height;
                // if (!$proMes->save()) {
                //     $isTransactionFails = true;
                // }

                // if (isset($request->tags)) {
                //     foreach ($request->tags as $tag) {
                //         $proTag = new ProductsTags();
                //         $proTag->product_id = $productId;
                //         $proTag->tag_id = $tag;
                //         $proTag->created_by = $userId;
                //         if ($proTag->save()) {
                //         } else {
                //             $isTransactionFails = true;
                //         }
                //     }
                // }

                // $si = 0;

                // if ($request->spec) {
                //     foreach ($request->spec['temp_spec_id'] as $product_spec_id) {
                //         if ($product_spec_id != 0) {
                //             $proSpec = new ProductsSpecifications();
                //             $proSpec->product_id = $productId;
                //             $proSpec->type = $product_spec_id;
                //             $proSpec->description = $request->spec['temp_spec_desc'][$si];
                //             if ($proSpec->save()) {
                //             } else {
                //                 $isTransactionFails = true;
                //             }
                //         }
                //         $si++;
                //     }
                // }


                // foreach ($request->product['temp_stock'] as $product_stock) {
                //     if (isset($request->product['temp_color_id'][$i]) || isset($request->product['temp_size_id'][$i]) || isset($request->product['temp_storage_id'][$i])) {
                //         $proAtt = new ProductsAttributes();
                //         $proAtt->product_id = $productId;
                //         $storage_id = isset($request->product['temp_storage_id'][$i]) ? $request->product['temp_storage_id'][$i] : null;
                //         $size_id = isset($request->product['temp_size_id'][$i]) ? $request->product['temp_size_id'][$i] : null;
                //         $color_id = isset($request->product['temp_color_id'][$i]) ? $request->product['temp_color_id'][$i] : null;
                //         $proAtt->storage_id = $storage_id == 0 ? null : $storage_id;
                //         $proAtt->size_id = $size_id == 0 ? null : $size_id;
                //         $proAtt->color_id = $color_id == 0 ? null : $color_id;

                //         if ($request->hasFile("temp_vimage")) {
                //             $vfiles = $request->file('temp_vimage');
                //             $file = $vfiles[$i];
                //             $path = '\images\products\profile';
                //             $dpath = '\images\products\profile\150';

                //             $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

                //             $resize_image = Image::make($file->getRealPath());
                //             $resize_image->resize(250, 250, function ($constraint) {
                //                 $constraint->aspectRatio();
                //             });

                //             $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                //             $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                //             $dbvimg = $image_name;
                //         } else {
                //             $dbvimg = '';
                //         }

                //         $proAtt->image = $dbvimg;
                //         $proAtt->created_by = $userId;

                //         if ($proAtt->save()) {
                //             $productAttId = $proAtt->id;
                //         } else {
                //             $isTransactionFails = true;
                //         }
                //     } else {
                //         $productAttId = NULL;
                //     }

                //     $proPrice = new ProductsPrices();

                //     $proPrice->product_id = $productId;
                //     $proPrice->product_attribute_id = $productAttId;
                //     $proPrice->reguler_dp = $request->product['temp_regular_dp'][$i];
                //     $proPrice->sell_dp = $request->product['temp_sale_dp'][$i];
                //     $proPrice->reguler_rp = $request->product['temp_regular_rp'][$i];
                //     $proPrice->sell_rp = $request->product['temp_sale_rp'][$i];
                //     $proPrice->reguler_price = $request->product['temp_regular_mrp'][$i];
                //     $proPrice->sell_price = $request->product['temp_sale_mrp'][$i];
                //     $proPrice->start_date = $request->product['temp_start_date'][$i];
                //     $proPrice->end_date = $request->product['temp_end_date'][$i];
                //     $proPrice->start_time = $request->product['temp_start_time'][$i];
                //     $proPrice->end_time = $request->product['temp_end_time'][$i];
                //     $proPrice->created_by = $userId;

                //     if ($proPrice->save()) {
                //     } else {
                //         $isTransactionFails = true;
                //     }

                //     $proInv = new ProductsInventories();
                //     $proInv->product_id = $productId;
                //     $proInv->product_attribute_id = $productAttId;
                //     $proInv->stock_in = $product_stock;
                //     $proInv->stock_out = 0;
                //     $proInv->ref_type = ProductsInventories::NEW_PRODUCT;
                //     $proInv->ref_id = 'Add New Product';
                //     $proInv->date = date("Y-m-d");
                //     $proInv->created_by = $userId;

                //     if ($proInv->save()) {
                //     } else {
                //         $isTransactionFails = true;
                //     }
                //     $i++;
                // }

            } else {
                $transactionFail = true;
            }


            if ($transactionFail) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong.'
                ]);
            } else {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Product stored successfully.'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Product::find($id);
        if ($data) {
            return response()->json([
                'status' => true,
                'data' => new ProductResource($data)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'reguler_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $id,
            'brand' => 'nullable|numeric',
            'category' => 'required|numeric',
            'unit' => 'required|numeric',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,gif|max:2000',
            'status' => 'required|boolean',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        //  dd($request->all());
        $transactionFail = false;
        try {
            DB::beginTransaction();

            $product =  Product::find($id);
            if (!strcmp($product->name, $request->name)) {
                $findUrl = $product->where('url', '=', Str::slug($request->name, '-'))->where('id', '!=', $id)->count();
                if ($findUrl == 0) {
                    $product->url = Str::slug($request->name, '-');
                } else {
                    $product->url = Str::slug($request->name . ' ' . rand(00, 99), '-');
                }
            }

            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->brand_id = $request->brand;
            $product->unit_id = $request->unit;
            $product->category_id = $request->category;
            $product->warranty_id = $request->warranty;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->status = $request->status;
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $path = '\images\products\profile';
                $dpath = '\images\products\profile\150';

                Storage::disk('public')->delete($path . '\\' . $product->image);
                Storage::disk('public')->delete($dpath . '\\' . $product->image);

                $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

                $resize_image = Image::make($file->getRealPath());
                $resize_image->resize(250, 250, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                $dbimg = $image_name;
                $product->image = $dbimg;
            }

            $product->updated_by = auth()->id();
            if ($product->save()) {
                $productId = $product->id;
                $i = 0;

                if ($request->hasFile("gallery")) {

                    // $deactivated
                    ProductImage::where('status', '=', 1)->where('product_id', '=', $id)->update(['status' => 0, 'updated_by' => auth()->id()]);
                    $gfiles = $request->file('gallery');
                    foreach ($gfiles as $file) {
                        $path = '\images\products\gallery_image';
                        $dpath = '\images\products\gallery_image\150';

                        $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

                        $resize_image = Image::make($file->getRealPath());
                        $resize_image->resize(250, 250, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                        $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                        $dbgimg = $image_name;

                        $proGal = new ProductImage();
                        $proGal->product_id = $productId;
                        $proGal->image = $dbgimg;
                        $proGal->created_by = auth()->id();
                        if (!$proGal->save()) {
                            $TransactionFail = true;
                        }
                    }
                }
                // $PriDeactivated
                ProductPrice::where('status', '=', 1)->where('product_id', '=', $id)->update(['status' => 0, 'updated_by'=>auth()->id()]);
                $proPrice = new ProductPrice();
                $proPrice->product_id = $productId;
                // $proPrice->product_attribute_id = $productAttId;
                $proPrice->reguler_price = $request->reguler_price;
                $proPrice->sell_price = $request->sell_price;
                $proPrice->created_by = auth()->id();
                if (!$proPrice->save()) {
                    $transactionFail = true;
                }
            } else {
                $transactionFail = true;
            }


            if ($transactionFail) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong.'
                ]);
            } else {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Product updated successfully.'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
