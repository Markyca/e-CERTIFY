{{-- Duty kagawad: signs in place of the Punong Barangay (clearance, residency, indigency only).
     Printed BELOW the captain's block, at the LEFT, indented. It reuses the .signatory style so the font and size
     are exactly the same as the Punong Barangay's. --}}
@if(!empty($dutyKagawad))
    <div class="duty-kagawad signatory" style="float: none; clear: both; margin-top: 0; padding-left: 45px; text-align: left; position: relative; z-index: 6;">
        <div style="height: 30px;"></div>
        <div style="display: inline-block; text-align: center;">
            <strong class="d-block text-uppercase">{{ $dutyKagawad['name'] }}</strong>
            <span style="font-size: 12pt;">{{ $dutyKagawad['title'] }}</span>
        </div>
    </div>
@endif
