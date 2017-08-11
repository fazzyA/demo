@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
    <form method="post" enctype="multipart/form-data" action="{{url("/upload")}}">
        <h2>Select image to upload:</h2>
        {{ csrf_field() }}
    <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
    <input type="submit" class="btn btn-primary " value="Upload Image" name="submit">
</form>
            </div> </div></div>
@endsection