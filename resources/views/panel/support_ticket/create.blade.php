@extends('layouts.app')

@section('title', 'Create Support Tickets')

@section('content')
    <section>
        <div class="container custom_width">
            <div class="row">
                <form action="{{ route('admin.support_ticket.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-8 mx-auto settings_tabs_content">
                        <div class="custome_input">
                            <label for="" class="@error('subject') validation-error-label @enderror">Subject</label>
                            <input type="text" name="subject" class="@error('subject') validation-error @enderror">
                            @error('subject')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="" class="@error('content') validation-error-label @enderror">Details</label>
                            <textarea type="text" name="content" rows="5" class="@error('subject') validation-error @enderror" style="border: 1px solid var(--border_color);"></textarea>
                            @error('content')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="">Attachment</label>
                            <input type="file" name="attachment" class="@error('attachment') validation-error @enderror">
                            @error('attachment')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custome_input">
                            <label for="">Select Merchant</label>
                            <select name="user_id" id="" class="form-control form-control-lg" >
                                <option value="" selected>Select Merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="custome_input">
                            <label for="">Assigned To</label>
                            <select name="staff_id" id="" class="form-control form-control-lg" >
                                <option value="" selected>Select Staff</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="custome_input">
                            <button class="btn-default">Save</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="section_gaps"></div>
@endsection
