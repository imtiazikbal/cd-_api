@section('title', 'Couriers')

@extends('layouts.app');

@section('content')
    <section class="courierSection custom_width container">
        <div class="heading">
            <h1>Courier Details</h1>
        </div>

        <div class="courierSection_grid">
            @foreach ($couriers as $courier)
                <div class="box">
                    <div class="box_heading">
                        <h1>{{ $courier->name ?? '' }}</h1>
                    </div>
                    <div class="box_body">
                        <form method="post" data-courier-id="{{ $courier->id }}"
                            onsubmit="update_courier(event, {{ $courier->id }})">
                            <div class="courier_details">
                                <div>
                                    <label class="sr-only">Toast</label>
                                    <div class="courier_toast {{ $courier->notice ? 'error show' : '' }}">
                                        <div class="courier_icon"><i
                                                class="fa-solid {{ $courier->notice ? 'fa-xmark' : 'fa-check' }}"></i>
                                        </div>
                                        <span class="courier_text">{{ $courier->notice ?? '' }}</span>
                                    </div>
                                </div>
                                <div id="name_{{ $courier->id }}">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" placeholder="Name" value="{{ $courier->name }}"
                                        autocomplete="off" />
                                </div>

                                <div id="courier_{{ $courier->id }}">
                                    <label for="courier">Courier <small>(Read only)</small></label>
                                    <input type="text" name="courier" placeholder="Courier"
                                        value="{{ $courier->courier }}" readonly />
                                </div>

                                <div id="email_{{ $courier->id }}">
                                    <label for="email">{{ $courier->courier == 'redx' ? 'Number' : 'Email' }}</label>
                                    <input type="{{ $courier->courier == 'redx' ? 'number' : 'email' }}" name="email"
                                        placeholder="{{ $courier->courier == 'redx' ? 'Number' : 'Email' }}"
                                        value="{{ $courier->email }}" autocomplete="off" />
                                </div>

                                <div id="password_{{ $courier->id }}">
                                    <label for="password">Password</label>
                                    <input type="text" name="password" placeholder="Password"
                                        value="{{ $courier->password }}" autocomplete="off" />
                                </div>

                                <div>
                                    <button>Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        function show_error(courier_id, target_id, message = '') {
            const parentElm = document.getElementById(`${target_id}_${courier_id}`);

            let errElement = parentElm.querySelector('p.error');

            if (errElement == null) {
                errElement = document.createElement('p');
                errElement.className = 'error';
                parentElm.appendChild(errElement);
            }

            errElement.innerText = message;
        }

        function hide_errors(courierForm) {
            const elements = courierForm.querySelectorAll('p.error');

            Object.values(elements).forEach(element => {
                element.remove();
            });
        }

        function handleCourier(courierForm, resp) {
            const courier_toast = courierForm.querySelector('.courier_toast');
            const courier_text = courier_toast?.querySelector('.courier_text');
            const courier_icon = courier_toast?.querySelector('.courier_icon i');
            const courier_id = courierForm.dataset.courierId;

            hide_errors(courierForm);

            if (resp?.message) {
                courier_text.innerText = resp.message;

                if (!courier_toast.classList.contains('show')) {
                    courier_toast.classList.add('show');
                }

                if (!courier_toast.classList.contains('error')) {
                    courier_toast.classList.add('error');
                }

                if (/success/i.test(resp.message)) {
                    if (courier_icon.classList.contains('fa-xmark')) {
                        courier_icon.classList.remove('fa-xmark');
                    }

                    if (!courier_icon.classList.contains('fa-check')) {
                        courier_icon.classList.add('fa-check');
                    }
                } else {
                    if (courier_icon.classList.contains('fa-check')) {
                        courier_icon.classList.remove('fa-check');
                    }

                    if (!courier_icon.classList.contains('fa-xmark')) {
                        courier_icon.classList.add('fa-xmark');
                    }
                }
            }

            if (resp?.errors) {
                Object.keys(resp.errors).forEach(key => {
                    show_error(courier_id, key, resp.errors[key][0]);
                });
            }
        }

        function update_courier(ev, courier_id) {
            ev.preventDefault();

            const formData = new FormData(ev.target);
            const params = new URLSearchParams(formData);
            const queryString = params.toString();
            const btn = ev.target.querySelector('button');

            ev.target.style.cursor = 'wait';
            btn.disabled = true;

            let updateRoute = "{{ route('admin.couriers.update', ':courier_id') }}";

            updateRoute = updateRoute.replace(':courier_id', courier_id);

            let headersList = {
                "Accept": "application/json",
                "Content-Type": "application/x-www-form-urlencoded",
            }

            let reqOptions = {
                url: updateRoute,
                method: "PUT",
                headers: headersList,
                data: queryString,
            }

            axios.request(reqOptions)
                .then(function(response) {
                    handleCourier(ev.target, response.data);
                })
                .catch(function(error) {
                    console.error(error);
                }).finally(function() {
                    ev.target.style.cursor = 'auto';
                    btn.disabled = false;
                });
        }
    </script>
@endsection
