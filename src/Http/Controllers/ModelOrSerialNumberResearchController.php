<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\System\Backend\Models\Event;
use Amplify\System\Factories\NotificationFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelOrSerialNumberResearchController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'manufacturer_name' => 'nullable',
            'model_number' => 'nullable',
            'serial_number' => 'nullable',
            'part_description' => 'nullable',
            'account_or_business_name' => 'required',
            'zip_code' => 'required',
            'method_of_contact' => [
                'required',
                function ($attribute, $value, $fail) {
                    $customAttribute = str_replace('_', ' ', $attribute);
                    $customAttribute = ucwords($customAttribute);
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $phonePattern = '/(01)[0-9]{9}/';
                        if (! preg_match($phonePattern, $value)) {
                            $fail('The '.$customAttribute.' must be a valid email address or phone number.');
                        }
                    }
                },
            ],
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $uploaded_file = null;
        $customer = ErpApi::getCustomerDetail();

        if ($request->hasFile('file')) {
            $path = Storage::putFile('model-research', $request->file('file'));
            $uploaded_file = Storage::path($path);
        }

        NotificationFactory::call([Event::MODEL_SERIAL_NUMBER_RESEARCH], [
            'research_data' => $data,
            'customer_info' => $customer,
            'uploaded_file' => $uploaded_file,
        ]);

        return back()->with('success', 'Successfully send email.');
    }
}
