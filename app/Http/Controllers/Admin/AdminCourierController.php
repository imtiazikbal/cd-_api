<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\StoreAdminCourierRequest;
use App\Http\Requests\UpdateAdminCourierRequest;
use App\Models\AdminCourier;
use App\Services\PathaoFraudChecker;
use App\Services\RedxFraudChecker;
use App\Services\SteadfastCourierFraudChecker;
use App\Services\SteadfastFraudChecker;
use App\Traits\sendApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

// class AdminCourierController extends AdminBaseController
// {
//     use sendApiResponse;

//     /**
//      * Display a listing of the resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function index()
//     {
//         if (!$adminCourier = Cache::get('admin_couriers')) {
//             $adminCourier = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCourier);
//         }
//         return view('panel.couriers.index', ['couriers' => $adminCourier]);
//     }

//     /**
//      * Show the form for creating a new resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      *
//      * @param  \App\Http\Requests\StoreAdminCourierRequest  $request
//      * @return \Illuminate\Http\Response
//      */
//     public function store(StoreAdminCourierRequest $request)
//     {
//         //
//     }

//     /**
//      * Display the specified resource.
//      *
//      * @param  \App\Models\AdminCourier  $adminCourier
//      * @return \Illuminate\Http\Response
//      */
//     public function show(AdminCourier $adminCourier)
//     {
//         //
//     }

//     /**
//      * Show the form for editing the specified resource.
//      *
//      * @param  \App\Models\AdminCourier  $adminCourier
//      * @return \Illuminate\Http\Response
//      */
//     public function edit(AdminCourier $adminCourier)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      *
//      * @param  \App\Http\Requests\UpdateAdminCourierRequest  $request
//      * @param  \App\Models\AdminCourier  $adminCourier
//      * @return \Illuminate\Http\Response
//      */
//     public function update(UpdateAdminCourierRequest $request, AdminCourier $adminCourier)
//     {
//         $courier = $adminCourier->courier;
//         $result = null;

//         if ($courier == 'pathao') {
//             $fraud_checker = new PathaoFraudChecker();
//             $result = $fraud_checker->make_config($request->email, $request->password);
//         } elseif ($courier == 'redx') {
//             $fraud_checker = new RedxFraudChecker();
//             $result = $fraud_checker->make_config($request->email, $request->password);
//         } elseif ($courier == 'steadfast') {
//             $fraud_checker = new SteadfastFraudChecker();
//             $result = $fraud_checker->make_config($request->email, $request->password);
//         } elseif ($courier == 'steadfastcourier') {
//             $fraud_checker = new SteadfastCourierFraudChecker();
//             $result = $fraud_checker->make_config($request->email, $request->password);
//         }

//         if ($result->status == 'error') {
//             $adminCourier->update(['notice' => $result->message]);

//             $adminCouriers = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCouriers);

//             return $this->sendApiResponse('', $result->message, 'auth_error');
//         }

//         $adminCourier->update([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Crypt::encryptString($request->password),
//             'config' => \json_encode($result->config),
//             'notice' => null,
//         ]);

//         $adminCouriers = AdminCourier::query()->get();
//         Cache::put('admin_couriers', $adminCouriers);

//         return $this->sendApiResponse($adminCourier, 'Courier update successfully');
//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @param  \App\Models\AdminCourier  $adminCourier
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy(AdminCourier $adminCourier)
//     {
//         //
//     }
// }