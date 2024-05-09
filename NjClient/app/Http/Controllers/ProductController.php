<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
    {
        // method will show product page
        public function index(){
            $products = Product::orderBy('created_at','DESC')->get();
        return view('products.list', [
              'products'=> $products
        ]);
            
        }
        // method will show product create
        public function create(){
            return view('products.create');
            
        }
        // method will show product store in db
        public function store(Request $request){
            $rules =[

                'name'=> 'required|min:5',
                'sku'=> 'required|min:3',
                'price'=> 'required|numeric'
            ];

            if($request->image !=""){
                $rules['image']='image';
            }

            $validator=Validator::make($request->all(),$rules);
            if($validator->fails()){
                return redirect()->route('products.create')->withInput()->withErrors($validator);
            }

            $product= new Product();
            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->price= $request->price;
            $product->description = $request->description;
            $product->save();

            if($request->image != ""){
            // here we will store image
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time().'.'.$ext; // Unique image name

            $image->move(public_path('upload/products'),$imageName);
            $product->image = $imageName;
            $product->save();

            }
            return redirect()->route('products.index')->with('success','Product added successfully.');
            
        }
        
        // method will show product edit page in db
        public function edit($id){
            $product = Product::findOrFail($id);
            return view('products.edit',[
                'product'=>$product
            ]);
            
        }
        // method will show product update in db
        public function update($id,Request $request){
            $product = Product::findOrFail($id);
            $rules =[

                'name'=> 'required|min:5',
                'sku'=> 'required|min:3',
                'price'=> 'required|numeric'
            ];

            if($request->image !=""){
                $rules['image']='image';
            }

            $validator=Validator::make($request->all(),$rules);
            if($validator->fails()){
                return redirect()->route('products.edit', $product->id)->withInput()->withErrors($validator);
            }

           
            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->price= $request->price;
            $product->description = $request->description;
            $product->save();

            if($request->image != ""){
            // here we will store image
            File::delete(public_path('upload/products/'.$product->image));

            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time().'.'.$ext; // Unique image name

            $image->move(public_path('upload/products'),$imageName);
            $product->image = $imageName;
            $product->save();

            }
            return redirect()->route('products.index')->with('success','Product update successfully.');
            
            
        }
        // method will show product delte in db
        public function destroy($id){
            $product = Product::findOrFail($id);

            File::delete(public_path('upload/products/'.$product->image));
            $product->delete();
            return redirect()->route('products.index')->with('success','Product Deleted successfully.');
        }
    }
