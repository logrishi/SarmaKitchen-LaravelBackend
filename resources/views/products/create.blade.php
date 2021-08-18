{{-- @extends('layouts.app') --}}

<form action="/products" method="POST" enctype="multipart/form-data">
    {{-- validation  errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="form-group">
        <label>Enter Product Name</label>
        <input type="text" name="name" placeholder="Product Name" value="{{old('name')}}" class="form-control @error('name') is-invalid 
             @enderror">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Enter Product Description</label>
        <input type="text" name="description" placeholder="Product Description" value="{{old('description')}}" class="form-control @error('description') is-invalid 
                     @enderror">
        @error('description')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Is Veg</label>
        <input type="checkbox" name="is_veg" value="1" class="form-control @error('is_veg') is-invalid 
             @enderror">
        @error('is_veg')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Select Product Category </label>
        <select class="form-control @error('category_id') is-invalid 
                 @enderror" name="category_id">
            <option value="">Select Category</option>
            @foreach ($categories as $category)
            <option value="{{$category->id}}" @if (old('category_id')==$category->id) selected="selected"
                @endif>{{$category->category}}</option>
            @endforeach
        </select>
        @error('categories')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Select Meal Type</label>
        @foreach ($categories as $category)
        @foreach ($category->meal_type as $item)

        <input type="checkbox" name="meal_type[][name]" value="{{($item['name'])}}" class="form-control ">
        <label>{{$item['name']}}</label>
       
        @endforeach
        @endforeach
    </div>

    <div class="form-group">
        <label>Enter Product Image</label>
        <input type="file" name="image" class="form-control ">
    </div>

    <div class="form-group">
        <input type="checkbox" name="is_available" value="1" checked class="form-control ">
        <label>Is Available</label>
    </div>

    <label>Enter Product Details:</label>

    <div class="form-group">
        <label>Select Subscriptions</label>
        @foreach ($categories as $category)
        @foreach ($category->subscriptions as $item)
            
        <input type="checkbox" name="details[subscriptions][]" value="{{($item)}}" class="form-control ">
        <label>{{$item}}</label>

        @endforeach
        @endforeach
    </div>

    <div class="form-group">
        <label>Select Dish Type</label>

        @foreach ($categories as $category)
            @isset($category->dish_type)    
                @foreach ($category->dish_type as $item)
                    <input type="checkbox" name="details[dish_type][]" value="{{($item)}}" class=" form-control">
                    <label>{{$item}}</label>    
                @endforeach
            @endisset
        @endforeach

    </div>

    <div class="form-group">
        <label>Enter Size</label>
        <input type="text" name="details[size][]" class="form-control">
        <input type="text" name="details[size][]" class="form-control">
    </div>

    <div class="form-group">
        <label>Enter Notes</label>
        <input type="text" name="details[notes][]" class="form-control">
        <input type="text" name="details[notes][]" class="form-control">
    </div>

    <div class="form-group">
        <label>Enter Price</label>
        <input type="text" name="details[price][]" class="form-control">
        <input type="text" name="details[price][]" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Submit</button>
    @csrf
</form>