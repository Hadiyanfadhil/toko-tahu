<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\MultiImage;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\SubSubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;

class ProductController extends Controller
{
    //redirect to index page
    public function index(){
        $data = Product::get();
        return view('admin.product.index')->with(['data'=> $data]);
    }
    //redirect create page
    public function createProduct(){
        $brands = Brand::get();
        $categories = Category::get();
        return view('admin.product.create')->with([
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    //get subcategory with ajax
    public function getSubCategory(Request $request){
        $subCategories = SubCategory::where('category_id',$request->id)->get();
        return response()->json([
            'subCategories' => $subCategories,
        ]);
    }
    //get sub-subcategory with ajax
    public function getSubSubCategory(Request $request){
        $subsubCategoires = SubSubCategory::where('subcategory_id',$request->id)->get();
        return response()->json([
            'subsubCategories' => $subsubCategoires
        ]);
    }

    //store product data
    public function storeProduct(Request $request){
        //validation
        $validation = $this->productValidation($request);
        if($validation->fails()){
            return back()->withErrors($validation)->withInput();
        }

        //get preview image
        $file = $request->file('previewImage');
        $fileName = uniqid().'_'.$file->getClientOriginalName();
        //get data
        $data = $this->requestProductData($request);
        $data['preview_image'] = $fileName;
        //store data
        $file->move(public_path().'/uploads/products/',$fileName);
        $productId = Product::insertGetId($data);

        //check multi image
        if($request->hasFile('multiImage')){
            $multiImageFiles = $request->file('multiImage');
            foreach($multiImageFiles as $img){
                $multiImageName = uniqid().'_'.$img->getClientOriginalName();
                //store image
                $img->move(public_path().'/uploads/products/',$multiImageName);
                MultiImage::create([
                    'productid_' => $productId,
                    'image' => $multiImageName,
                ]);
            }

        }
        return redirect()->route('admin#product')->with(['success'=>'Product created successfully']);

    }

    //redirect to edit page
    public function editProduct($id){
        $product = Product::where('product_id',$id)->first();
        $brands = Brand::get();
        $categories = Category::get();
        $subCategories = SubCategory::where('category_id',$product->category_id)->get();
        $subsubCategories = SubSubCategory::where('subcategory_id',$product->subcategory_id)->get();
        $multiImages = MultiImage::where('productid_',$id)->get();
        return view('admin.product.edit')->with([
            'product'=>$product,
            'brands'=>$brands,
            'categories'=>$categories,
            'subCategories' => $subCategories,
            'subsubCategories' => $subsubCategories,
            'multiImages' => $multiImages
        ]);
    }

    //delete multiImage
    public function deleteImg(Request $request){
        $multiImage = MultiImage::where('multi_image_id',$request->id)->first();
        $fileName = $multiImage->image;

        if(File::exists(public_path().'/uploads/products/'.$fileName)){
            File::delete(public_path().'/uploads/products/'.$fileName);
        }
        MultiImage::where('multi_image_id',$request->id)->delete();
        return response()->json([
            'success'=>'deleted successfully',
        ]);
    }

    //update data
    public function updateProduct(Request $request,$id){
        //validation
        $validation =  Validator::make($request->all(),[
            'brandId' => 'required',
            'categoryId' => 'required',
            'subCategoryId' => 'required',
            'subsubCategoryId' => 'required',
            'name' => 'required',
            'smallDescription' => 'required',
            'longDescription' => 'required',
            'originalPrice' => 'required',
            'sellingPrice' => 'required',
            'discountPrice' => 'required',
            'publishStatus' => 'required',
            'specialOffer' => 'required',
            'featured' => 'required',
        ]);
        if($validation->fails()){
            return back()->withErrors($validation)->withInput();
        }
        //get data
        $updateData = $this->requestProductData($request);

        //check preview image
        if($request->hasFile('previewImage')){
            //delete old image
            $product = Product::where('product_id',$id)->first();
            $oldFileName = $product->preview_image;
            if(File::exists(public_path().'/uploads/products/'.$oldFileName)){
                File::delete(public_path().'/uploads/products/'.$oldFileName);
            }
            //update new image
            $newFile = $request->file('previewImage');
            $newFileName = uniqid().'_'.$newFile->getClientOriginalName();
            $newFile->move(public_path().'/uploads/products/',$newFileName);

            $updateData['preview_image'] = $newFileName;

        }

        Product::where('product_id',$id)->update($updateData);

        //check multi image
        if($request->hasFile('multiImage')){
            //store multi image
            $multiImageFiles = $request->file('multiImage');
            foreach($multiImageFiles as $img){
                $multiImageName = uniqid().'_'.$img->getClientOriginalName();
                $img->move(public_path().'/uploads/products/',$multiImageName);

                MultiImage::create([
                    'productid_' => $id,
                    'image' => $multiImageName
                ]);
            }

        }

        return redirect()->route('admin#product')->with(['success'=>'Product updated successfully']);
    }

    //get request data
    private function requestProductData($request){
        $data = [
            'brand_id' => $request->brandId,
            'category_id' => $request->categoryId,
            'subcategory_id' => $request->subCategoryId,
            'subsubcategory_id' => $request->subsubCategoryId,
            'name' => $request->name,
            'short_description' => $request->smallDescription,
            'long_description' => $request->longDescription,
            'original_price' => $request->originalPrice,
            'selling_price' => $request->sellingPrice,
            'discount_price' => $request->discountPrice,
            'publish_status' => $request->publishStatus,
            'special_offer' => $request->specialOffer,
            'featured' => $request->featured,
        ];
        if(isset($request->previewImage)){
            $data['preview_image'] = $request->previewImage;
        }
        return $data;
    }

    //validation
    private function productValidation($request){
        return Validator::make($request->all(),[
            'brandId' => 'required',
            'categoryId' => 'required',
            'subCategoryId' => 'required',
            'subsubCategoryId' => 'required',
            'name' => 'required',
            'smallDescription' => 'required',
            'longDescription' => 'required',
            'previewImage' => 'required',
            'originalPrice' => 'required',
            'sellingPrice' => 'required',
            'discountPrice' => 'required',
            'publishStatus' => 'required',
            'specialOffer' => 'required',
            'featured' => 'required',
        ]);
    }




}