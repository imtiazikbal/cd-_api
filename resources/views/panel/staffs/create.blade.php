@extends('layouts.app')

@section('content')
    <section>
        <div class="container custom_width">
            <div class="row">

                <form action="{{ route('admin.staffs.store') }}" method="POST">
                    @csrf

                    <div class="col-lg-8 mx-auto settings_tabs_content">
                        <div class="custome_input">
                            <label for="" class="@error('name') validation-error-label @enderror">Name</label>
                            <input type="text" name="name" class="@error('name') validation-error @enderror">
                            @error('name')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="" class="@error('email') validation-error-label @enderror">Email</label>
                            <input type="text" name="email" class="@error('email') validation-error @enderror">
                            @error('email')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="" class="@error('phone') validation-error-label @enderror">Phone</label>
                            <input type="text" name="phone" class="@error('phone') validation-error @enderror">
                            @error('phone')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="" class="@error('password') validation-error-label @enderror">Password</label>
                            <input type="text" name="password" class="@error('password') validation-error @enderror">
                            @error('password')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="" class="@error('role') validation-error-label @enderror">Select Role</label>
                            <select name="role" id="" class="form-control form-control-lg">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <button type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
