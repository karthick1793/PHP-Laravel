<style>
    .invoice-number {
        font-weight: bold;
    }
    
    .header {
        font-size: 32px;
    }
    
    .small {
        font-size: 12px;
    }
    
    .bold {
        font-weight: bold;
    }
    
    th {
        color: #020202;
    }
    
    .amount__word {
        font-style: italic;
        font-weight: bold;
        text-align: left;
    }
    </style>
    
    <table border="0" cellpadding="1" cellspacing="0">
        <tr>
            <td colspan="1" cellpadding="0">
                <div style="padding: 0px; margin: 0px; width: 100%;">
                    <table border="0" cellpadding="8" cellspacing="0" style="border: 2px solid #adadad;width: 100%;">
                        <tr>
                            <th class="small"
                            style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:8%;">
                                SI No </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:10%;">
                                Name </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:14%;">
                                Quarters Name </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:13%;">
                                Room Number </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:15%;">
                                Date </th>
                            {{-- <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:10%;">
                                Time </th> --}}
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:12%;">
                                Morning Slot </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:12%;">
                                Evening Slot </th>
                            <th class="small"
                                style="line-height: 15px; text-align: center; border-bottom: 1px solid #adadad; border-left: 1px solid #adadad; width:16%;">
                                Milk Distributed </th>
                        </tr>
                        @foreach ($aaData as $key=>$row)
                        <tr>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['sl_no'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['name'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['quarter_name'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['room_number'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['date'] }}</td>
                            {{-- <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['time'] }}</td> --}}
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['moring_slot'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['evening_slot'] }}</td>
                            <td class="small"
                                style="line-height: 18px; border-top: 1px solid #adadad;border-left: 1px solid #adadad; border-bottom: 1px solid #adadad; text-align: center;">
                                {{ $row['litre'] }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </td>
        </tr>
    </table>
    