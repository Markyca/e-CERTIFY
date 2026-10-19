{{-- Duty kagawad: signs in place of the Punong Barangay. Printed just below and to the left of the captain's name. --}}
@if(!empty($dutyKagawad))
    <div class="duty-kagawad" style="width: 240px; margin: 14px 0 0 -215px; text-align: center; position: relative; z-index: 6; line-height: 1.25;">
        <div style="height: 34px;"></div>
        <strong class="d-block text-uppercase" style="font-size: 12pt;">{{ $dutyKagawad['name'] }}</strong>
        <span style="font-size: 11pt;">{{ $dutyKagawad['title'] }}</span>
    </div>
@endif
