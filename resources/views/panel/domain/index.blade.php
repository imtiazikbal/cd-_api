
@extends('layouts.app');

@section('content')
<section id="ClientList" class="openTicket">

    <div class="container custom_width">

        <!-- Header -->
        <div class="row">
            
            <div class="domain-status-buttons">
                <a href="{{route('admin.domain.request', 'pending')}}">Pending</a>
                <a href="{{route('admin.domain.request', 'connected')}}">Connected</a>
                <a href="{{route('admin.domain.request', 'rejected')}}">Rejected</a>
            </div>

            <div class="col-lg-12">
               <div class="title-search-section">
                <span class="doamin-status-header">{{ucfirst($type)}} Domain Request </span>
                <form action="{{route('admin.domain.search')}}" method="POST">
                    @csrf
                    <input class="form-control" type="text" name="search" placeholder="Search...">
                    <input type="hidden" name="type" value="{{$type}}">
                </form>
               </div>
               <hr>
                <div class="table_part">

                    <table class="table table-striped">
                        
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Shop Name</th>
                                <th>Shop ID</th>
                                <th>Client Name</th>
                                <th>Client Phone</th>
                                <th>Domain Request</th>
                                <th style="width: 10%; text-align:center;">Update Domain Request</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Status Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = ($domains->perPage() * ($domains->currentPage() - 1)) + 1;
                            @endphp
                            @forelse ($domains as $key => $item)
                            <tr>
                                <td>{{$index++}}</td>
                                <td class="name">{{$item->name}}</td>
                                <td>{{$item->shop_id}}</td>
                                <td>{{$item->merchant ? ($item->merchant->name ?? 'None') : 'None'}}</td>
                                <td>{{$item->merchant ? ($item->merchant->phone ?? 'None') : 'None'}}</td>
                                <td>{{$item->domain_request ?? "None"}}</td>
                                <td>
                                    <form action="{{route('admin.domain.request.update', $item->id)}}" method="POST">
                                        @csrf
                                        <input class="form-control" type="text" name="domain_request" placeholder="Domain...">
                                    </form>
                                </td>
                                <td> 
                                    <span class="badge bg-@if($item->domain_status == 'connected')success @elseif($item->domain_status == 'pending')primary @else()danger @endif">{{$item->domain_status ?? "None"}}</span>
                                </td>
                                <td>{{ $item->domain_request_date ? \Carbon\Carbon::parse($item->domain_request_date)->format('d M Y') : 'None' }}</td>
                                <td class="domain-status-update-btns">
                                    <a href="{{ route('admin.domain.refresh', $item->id) }}" class="status-confirm">Refresh</a>
                                </td>

                                <td class="domain-status-update-btns">
                                    @if($item->domain_status == 'rejected')
                                    <a href="{{route('admin.domain.request.status.update',$item->id.'/'.'connected')}}" class="status-confirm">{{$item->domain_status == 'pending' ? "Connected" : "Pending"}}</a>
                                    @endif
                                    
                                    @if($item->domain_status != 'rejected')
                                    <a href="{{route('admin.domain.request.status.update',$item->id.'/'.$type)}}" class="status-confirm">{{$item->domain_status == 'pending' ? "Connected" : "Pending"}}</a>

                                    <a href="javascript:void()" class="domain-reject-btn" data-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#domainRejectedModal">Rejected</button>
                                 
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="10"><p class="no-data-found">No Data Found</p></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $domains->links() }}
                </div>

            </div>

        </div>

    </div>

</section>

 {{-- Domain Request Model --}}
 <div class="modal fade " id="domainRejectedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{route('admin.domain.request.status.reject')}}" method="post" class="domainRejectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="" class="form-labe"><b>Rejected Reason</b></label>
                        <textarea name="rejected_reason" id="" cols="30" rows="10" class="w-100 p-2" placeholder="Write your reason..."></textarea>
                        <input type="hidden" name="id">
                        <input type="hidden" name="type" value="rejected">
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color: #a16cf8; border:none;">Submit</button>
                    &nbsp;
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

  
@endsection

