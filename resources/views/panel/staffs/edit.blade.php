@extends('layouts.app')

@section('content')
    <section>
        <div class="container custom_width">
            <div class="row">


                <form action="{{ route('admin.staffs.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="col-lg-8 mx-auto settings_tabs_content">
                        <div class="custome_input">
                            <label for="name" class="@error('name') validation-error-label @enderror">Name</label>
                            <input type="text" name="name" class="@error('name') validation-error @enderror" value="{{ $user->name }}" />
                            @error('name')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="email" class="@error('email') validation-error-label @enderror">Email</label>
                            <input type="text" name="email" class="@error('email') validation-error @enderror" value="{{ $user->email }}">
                            @error('email')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="phone" class="@error('phone') validation-error-label @enderror">Phone</label>
                            <input type="text" name="phone" class="@error('phone') validation-error @enderror" value="{{ $user->phone }}">
                            @error('phone')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="password" class="@error('password') validation-error-label @enderror">Password</label>
                            <input type="password" name="password" class="@error('password') validation-error @enderror">
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
