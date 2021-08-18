{{-- @extends('layouts.app') --}}

<form action="/categories" method="POST" enctype="multipart/form-data">

    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    {{-- validation  errors --}}
    {{-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
    @endforeach
    </ul>
    </div>
    @endif --}}

    <div class="form-group">
        <label>Enter Product Category</label>
        <input type="text" name="category" placeholder="Product Category" value="{{old('category')}}" class="form-control @error('name') is-invalid 
             @enderror">
        @error('category')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <label>Enter Meal Type Details</label>
    <div class="form-group">
        <label>Enter Meal Name</label>
        <input type="text" name="meal_type[name]" placeholder="Meal Type" value="{{old('meal_type.name')}}" class="form-control @error('meal_type.name') is-invalid 
                     @enderror">
        @error('meal_type.name')
        <div class="text-danger">{{ $message }}</div>
        @enderror

        <label>Enter Meal Image</label>
        <input type="file" name="meal_type[image]" value="{{old('meal_type.image')}}" class="form-control @error('meal_type.image') is-invalid 
                             @enderror">
        @error('meal_type.image')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Enter subscriptions</label>
    
        <input type="checkbox" name="subscriptions[]" value=1 class="form-control">
        <label>1 day</label>
    
        <input type="checkbox" name="subscriptions[]" value=7 class="form-control ">
        <label>7 Days</label>
    
        <input type="checkbox" name="subscriptions[]" value=15 class="form-control ">
        <label>15 Days</label>
    
        <input type="checkbox" name="subscriptions[]" value=30 class="form-control ">
        <label>30 Days</label>
    </div>

<div class="form-group">
    <label>Enter Dish Type</label>

    <br>
    <input type="checkbox" name="dish_type[]" value="Thali" class=" form-control">
    <label>Thali</label>

    <input type="checkbox" name="dish_type[]" value="rice" class="form-control ">
    <label>Rice</label>

    <input type="checkbox" name="dish_type[]" value="Chicken" class="form-control ">
    <label>Chicken</label>

    <input type="checkbox" name="dish_type[]" value="Mutton" class="form-control ">
    <label>Mutton</label>

    <input type="checkbox" name="dish_type[]" value="Fish" class="form-control ">
    <label>Fish</label>

    <input type="checkbox" name="dish_type[]" value="Egg" class="form-control ">
    <label>Egg</label>

    <input type="checkbox" name="dish_type[]" value="Paneer" class="form-control ">
    <label>Paneer</label>

    <input type="checkbox" name="dish_type[]" value="Noodles" class="form-control ">
    <label>Noodles</label>

    <input type="checkbox" name="dish_type[]" value="Dessert" class="form-control ">
    <label>Dessert</label>
</div>

    </div>

    <button type="submit" class="btn btn-success">Submit</button>
    @csrf
</form>