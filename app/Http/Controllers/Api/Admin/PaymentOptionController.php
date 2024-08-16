<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentOption;
use App\Models\PaymentSubType;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class PaymentOptionController extends Controller
{
    use ApiResponses;

    public function getAllPaymentOptions()
    {
        $paymentOptions = PaymentOption::with(['paymentSubTypes'])->get();

        return $this->success('Payment option retrieved successfully.', $paymentOptions);
    }

    public function updatePaymentStatus(Request $request)
    {

        $data = $request->all();

        $mapping = [
            'cash_on_delivery' => 'Cash on delivery',
            'direct_bank_transfer' => 'direct bank transfer',
            'paynow' => 'paynow',
        ];

        foreach ($mapping as $key => $type) {
            if (isset($data[$key])) {
                if ($key === 'paynow' && is_array($data[$key])) {
                    $isActive = $data[$key]['status'] ? true : false;

                    $paymentOption = PaymentOption::where('type', $type)->first();
                    if ($paymentOption) {
                        $paymentOption->update(['is_active' => $isActive]);

                        if (isset($data[$key]['subtypes']) && is_array($data[$key]['subtypes'])) {
                            foreach ($data[$key]['subtypes'] as $subtypeName => $subtypeStatus) {
                                $paymentOption->paymentSubTypes()
                                    ->where('name', $subtypeName)
                                    ->update(['is_active' => $subtypeStatus ? true : false]);
                            }
                        }
                    }
                } else {
                    $isActive = $data[$key] ? true : false;

                    $paymentOption = PaymentOption::where('type', $type)->first();
                    if ($paymentOption) {
                        $paymentOption->update(['is_active' => $isActive]);
                    }
                }
            }
        }

        return $this->success('Payment status updated successfully.', $data);
    }

    public function updateDirectBankTransfer(Request $request)
    {
        $validatedData = $request->validate([
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
        ]);

        $description = json_encode([
            'account_number' => $validatedData['account_number'],
            'ifsc_code' => $validatedData['ifsc_code'],
        ]);

        $paymentOption = PaymentOption::where('type', 'direct bank transfer')->firstOrFail();
        $paymentOption->update(['description' => $description]);

        return $this->success('Direct Bank Transfer details updated successfully.', $paymentOption);
    }

    public function updateQRCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = $request->file('image');

        $title = 'qr_code';
        $orderNumber = time();
        $publicPath = public_path("assets/qr_codes/" . $title . "/" . $orderNumber);

        if (!file_exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }

        $imageName = $image->getClientOriginalName();
        $image->move($publicPath, $imageName);

        $imagePath = "assets/qr_codes/" . $title . "/" . $orderNumber . "/" . $imageName;

        $paymentSubType = PaymentSubType::where('name', 'QR Code')->firstOrFail();
        $paymentSubType->update(['description' => $imagePath]);

        return $this->success('QR Code image updated successfully.', $paymentSubType);
    }

    public function updateUENNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uen_number' => 'required|alpha_num|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paymentSubType = PaymentSubType::where('name', 'UEN')->firstOrFail();
        $paymentSubType->update(['description' => $request->uen_number]);

        return $this->success('UEN Number details updated successfully.', $paymentSubType);
    }

    public function updateMobileNumber(Request $request)
    {
        $validatedData = $request->validate([
            'mobile_number' => 'required|integer',
        ]);

        $paymentSubType = PaymentSubType::where('name', 'Mobile Number')->firstOrFail();
        $paymentSubType->update(['description' => (string) $validatedData['mobile_number']]);

        return $this->success('Mobile Number details updated successfully.', $paymentSubType);
    }


}
