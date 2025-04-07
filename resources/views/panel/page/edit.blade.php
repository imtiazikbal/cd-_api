@extends('layouts.app')

@section('content')
<section>
    <div class="page-edit-wraper">
        <div class="container custom_width">
            <span class="doamin-status-header">Page Edit</span>
            <hr>
            <div class="page-edit-form p-3 bg-white">
                <form action="{{route('admin.page.update', $pageId)}}" method="post">
                    @csrf 

                    <div class="form-group">
                      <label for="exampleInputEmail1"></label>
                      <textarea name="pageUpdatedCode" id="" cols="30" rows="25" class="w-100" value="{{$pageEditCode}}">{{$pageEditCode}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection