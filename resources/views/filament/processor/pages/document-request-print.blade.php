@php
    use Yajra\Address\Entities\Barangay;use Yajra\Address\Entities\City;use Yajra\Address\Entities\Province;$manifestPath = public_path('build/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);
    $cssFile = $manifest['resources/css/app.css']['css'][0] ?? null;
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @if($cssFile)
        <style>{!! file_get_contents(public_path('build/'.$cssFile)) !!}</style><title></title>
    @endif
</head>
<body>
<div>
    <input
        type="checkbox"
        id="rep"
        name="rep"
        :value="{{$record->has_representative}}"
        disabled>
    <label for="rep">Has Representative</label>
</div>

<div>
    <input
        type="checkbox"
        id="delivery"
        name="delivery"
        :value="{{$record->for_mailing}}"
        disabled>
    <label for="delivery">For Mailing</label>

</div>
<h1>Status:
    <span>
        {{$record->status->getLabel()}}
    </span>
</h1>

<h1>
    Processor:
    <span>
        @if($record->user_id !== null)
            {{$record->processor->name}}
        @else
            Not Assigned
        @endif
    </span>
</h1>

<table>
    <thead>
    <tr class="text-left">
        <th class="border p-1">Document Type</th>
        <th class="border p-1">Fee</th>
        <th class="border p-1">Quantity</th>
        <th class="border p-1">Total Fee</th>
    </tr>
    </thead>
    <tbody>
    @foreach($record->requestQuantity as $quantity)
        <tr>
            <td class="border p-1">{{$quantity->documentType->document_type}}</td>
            <td class="border p-1">{{$quantity->fee}}</td>
            <td class="border p-1">{{$quantity->quantity}}</td>
            <td class="border p-1">{{$quantity->total_fee}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div>
    <p>
        Requester Name:
        <span>
                @php
                    $name =
                    $record->requester_last_name
                    . ', ' .
                    $record->requester_first_name
                    . ' ' .
                    mb_substr($record->requester_middle_name, 0, 1)
                    . ' ' .
                    $record->requester_suffix . '.'
                @endphp
            {{$name}}
            </span>
    </p>

    <p>
        Contact Number:
        <span>
                {{$record->contact_number}}
            </span>
    </p>

    <p>
        Serial Number:
        <span>
                {{$record->cadet_serial_number}}
            </span>
    </p>

    <p>
        Original Class:
        <span>
                {{$record->original_class}}
            </span>
    </p>

    <p>
        Address:
        <span>
                @php
                    $address =
                    $record->street_address . ', '
                    . ', ' . Barangay::find($record->barangay)->name
                    . ', ' . City::where('city_id', $record->city)?->first()->name
                    . ', ' . Province::where('province_id', $record->region)?->first()->name
                @endphp

            {{$address}}
            </span>
    </p>
</div>

<div
    @class([
        'hidden' => ! $record->has_representative
    ])>
    <b>Representative Information</b>
    <p>
        Name:
        <span>
            @php
                $name =
                $record->representative_last_name
                . ', ' . $record->representative_first_name
                . ' ' . $record->representative_middle_name
                . ' ' . $record->representative_suffix
            @endphp
            {{$name}}
        </span>
    </p>

    <p>
        Contact Number:
        <span>
            {{$record->representative_contact}}
        </span>
    </p>

    <p>
        Office:
        <span>
            {{$record->representative_office}}
        </span>
    </p>
</div>

<div @class([
    'hidden' => ! $record->is_mailing
])
     class="mt-2">
    <b>Delivery Information</b>
    <p>

            Name:
        <span>
                @php
                    $name =
                    $record?->deliveryDetail?->recipient_last_name
                    . ', ' .
                    $record?->deliveryDetail?->recipient_first_name
                    . ' ' .
                    mb_substr($record?->deliveryDetail?->recipient_middle_name, 0, 1)
                    . ' ' .
                    $record?->deliveryDetail?->recipient_suffix . '.'
                @endphp
            {{$name}}
            </span>
    </p>

    <p>
        Contact Number:
        <span>
                {{$record?->deliveryDetail?->recipient_contact_number}}
            </span>
    </p>

    <p>
        Address:
        <span>
                @php
                    $address =
                    $record?->deliveryDetail?->street_address . ', '
                    . ', ' . Barangay::find($record?->deliveryDetail?->barangay)?->name
                    . ', ' . City::where('city_id', $record?->deliveryDetail?->city)?->first()?->name
                    . ', ' . Province::where('province_id', $record?->deliveryDetail?->region)?->first()?->name
                @endphp

            {{$address}}
            </span>
    </p>
</div>
</body>
</html>
