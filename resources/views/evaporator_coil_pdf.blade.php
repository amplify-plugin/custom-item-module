<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF DOC</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: Verdana, sans-serif;
            padding: 3%;
        }

        h2,
        h4 {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
        }

        .mt-2 {
            margin-top: 8px;
            padding-top: 8px;
        }

        .mb-2 {
            margin-bottom: 8px;
            padding-bottom: 8px;
        }

        .section-divider {
            border-bottom: 1px solid black;
            padding-bottom: 12px;
            margin-bottom: 8px;
        }

        .additional-notes-content {
            width: 100%;
            min-height: 100px;
            font-family: Verdana;
            border-color: rgba(128, 128, 128, 0.493);
            border-radius: 8px;
            margin-top: 8px;
            padding: 8px;
        }

        .product-image-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 48px;
        }

        .product-image {
            width: 100%;
            display: block;
            margin: 0 auto;
        }

        .label-container {
            position: absolute;
            text-align: center;
            align-content: center;
            width: 80px;
        }

        .label-marker {
            font-weight: bold;
            font-size: 12px;
        }

        .label-describer {
            font-size: 12px;
        }

        .number-label {
            background: green;
            width: 60%;
            color: white;
            text-align: center;
            border-radius: 8px;
            padding: 8px 0;
            margin: 4px auto 0;
        }

        .label-1-container {
            top: 6%;
            left: 24.5%;
        }

        .label-2-container {
            top: 3.25%;
            left: 46.5%;
        }

        .label-3-container {
            top: 10%;
            right: 24%;
        }

        .label-4-container {
            top: 16.5%;
            right: 14%;
        }

        .label-5-container {
            top: 29%;
            right: 22.5%;
        }

        .label-6-container {
            top: 26.5%;
            left: 29%;
        }

        .label-7-container {
            top: 18%;
            left: 12.5%;
        }
        .label-4-container .label-marker{
            padding-top: 18px;
        }
    </style>
</head>

<body>
    <table class="mt-2">
        <tr>
            <td style="width:20%"></td>
            <td>
                <h2 style="text-align: center">Quote Request for Custom Coil</h2>
            </td>
            <td style="width:20%; text-align: right">
                <img src="{{ config('amplify.cms.logo_path', public_path('frontend/rhsparts/images/coils/logo.png')) }}"
                    alt="Logo" style="width: 100%;">
            </td>
        </tr>
    </table>

    <h4 class="section-divider mt-2">From</h4>
    <table>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Contact Name:</td>
            <td>{{ $info['contact_name'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Method of Contact:</td>
            <td><a href="mailto:{{ $info['method_of_contact'] }}">{{ $info['method_of_contact'] }}</a></td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Company Name:</td>
            <td>{{ $info['company_name'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Address:</td>
            <td>{{ $info['address'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">City:</td>
            <td>{{ $info['city'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">State:</td>
            <td>{{ $info['state'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Zip:</td>
            <td>{{ $info['zipcode'] }}</td>
        </tr>
        <tr>
            <td style="width: 20%; padding-bottom: 8px;">Country:</td>
            <td>{{ $info['notes'] }}</td>
        </tr>
    </table>

    <table class="mt-2" style="page-break-after: always;">
        <tr>
            <td class="mb-2">
                <h4 class="section-divider mt-2">Measurements:</h4>
            </td>
        </tr>
        <tr>
            <td class="mb-2">
                A – {{ $info['measurement_one_display'] }},
                B – {{ $info['measurement_three_display'] }},
                C – {{ $info['measurement_five_display'] }},
                D – {{ $info['measurement_seven_display'] }},
                E – {{ $info['measurement_six_display'] }},
                F – {{ $info['measurement_four_display'] }},
                G – {{ $info['measurement_two_display'] }}
            </td>
        </tr>
        <tr>
            <td class="mt-2 mb-2">
                <h4 class="section-divider mt-2">Specifications:</h4>
            </td>
        </tr>
        <tr>
            <td class="mb-2">Coil is coated : {{ $info['coil_is_coated'] }}<br>
                Tube O/D : {{ $info['copper_tube'] }}"<br>
                Fins per inch : {{ $info['number_of_fins_per_inc'] }}<br>
                Tubes on coil end : {{ $info['number_of_tubes'] }}
            </td>
        </tr>
        <tr>
            <td class="mt-2 mb-2">Quantity required : {{ $info['qty'] }}</td>
        </tr>
        <tr>
            <td class="mt-2 mb-2">
                Additional Notes :
                <textarea placeholder="Additional Notes Content" class="additional-notes-content">{{ $info['notes'] }}</textarea>
            </td>
        </tr>
    </table>

    <table class="mt-2">
        <tr>
            <td class="mb-2">Image:</td>
        </tr>
        <tr>
            <td class="product-image-container">
                <img src="{{ public_path('frontend/rhsparts/images/coils/custom-coil.png') }}" alt="product"
                    class="product-image">

                <div class="label-container label-1-container">
                    <p class="label-marker">A</p>
                    <p class="label-describer">Finned Width</p>
                    <div class="number-label">{{ $info['measurement_one_display'] }}</div>
                </div>

                <div class="label-container label-2-container">
                    <p class="label-marker">G</p>
                    <p class="label-describer">Back Flange Length</p>
                    <div class="number-label">{{ $info['measurement_two_display'] }}</div>
                </div>

                <div class="label-container label-3-container">
                    <p class="label-marker">F</p>
                    <p class="label-describer">Front Flange Length</p>
                    <div class="number-label">{{ $info['measurement_four_display'] }}</div>
                </div>

                <div class="label-container label-4-container">
                    <p class="label-marker">E</p>
                    <p class="label-describer">Casing Height</p>
                    <div class="number-label">{{ $info['measurement_six_display'] }}</div>
                </div>

                <div class="label-container label-5-container">
                    <p class="label-marker">D</p>
                    <p class="label-describer">Casing Width</p>
                    <div class="number-label">{{ $info['measurement_seven_display'] }}</div>
                </div>

                <div class="label-container label-6-container">
                    <p class="label-marker">C</p>
                    <p class="label-describer">Finned Length</p>
                    <div class="number-label">{{ $info['measurement_five_display'] }}</div>
                </div>

                <div class="label-container label-7-container">
                    <p class="label-marker">B</p>
                    <p class="label-describer">Finned Height</p>
                    <div class="number-label">{{ $info['measurement_three_display'] }}</div>
                </div>

            </td>
        </tr>
    </table>

</body>

</html>
