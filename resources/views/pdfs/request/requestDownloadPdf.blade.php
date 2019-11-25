@extends('pdfs.request.layout')
@section('title')
    @lang("models.request.download_pdf.service_request")
@endsection

@section('body')
    <table class="data_table" cellpadding="0" cellspacing="0">
        @foreach($datas as $key=>$data)
        <tbody>
        <tr>
            <td colspan="2" width="100%" class="no_border">
                <table width="100%" style="vertical-align:middle;margin-bottom: 15px;">
                    <tbody>
                    <tr>
                        <td class="table_header" valign="middle">
                            <span class="text_font" style="display: inline-block;vertical-align:top;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.captured_by') {{$data['request']->creator->email}} {{ now()->format('d.m.Y H:i') }}</span>
                            <span style="display: inline-block;height:25px;width:100%;"></span>
                            <span style="font-family: {{$pdf_font_family ?? 'Arial'}};">{{ $data['request']->service_request_format }}</span>
                            <br/>
                            <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.request_number'):</b>
                                {{ $data['request']->request_format }}
                            </p>
                            <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category'):</b>
                                @if (! empty($data['subCategory']))
                                    {{ !empty($data['category']['name']) ? __('models.request.category_list.' . $data['category']['name']) . ' > ' : ''  }}
                                    {{ __('models.request.sub_category.' . $data['subCategory']['name']) }}
                                @elseif (! empty($data['category']['name']))
                                    {{ __('models.request.category_list.' . $data['category']['name']) }}
                                @endif
                            </p>
                            <p style="margin:7px 0 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                <b style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.status.label') ({{ now()->format('d.m.Y, H:i') }}):</b>
                                @lang('models.request.status.'.\App\Models\Request::Status[$data['request']->status])
                            </p>
                        </td>
                        @if(!$blank_pdf)
                            <td style="text-align:right; vertical-align:top;font-family: {{$pdf_font_family ?? 'Arial'}};" class="table_header">
                                <div style="width: 180px;  float: right;">
                                        <img class="logo" src="{{public_path($logo)}}" style="width: 100%; float: right;"/>
                                </div>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="no_border" colspan="2">
                            <p style="margin:0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                <b>@lang('models.request.download_pdf.address'):</b>
                                {{ @$data['relation']->building->address->street }}
                                {{ @$data['relation']->building->address->house_num }}
                                {{ @$data['relation']->building->address->zip }}
                                {{ @$data['relation']->building->address->city }}
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="no_border" width="100%">
                <h2 style="margin-bottom:10px;margin-top:40px;font-size: 25px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.details')</h2>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="100%" class="no_border">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td width="100%" class="no_border">
                            <table class="inner_table" width="100%">
                                <tbody>
                                @if( (!empty($data['category']['capture_phase']) && $data['category']['capture_phase'] == 1)
                                || (!empty($data['subCategory']['capture_phase']) && $data['subCategory']['capture_phase']
                                == 1) || (!empty($data['category']['qualification']) && $data['category']['qualification'] == 1)
                                || (!empty($data['subCategory']['qualification']) && $data['subCategory']['qualification']
                                == 1) )
                                    <tr>
                                        @if( (!empty($data['category']['capture_phase']) && $data['category']['capture_phase'] == 1)
                                        || (!empty($data['subCategory']['capture_phase']) && $data['subCategory']['capture_phase']
                                        == 1) )

                                            <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    @lang('models.request.category_options.capture_phase'):
                                                </strong>
                                                @if(key_exists($data['request']->capture_phase,
                                                \App\Models\Request::CapturePhase))
                                                    @lang('models.request.capture_phase.' .
                                                    \App\Models\Request::CapturePhase[$data['request']->capture_phase])
                                                @endif
                                            </td>

                                        @endif


                                        @if( (!empty($data['category']['qualification']) && $data['category']['qualification'] == 1)
                                        || (!empty($data['subCategory']['qualification']) && $data['subCategory']['qualification']
                                        == 1) )
                                            <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    @lang('models.request.qualification.label'):
                                                </strong>
                                                @lang('models.request.qualification.'.\App\Models\Request::Qualification[$data['request']->qualification])
                                            </td>

                                        @endif
                                    </tr>
                                @endif

                                @if((isset($data['category']['component']) && $data['category']['component']==1) ||
+                                            (isset($data['subCategory']['component']) && $data['subCategory']['component']==1) ||
+                                            (!empty($data['category']['location']) && $data['category']['location'] == 1) ||
+                                            (!empty($data['subCategory']['location']) && $data['subCategory']['location'] == 1) ||
+                                            (!empty($data['category']['room']) && $data['category']['room'] == 1) ||
+                                            (!empty($data['subCategory']['room']) && $data['subCategory']['room'] == 1))
                                    <tr>
                                        @if((isset($data['category']['component']) && $data['category']['component']==1) ||
+                                                (isset($data['subCategory']['component']) && $data['subCategory']['component']==1))
                                            <td
                                                    class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};" @if (
                                                    !((((!empty($data['category']['location']) && $data['category']['location'] == 1) ||
                                                    (!empty($data['subCategory']['location']) && $data['subCategory']['location'] == 1))) ||
                                                    ((!empty($data['category']['room']) && $data['category']['room'] == 1) ||
                                                    (!empty($data['subCategory']['room']) && $data['subCategory']['room'] == 1)))
                                                )
                                            colspan="2"
                                                    @endif>
                                                <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category_options.component'):
                                                </strong>
                                                {{ $data['request']->component }}
                                            </td>
                                        @endif



                                        @if( (!empty($data['category']['location']) && $data['category']['location'] == 1) ||
                                        (!empty($data['subCategory']['location']) && $data['subCategory']['location'] == 1) )
                                            <td class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};"> @lang('models.request.category_options.range'):
                                                </strong>
                                                @if(key_exists($data['request']->location, \App\Models\Request::Location))
                                                    @lang('models.request.location.' .
                                                    \App\Models\Request::Location[$data['request']->location]);
                                                @endif
                                            </td>


                                        @elseif( (!empty($data['category']['room']) && $data['category']['room'] == 1) ||
                                        (!empty($data['subCategory']['room']) && $data['subCategory']['room'] == 1) )
                                            <td class="border_btm" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.category_options.room'):
                                                </strong>
                                                @if(key_exists($data['request']->room, \App\Models\Request::Room))
                                                    @lang('models.request.room.' .
                                                    \App\Models\Request::Room[$data['request']->room])
                                                @endif
                                            </td>

                                        @endif
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                <h4 style="margin-bottom:0;font-size: 14px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.title'):</h4>
                <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">{{ $data['request']->title }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                <h4 style="margin-bottom:0;font-size: 14px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.description'):</h4>
                <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">{!! $data['request']->description !!} </p>
            </td>
        </tr>
        <tr>
            <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                <h4 style="margin-bottom:0;margin-top:-10px;font-size: 20px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.contact_details')</h4>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="no_border" width="100%" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                <p style="display:block;width:100%;margin-top:5px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.download_pdf.contact_text')
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="100%" class="no_border">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td class="no_border" width="100%">
                            <table class="info_table" width="100%">
                                <tbody>
                                <tr>
                                    <td class="no_border" style="width:55%;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                        <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.name'): </strong>{{ $data['resident']->user->name }}
                                    </td>
                                    <td class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                        <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('general.email'): </strong>{{ $data['resident']->user->email }}
                                    </td>
                                </tr>
                                <tr>
                                    @if($data['resident']->mobile != null)
                                        <td class="border_btm" colspan="2" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                            <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.resident.mobile_phone'): </strong>{{ $data['resident']->mobile }}
                                        </td>
                                    @else
                                        <td class="border_btm" colspan="2" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                                            <strong style="font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.resident.private_phone'): </strong>{{ $data['resident']->private_phone }}
                                        </td>
                                    @endif
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="100%" height="20px" style="border:none;font-family: {{$pdf_font_family ?? 'Arial'}};"></td>
        </tr>
        <tr>
            <td @if(count($data['media'])==0) colspan='2' @else colspan='6' @endif width="100%" class="no_border" style="font-family: {{$pdf_font_family ?? 'Arial'}};">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td class="no_border" width="100%">
                            <table class="info_table" width="100%">
                                <tbody>
                                <tr>
                                    <td class="no_border" width="115%" style="margin:50px 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                            style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:85%;float:left;height:35px;">
                                                        @lang('models.request.download_pdf.customer_signature')
                                                    </span>
                                    </td>
                                    <td class="no_border" width="100%" style="margin:50px 0;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                            style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;border-bottom:2px dotted #888;padding-bottom:30px;display:inline-block;width:95%;float:left;height:35px;">
                                                        @lang('models.request.download_pdf.entrepreneur_signature')
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:100%;height:75px;border:none;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="no_border" width="100%" style="margin-top:50px;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                    <span
                                                            style="font-family: {{$pdf_font_family ?? 'Arial'}};margin-top:20px;padding-bottom:30px;display:inline-block;width:100%;">
                                                        @lang('models.request.download_pdf.blank_pdf')
                                                    </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        @foreach($data['media'] as $i => $m)
            @if($i%2==0)
                <tr>
                    <td colspan="2" width="100%" class="no_border">
                        <table width="100%">
                            <tbody>
                            @if($i==0)
                                <tr>
                                    <td class="no_border" width="100%">
                                        <h4 style="font-size: 20px;margin-buttom:45px;font-family: {{$pdf_font_family ?? 'Arial'}};">@lang('models.request.pictures_of')
                                        </h4>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="no_border" width="100%">
                                    @if($i==0)
                                        <span style="display: inline-block;height:25px;width:100%; font-family: {{$pdf_font_family ?? 'Arial'}};"></span>
                                    @endif
                                    <table class="info_table" width="100%">
                                        <tbody>
                                        <tr style="vertical-align: top;">
                                            @endif
                                            <td class="no_border" style="top:0;width:292px;vertical-align: top;font-family: {{$pdf_font_family ?? 'Arial'}};">
                                                <img class="pdf_attached" src="{{$m['file_path']}}"/>
                                            </td>
                                            @if($i%2==0)
                                                <td class="no_border">
                                                    <span class="sepearation"></span>
                                                </td>
                                            @endif

                                            @if($i%2==1)
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
        @endforeach
        @if (count($datas)-1!=$key)
            <tr>
                <td>
                    <div style="page-break-after: always!important;"></div>
                </td>
            </tr>
        @endif
        </tbody>
        @endforeach
    </table>
@endsection